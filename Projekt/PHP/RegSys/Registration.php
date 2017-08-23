<?php
/**
 * Třída představující jednu registraci na udalost a obsluhující práci s ní
 * 
 */
class Registration extends ResponsiveObject
{
  /** 
   * asociativni pole, ktere byde obsahovat mapu PRo sloupce ktere pouziva system k behu
   * Vsechny musi byt vyplnene
   * 
   * Potřebné aliasy:
   *    - eventPK       - number      urcuje vazbu na udalost
   *    - isNew         - bool        priznakuje novou registraci
   *    - created       - timestamp   Datum a cas vytvoreni
   * @var array
   */
  public $i_aDBAliases;
  
  public $i_bIsOpen = false;
  
  //Priznak jestli se ma pri uspesnem vytvoreni odeslat email klientovy
  public $i_bSendEmailToClient = true;
  
  //Priznak jestli se ma pri uspesnem vytvoreni odeslat email aminovi
  public $i_bSendEmailToAdmin = false;
  
  
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    // inicializace poli predka
    $this->i_bSubmited = false;
    $this->i_aActions = array();
    $this->i_oAlertStack = new AlertStack();
    
    // kontrola aliasu potomka
    if (gettype($this->i_aDBAliases) !== "array" || count($this->i_aDBAliases) !== 3)
    {
       $this->i_oAlertStack->Push('red', 
          'Error: Registration wrong aliasses definition "$this->i_aDBAliases" is invalidly defined.');
        Logging::WriteLog(LogType::Error, 
          'Registration()->__construct() - Registration wrong aliasses definition "$this->i_aDBAliases" is invalidly defined.');
    }
    else
    {
      foreach ($this->i_aDBAliases as $key => $value)
      {
        if ($value === '')
        {
          $this->i_oAlertStack->Push('red', 
            'Error: Registration wrong aliasses definition on key: "' . $key . '", value: "' . $value . '".');
          Logging::WriteLog(LogType::Error, 
            'Registration()->__construct() - Registration wrong aliasses definition on key: "' . $key . '", value: "' . $value . '".');
          break;
        }
        switch($key)
        {
          case 'orderInTerm':
          case 'eventPK':
            $this->AddColumn(DataType::Integer, $value, true);
            break;
          case 'isNew':
            $this->AddColumn(DataType::Bool, $value, true);
            break;
          case 'created':
            $this->AddColumn(DataType::Timestamp, $value, true)->SetValue(time());
            break;
        }
        $this->i_aDBAliases[$key] = strtoupper($value);
      }
    }
    // rozhodnuti o stavu    
    if ($this->i_oAlertStack->Count() > 0)
      $this->i_tState = ObjectState::osClose;
    else if ($a_iPK > 0)
      $this->i_tState = ObjectState::osOverview;
    else
      $this->i_tState = ObjectState::osNew;
    parent::__construct($a_iPK, $ExternTransaction);
    $this->i_bLoad_Success = $this->i_bLoad_Success && $this->i_oAlertStack->Count() === 0;    
  }
  
  // ---------------------------- PUBLIC ---------------------------------------
  
  public function SaveNew()
  {
    $this->i_bIsOpen = true;
    if (parent::SaveNew())
    {
      if ($this->i_bSendEmailToClient)
      {
        $v_oEmailCol = $this->GetColumnByName('rgreg_vclemail');
        if (!$this->SendRegistrationEmail(
            FROM_EMAIL, 
            $v_oEmailCol->GetValueAsString(),
            TO_CLIENT_EMAIL_SUBJECT,
            TO_CLIENT_EMAIL_TEMPLATE_PATH)
          )
        {
          if (!$this->DeleteFromDB(false))
          {
            $this->i_oAlertStack->Push('red', 'Chyba databáze');
            Logging::WriteLog(LogType::Error, 'Registration::SaveNew() - Failed to delete not valid registration.');
          }
          $v_oEmailCol->i_bValid = false;
          $v_oEmailCol->i_sInvalidDataMsg = 
            'Na zadanou adresu se nepodařilo odeslat potvrzovací e-mail.' . PHP_EOL .
            'Zadejte prosím platnou e-mailovou adresu.';
          return false;
        }
      }
      
      if ($this->i_bSendEmailToAdmin)
      {
        $this->SendRegistrationEmail(
          FROM_EMAIL, 
          ADMIN_ANNOUNCEMENT_EMAIL,
          TO_ADMIN_EMAIL_DEF_SUBJECT,
          TO_ADMIN_EMAIL_TEMPLATE_PATH);
      }
    }
    else 
      return false;
    return true;
  }
  
  public function SendRegistrationEmail($a_sFrom, $a_sTo, $a_sSubject, $a_sTemplatePath)
  {
    $v_sMessage = $this->LoadHTMLTemplate($a_sTemplatePath);
    if ($v_sMessage === false)
      return false;
    
    $v_Email = new MyMail();
    $v_Email->From = $a_sFrom;
    $v_Email->To = $a_sTo;
    $v_Email->Subject = $a_sSubject;
    $v_Email->Message = $v_sMessage;

    if (!$v_Email->Send())
      return false;
    return true;
  }
  
  public function LoadFromPostData($a_sPrefix = "")
  {
    $this->i_bSendEmailToClient = isset($_POST['sendEmailToClient']);
    $this->i_bSendEmailToAdmin = isset($_POST['sendEmailToAdmin']);
    return parent::LoadFromPostData($a_sPrefix);
  }
  
  public function ProcessAjax($a_sType)
  {
    if ($this->i_tState !== ObjectState::osClose)
    {
      switch ($a_sType)
      {
        case 'readnew':
          if ($this->GetColumnByName($this->i_aDBAliases['isNew'])->GetValue() == 1)
          {
            $this->GetColumnByName($this->i_aDBAliases['isNew'])->SetValue(0);
            if (!$this->SaveToDB(false, true))
              $this->i_oAlertStack->Push('red', 'Chyba při ověřování přečtené nové registrace.');
          }
          break;
        default:
          parent::ProcessAjax($a_sType);    
      }
    }
    else
      parent::ProcessAjax($a_sType);    
  }
  
  // ---------------------------- PROTECTED ------------------------------------
  protected function BuildNewHTML()
  {
    return $this->LoadHTMLTemplate(NEW_REGISTRATION_HTML);
  }
  
  protected function BuildEditHTML()
  {
    return $this->LoadHTMLTemplate(EDIT_REGISTRATION_HTML);
  }
  
  protected function BuildOverviewHTML()
  {
    return $this->LoadHTMLTemplate(OVERVIEW_REGISTRATION_HTML);
  }
  
  protected function GetResponseAddition()
  {
    $v_sRes = 
      '<primary_key>' . $this->i_iPK . '</primary_key>' . 
      '<isopendetail>' . BoolTo01Str($this->i_bIsOpen) . '</isopendetail>';
    $this->i_bIsOpen = false;
    return $v_sRes;        
  }
  protected function DefColumns() {}
  
  /**
   * Nacte obsah souboru sablony a podle definovanych pravidel nahradi klicove retezce za aktualne platne hodnoty
   *
   * Mozne konstanty, ktere budou substituovany:
   * -- generovane casove konstanty
   * {NOW_DATE} - aktualni datum d.m.Y
   * {NOW_TIME} - aktualni cas H:i
   * 
   * -- Formaty Casu vytvoreni
   * {CREATED_DATE} - datum vytvoreni, d.m.Y
   * {CREATED_TIME} - cas vytvoreni, H:i
   * 
   * -- obecne nazvy tabulek - pro post data ... 
   * {PK_COL} - $this->i_sPKColName
   * {CREATED_COL}        - $this->i_sFromColName
   * {EVENTPK_COL}        - $this->i_sStateColName
   * {ISNEW_COL}          - $this->i_sCapacityColName
   * 
   * -- dalsi informace o lekci
   * {sendEmailToClient}  - $this->$i_bSendEmailToClient
   * 
   * -- obecne informace o udalosti
   * {event_name}   - nazev udalosti
   * {event_time}   - cas zahajeni H:i, udalosti d.m.Y (w)
   * {event_desc}   - popis udalosti
   * 
   * -- automaticke doplneni hodnot
   * {`colname`_VAL} - nahradi za string hodnotu z kolekce sloupcu pro prislusny nazev sloupce `colname`
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
          'Registration::LoadHTMLTemplate(): cannot read template file: "' . $a_sTemplatePath . '"');
      return false;
    }    
    
    $html = str_replace('{NOW_DATE}', date('d.m.Y'), $html);
    $html = str_replace('{NOW_TIME}', date('H:i'), $html);

    $html = str_replace('{CREATED_DATE}', date('d.m.Y', $this->GetColumnByName($this->i_aDBAliases['created'])->GetValue()), $html);
    $html = str_replace('{CREATED_TIME}', date('H:i', $this->GetColumnByName($this->i_aDBAliases['created'])->GetValue()), $html);

    $html = str_replace('{ISNEW_COL}', $this->GetColumnByName($this->i_aDBAliases['isNew'])->GetValue(), $html);
    $html = str_replace('{sendEmailToClient}', ($this->i_bSendEmailToClient) ? 'checked' : '', $html);
    
    // informace o kruz
    $v_oEvent = EVENT_TYPE;
    $v_oEvent = new $v_oEvent($this->GetColumnByName($this->i_aDBAliases['eventPK'])->GetValue());
    $v_dtEventTime = $v_oEvent->GetColumnByName($v_oEvent->i_sFromColName)->GetValue();
    
    $html = str_replace('{event_name}', $v_oEvent->GetColumnByName($v_oEvent->i_sEventNameColName)->GetValue(), $html);
    $html = str_replace('{event_time}', date('d.m.Y, h:i' , $v_dtEventTime) . ' (' . GetCzechDayName(date('w', $v_dtEventTime)) . ')', $html);
    $html = str_replace('{event_desc}', $v_oEvent->GetColumnByName($v_oEvent->i_sEventdescColName)->GetValue(), $html);

    foreach ($this->i_aColumns as $column)
      $html = str_replace('{' . strtolower($column->i_sName) . '_val}', $column->GetValueAsString(), $html);      

    return $html;
  }
} 
