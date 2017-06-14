<?php
/**
 * Vycet stavu, ktere event muze nabyvat
 */
class EventStates
{
  const esNew = 0;
  const esOverview = 1;
  const esEditing = 2;
  const esClose = 3;
}

/**
 * Třída představující jednu událost a obsluhující práci s ní
 * Chová se jako stavový automat. komunikkuje se svou stranou v javascriptu pomocí ajax todazů
 * S každým dotazem vrátí xml obsahující akci, která se má provést
 */
class Event extends DatabaseEntity
{
  // Nazev databazoveho sloupce, ktery obsahuje udaj o case zahajeni udalosti
  public $i_sFromColName = '';
  // Nazev databazoveho sloupce, ktery obsahuje ciselny udaj o stavu udalosti
  public $i_sStateColName = '';
  // Nazev databazoveho sloupce, ktery obsahuje ciselny udaj o maxipalnim poctu registraci na udalost
  public $i_sCapacityColName = '';
  
  // Aktualni stav objektu udalosti
  // typ: EventType
  public $State;
  
  // Zasobnik upozorneni, ktere se budou vypisovat na obrazovku s kazdou odpovedi klientovi
  // typ: AlertStack
  public $i_oAlertStack;
  
  // ---------------------------- PUBLIC -------------------------------

  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    // inicializace objektu
    $this->i_oAlertStack = new AlertStack();
    
    // kontrola spravneho definovani
    if ($this->i_sFromColName !== '')
      $this->AddColumn(DataType::Timestamp, $this->i_sFromColName, true);
    else
      $this->i_oAlertStack->Push('red', 'Error: Event form-time table field is not specified.');

    if ($this->i_sStateColName !== '')
      $this->AddColumn(DataType::Integer, $this->i_sStateColName, true, "0");
    else
      $this->i_oAlertStack->Push('red', 'Error: Event state table field is not specified.');
    
    if ($this->i_sCapacityColName !== '')
      $this->AddColumn(DataType::Integer, $this->i_sCapacityColName, true, "0");
    else
      $this->i_oAlertStack->Push('red', 'Error: Event capacity table field is not specified.');

    // rozhodnuti o stavu    
    if ($this->i_oAlertStack->Count() > 0)
      $this->State = EventStates::esClose;
    else if ($a_iPK > 0)
      $this->State = EventStates::esOverview;
    else
      $this->State = EventStates::esNew;

    
    parent::__construct($a_iPK, $ExternTransaction);
  }  

  /**
   * Zpracuje ajax dotaz podle aktualniho stavu a prejde do stavu nasledujiciho
   */
  public function ProcessAjax()
  {
    if (!isset($_POST['EventAjaxType'])) return;
    $reqtype = $_POST['EventAjaxType'];
    $invalidRequestType = false;
    
    if ($reqtype == 'close')
    {
      $this->State = EventStates::esClose;
      return;
    }
    
    switch ($this->State)
    {
      case EventStates::esNew:
        if ($reqtype == 'submitdata')
        {
          if ($this->SaveNewEvent())
            $this->State = EventStates::esOverview;
        }
        else  
          $invalidRequestType = true;
        break;
      case EventStates::esOverview:
        if ($reqtype == 'edit')
          $this->State = EventStates::esEditing;
        else  
          $invalidRequestType = true;
        break;
      case EventStates::esEditing:
        if ($reqtype == 'edit')
        {
          if ($this->SaveEditEvent())
            $this->State = EventStates::esOverview;
        }
        else  
          $invalidRequestType = true;
        break;
    }
    if ($invalidRequestType)
      $this->i_oAlertStack->Push('red', 'Invalid event request type.');    
  }
  
  /**
   * Vraci ridici xml pro javascript
   * 
   * esNew - Vraci formular pro vytvoreni nove udalosti
   * esOverview - Vraci html s prehledem udalosti vcetne prehledu registraci
   * esEditing - Vrati formular existujici udalosti s editaci
   * esClose - Vrati prikaz pro zavreni a zniceni udalosti
   * 
   * Popis vystupniho XML
   * 
   *  <respxml>
   *    <event_response>
   *      <alerts> ... </alerts>            - automaticky zpracovana upozorneni
   *      <actions>                         - seznam akci, ktere ma ridici jednotka provedst
   *        <action>CloseEvent</action>       - zavre formular a posle dotas ke zniceni objektu
   *        <action>ShowHtml</action>         - zobrazi predane html do '.adm-day-conn' a vrati 
   *                                            jQuery objekt onoho html
   *        <action>InitNewForm</action>      - vytvori vychozi obsluzne metody pro formular nove udalosti
   *                                            nad objektem vracenym z ShowHtml
   *      </actions>
   *      <html> ... </html>                - obsah toho co se ma zobrazit pomoci ShowHtml
   *                                          obycejne nacteno z nejake sablony
   *    </event_response>
   *  </respxml>
   */
  public function GetEventResponse()
  {
    $v_sResponse = '<respxml><event_response>';
    
    $v_sResponse .= $this->i_oAlertStack->GetXML();
    
    switch ($this->State)
    {
      case EventStates::esClose:
        $v_sResponse .= '<actions><action>CloseEvent</action><actions>';
        break;
      case EventStates::esNew:
        $v_sResponse .= 
          '<actions>'.
            '<action>ShowHtml</action>'.
            '<action>InitNewForm</action>'.
          '<actions>';
        $v_sResponse .= '<html>' . $this->LoadHTMLTemplate(NEW_EVENT_HTML) . '</html>';
        
        break;
    }
    $v_sResponse .= '</event_response></respxml>';    
    
    return $v_sResponse;
  }
  
  
  public function SaveNewEvent(){}
  public function SaveEditEvent(){}
  
  // ---------------------------- PROTECTED -------------------------------
  
  protected function DefColumns() {}
  
  /**
   * Nacte obsah souboru sablony a podle definovanych pravidel nahradi klicove retezce za aktualne platne hodnoty
   *
   * Mozne konstanty, ktere budou substituovany:
   * 
   * -- datumy
   * {FROM_DATE} - datum ve formatu d.m.y brane z polozky Event::i_sFromColName
   * {FROM_TIME} - cas ve formatu H:i brane z polozky Event::i_sFromColName
   * {FROM_DAY} - nazev dnu vis 'GetCzechDayName(date('w', Event::i_sFromColName))
   * 
   * @param string $a_sTemplatePath - cesta k sablone
   */
  protected function LoadHTMLTemplate($a_sTemplatePath)
  {
    $html = file_get_contents($a_sTemplatePath);    
    if ($html === false)
    {
      $this->i_oAlertStack->Push('red', 'Error: cannot read template file.');      
      Logging::WriteLog(LogType::Error, 
          'Event::LoadHTMLTemplate(): cannot read template file: "' . $a_sTemplatePath . '"');
      return;
    }    

    $html = str_replace('{FROM_DATE}', date('d.m.y', $this->GetColumnByName($this->i_sFromColName)->GetValue()), $html);
    $html = str_replace('{FROM_TIME}', date('H:i', $this->GetColumnByName($this->i_sFromColName)->GetValue()), $html);
    $html = str_replace('{FROM_DAY}', GetCzechDayName(date('w', $this->GetColumnByName($this->i_sFromColName)->GetValue())), $html);
    return $html;
  }
 
  
  //-------------------------------- stare funkce ------------------------------
  
  /*
  
  
  public function GetDayOwerwiewHTML()
  {
   $html = '<div class="event" state="' . $this->GetState() . '">';
    $html .= '<div class="time">' . date('H:i', $this->GetColumnByName($this->i_sFromColName)->GetValue()) . '</div>';
    $html .= '<div class="content">' . $this->GetDayOwerwiewHTML_content() . '</div>';
    $html .= '</div>';
    return $html;
  }
  protected function GetDayOwerwiewHTML_content()
  {
    return 'Událost';
  }
 
  public function GetEventHTML()
  {
    $html = '<div class="conndetail eventform">';
    
    
    $v_bNew = false;

    if ($this->i_iPK == 0)
    {
      $v_bNew = true;
      $this->i_bEditing = true;
    }
    
    if ($this->i_bEditing)
      $html .= '<form method="post">';
    
    // nadpis
    $html .= '<div class="conndetail-caption">';
    
    $v_dtActDateTime = $this->GetColumnByName($this->i_sFromColName)->GetValue();
    if ($v_bNew)
      $html .= ucfirst(NOVA_UDALOST) . ': ' . date('d.m.Y', $v_dtActDateTime) . ' (' . GetCzechDayName(date('w', $v_dtActDateTime)) . ')';
    else if ($this->i_bEditing)
      $html .= ucfirst(UDALOST) . ': ' . date('d.m.Y', $v_dtActDateTime) . ' (' . GetCzechDayName(date('w', $v_dtActDateTime)) . ')';
    else
      $html .= ucfirst(UDALOST) . ': ' . date('H:i, d.m.Y', $v_dtActDateTime) . ' (' . GetCzechDayName(date('w', $v_dtActDateTime)) . ')';
    
    $html .= '</div>';
    
    // formular
    $html .= '<div class="conndetail-inhtml">';
    if ($this->i_bEditing)
    {
      $html .= $this->GetInHTML();           
    }
    $html .= '</div>';
    $html .= '<div class="footer">'.
        '<input type="submit" isajax="true" value="Potrvdit" name="c_submit">'.
        '<input type="submit" isajax="true"value="Zrušit" name="c_storno"></div>';
    
    if ($this->i_bEditing)
      $html .= '</form>';
    $html .= '</div>';
    return $html;
  }
  protected function GetInHTML()
  {
    return 
    '<table>'.
          '<tr><td>Čas zahájení</td>'.
          '<td>'.
            '<div class="timeinput">'.
              '<input size=1 class="timeinput" name="time_from" type="text" value="' . date("H:i", $this->GetColumnByName($this->i_sFromColName)->GetValue()) . '"/>'.
              '<button class="seltimebt"><img src="../img/clock.png"></button>'.
            '</div>'.
          '</td></tr>'.
          '<tr><td>Kapacita:</td>'.
          '<td><input size=1 class="numberinput" name="capacity" type="text" value="' . $this->GetColumnByName($this->i_sCapacityColName)->GetValueAsString() . '"/></td></tr>'.
        '</table>';   
  }
  */
}  
