<?php
/**
 * Třída představující jednu událost a obsluhující práci s ní
 * Chová se jako stavový automat. komunikkuje se svou stranou v javascriptu pomocí ajax todazů
 * S každým dotazem vrátí xml obsahující akci, která se má provést
 */
class Event extends ResponsiveObject
{
  // Nazev databazoveho sloupce, ktery obsahuje udaj o case zahajeni udalosti
  public $i_sFromColName = '';
  // Nazev databazoveho sloupce, ktery obsahuje ciselny udaj o stavu udalosti
  public $i_sStateColName = '';
  // Nazev databazoveho sloupce, ktery obsahuje ciselny udaj o maxipalnim poctu registraci na udalost
  public $i_sCapacityColName = '';
  
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
      $this->i_tState = ObjectState::osClose;
    else if ($a_iPK > 0)
      $this->i_tState = ObjectState::osOverview;
    else
      $this->i_tState = ObjectState::osNew;

    
    parent::__construct($a_iPK, $ExternTransaction);
  }  

  /**
   * Zkonkretneni zpracovani ajaxu, pokud se ajax tyka prace s registracemi, tak rizeni nechavame registracim
   */
  public function ProcessAjax($a_sType)
  {
    if ($this->i_tState !== ObjectState::osClose)
    {
      switch ($a_sType)
      {
        case 'newregistration':
          
          break;
        case 'deletegistration':
          
          break;
        case 'RegistrationAjax':
          // todo vyhledat registraci a zavolat na ni prislusny ajax
          
          break;
        default:
          parent::ProcessAjax();    
      }
    }
    else
      parent::ProcessAjax();    
  }
  
  // ---------------------------- PROTECTED -------------------------------
  
  protected function BuildNewHTML()
  {
    return $this->LoadHTMLTemplate(NEW_EVENT_HTML);
  }
  
  protected function BuildEditHTML()
  {
    return $this->LoadHTMLTemplate(EDIT_EVENT_HTML);
  }
  
  protected function BuildOverviewHTML()
  {
    return $this->LoadHTMLTemplate(OVERVIEW_EVENT_HTML);
  }
  
  protected function GetResponseAddition() {}
  
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
} 
