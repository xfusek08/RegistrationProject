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
  
  // Nazev databazoveho sloupce, ktery obsahuje nazev udalosti
  public $i_sEventNameColName = '';

  // Nazev databazoveho sloupce, ktery obsahuje popis udalosti
  public $i_sEventdescColName = '';
  
  // Pole registraci na otevrene udalosti
  public $i_aRegistrations;
  
  // ---------------------------- PUBLIC -------------------------------

  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_bSubmited = false;
    $this->i_aActions = array();
    $this->i_aRegistrations = array();
    // inicializace objektu
    $this->i_oAlertStack = new AlertStack();
    $this->i_bReload = false;
    
    // kontrola spravneho definovani
    if ($this->i_sFromColName !== '')
      $this->AddColumn(DataType::Timestamp, $this->i_sFromColName, true);
    else
    {
      $this->i_oAlertStack->Push('red', 'Error: Event form-time table field is not specified.');
      Logging::WriteLog(LogType::Error, 'Event()->__construct() - Event form-time table field is not specified.');
    }

    if ($this->i_sStateColName !== '')
      $this->AddColumn(DataType::Integer, $this->i_sStateColName, true, "0");
    else
    {
      $this->i_oAlertStack->Push('red', 'Error: Event state table field is not specified.');
      Logging::WriteLog(LogType::Error, 'Event()->__construct() - Event state table field is not specified.');
    }
    
    if ($this->i_sCapacityColName !== '')
      $this->AddColumn(DataType::Integer, $this->i_sCapacityColName, true, "0");
    else
    {
      $this->i_oAlertStack->Push('red', 'Error: Event capacity table field is not specified.');
      Logging::WriteLog(LogType::Error, 'Event()->__construct() - Event capacity table field is not specified.');
    }

    if ($this->i_sEventNameColName !== '')
      $this->AddColumn(DataType::String, $this->i_sEventNameColName, true);
    else
    {
      $this->i_oAlertStack->Push('red', 'Error: Event name table field is not specified.');
      Logging::WriteLog(LogType::Error, 'Event()->__construct() - Event name table field is not specified.');
    }

    if ($this->i_sEventdescColName !== '')
      $this->AddColumn(DataType::String, $this->i_sEventdescColName);
    else
    {
      $this->i_oAlertStack->Push('red', 'Error: Event desc table field is not specified.');
      Logging::WriteLog(LogType::Error, 'Event()->__construct() - Event desc table field is not specified.');
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
    
    if (!$this->i_bLoad_Success && $this->i_iPK > 0)
    {
      Logging::WriteLog(LogType::Error, 'Event.__construct(' . $a_iPK . ') - failed to load event.');
      return;
    }
    $this->i_bLoad_Success = $this->LoadRegistrations($ExternTransaction);
    if (!$this->i_bLoad_Success)
      Logging::WriteLog(LogType::Error, 'Event.__construct(' . $a_iPK . ') - failed to load registrations.');    
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
        case 'newregistration': $this->AddRegistration(0); break;
        case 'deletegistration':
        case 'RegistrationAjax':
          $v_iRegPK = false;
          if (isset($_POST['RegistrationPK']))
            $v_iRegPK = intval($_POST['RegistrationPK']);
          if ($v_iRegPK === false)
          {
            $this->i_oAlertStack.Push('red', 'Chyba: neplatné číslo rezervace.');
            Logging::WriteLog(LogType::Error, 
              'Event->ProcessAjax: not valid registration PK: "' . $_POST['RegistrationPK'] . '"');
            break;
          }
          
          if ($a_sType == 'deletegistration')
          {
            if (!$this->DeleteRegistration($v_iRegPK))
              $this->i_oAlertStack.Push('red', 'Chyba: Nepodařílo se vymazat registraci.');
            else 
              $this->i_oAlertStack->Push('green', 'Smazáno.');              
          }
          else if (isset($_POST['RegistrationAxajType']))
          {
            $a_iIndex = 0;
            $v_oReg = $this->FindRegistration($v_iRegPK, $a_iIndex);
            if ($v_oReg === null)
            {
              $this->i_oAlertStack.Push('red', 'Chyba: neplatné číslo rezervace.');
              Logging::WriteLog(LogType::Error, 
                'Event->ProcessAjax: registration not found Pk: "' . $v_iRegPK . '"');
              break;
            }

            $v_oReg->ProcessAjax($_POST['RegistrationAxajType']);
            if($v_oReg->i_tState == ObjectState::osClose)
              $this->RemoveRegistration($v_iRegPK);            
          }   
          else
          {
            $this->i_oAlertStack.Push('red', 'Chyba: neplatný typ dotazu.');
            Logging::WriteLog(LogType::Error, 
              'Event->ProcessAjax: invalid AjaxType not defined');
          }
          break;
        default:
          parent::ProcessAjax($a_sType);    
      }
    }
    else
      parent::ProcessAjax($a_sType);    
  }
  
  public function GetState()
  {
    
    // TODO zkontrolovat pořet registraci + kapacitu a pokud je kapacina naplnena vratit full
    
    $v_iCount = count($this->i_aRegistrations);
    $v_iCapacity = $this->GetColumnByName($this->i_sCapacityColName)->GetValue();

    $v_iState = $this->GetColumnByName($this->i_sStateColName)->GetValue();
    if ($v_iCount == $v_iCapacity && $v_iCapacity > 0)
      $v_iState = 2;

    switch ($v_iState)
    {
      case 1: return 'hidden';
      case 2: return 'full';
      default: return 'open';
    }
  }
  
  public function GetDayOwerwiewHTML()
  {
    $html = '<div class="event_owerwiew">';
    $html .= '<div class="time">' . date('H:i', $this->GetColumnByName($this->i_sFromColName)->GetValue()) . '</div>';
    $html .= '<div class="content">' . $this->GetDayOwerwiewHTML_content() . '</div>';
    $html .= '</div>';
    return $html;
  }
  
  public function LoadFromPostData($a_sPrefix = "")
  {
    $v_oTimeCol = $this->GetColumnByName($this->i_sFromColName);
    $v_sTimestring = '';
    
    if (isset($_POST[strtolower($this->i_sFromColName)]))
    {
      $v_sTimestring = $_POST[strtolower($this->i_sFromColName)];
      unset($_POST[strtolower($this->i_sFromColName)]);
    }
    
    if ($v_sTimestring == '')
    {
      $v_oTimeCol->i_bValid = false;
      $v_oTimeCol->i_sInvalidDataMsg = 'Položka musí být vyplněna.';
    }
    else
    {
      $v_dtNewDateTime = AddTimeToDate($v_oTimeCol->GetValue(), $v_sTimestring);
      if ($v_dtNewDateTime === false)
      {
        $v_oTimeCol->i_bValid = false;
        $v_oTimeCol->i_sInvalidDataMsg = 'Položka není platný časový údaj.';
      }
      else
      {
        $v_oTimeCol->SetValue($v_dtNewDateTime);
      }
    }
      
    return parent::LoadFromPostData($a_sPrefix) + (($v_oTimeCol->i_bValid) ? 1 : 0);
  }
  
  public function DeleteFromDB($ExternalTrans = false)
  {
    $v_bSucces = true;
    try
    {
      if (!$ExternalTrans)
        MyDatabase::$PDO->beginTransaction();
      
      for ($i = 0; $i < count($this->i_aRegistrations) && $v_bSucces; $i++)
        $v_bSucces = $this->i_aRegistrations[$i]->DeleteFromDB(true);
      
      if ($v_bSucces)
        $v_bSucces = parent::DeleteFromDB(true);
      
      if ($v_bSucces)
      {
        if (!$ExternalTrans)
          MyDatabase::$PDO->commit();
      } 
      else
      {
        Logging::WriteLog(LogType::Error, 'Event->DeleteFromDB() - Failed to delete event.');
        $succes = false;
        if (!$ExternalTrans)
        {
          Logging::WriteLog(LogType::Anouncement, "RollBack");
          MyDatabase::$PDO->rollBack();
        }
      }
    }
    catch (PDOException $e)
    {
      $succes = false;
      if (!$ExternalTrans)
      {
        Logging::WriteLog(LogType::Error, $e->getMessage());
        Logging::WriteLog(LogType::Anouncement, "RollBack");
        MyDatabase::$PDO->rollBack();
      }
    }
    return $v_bSucces;
  }

  public function GetCapacityStatus()
  {
    $v_iCount = count($this->i_aRegistrations);
    $v_iCapacity = $this->GetColumnByName($this->i_sCapacityColName)->GetValue();
    $v_sRes = $v_iCount . '/';
    if ($v_iCapacity == 0)
      $v_sRes .= '-';
    else
    {
      $v_sRes .= $v_iCapacity;
      if ($v_iCapacity <= $v_iCount)
        $v_sRes .= ' - plno';
    }
    return $v_sRes;
  }
  // ---------------------------- PROTECTED -------------------------------
  
  protected function GetDayOwerwiewHTML_content()
  {
    $v_iCapacity = $this->GetColumnByName($this->i_sCapacityColName)->GetValue();
    $v_sHtml = 
      '<div>' . $this->GetColumnByName($this->i_sEventNameColName)->GetValueAsString() . '</div>'.
      '<table>' .
        '<tr>'.
          '<td>přihlášeno: '. count($this->i_aRegistrations) . '</td>'.
          '<td>kapacita: ' . (($v_iCapacity > 0) ? $v_iCapacity : 'neomezeno') . '</td>'.
        '</tr>'.
      '</table>';
    
    return $v_sHtml;
  }
  
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
  
  /** 
   * Pridame akce na ktere lze vybrat a odeslat zpet
   */
  protected function GetResponseAddition() 
  {
    $res = '<primary_key>' . $this->i_iPK . '</primary_key>';
    $res .= '<registrations>';
    for ($i = 0; $i < count($this->i_aRegistrations); $i++)
      $res .= '<registration>' . $this->i_aRegistrations[$i]->GetResponse() . '</registration>';
    $res .= '</registrations>';
    return $res;
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
   * -- datumy
   * {FROM_DATE} - datum ve formatu d.m.Y brane z polozky Event::i_sFromColName
   * {FROM_TIME} - cas ve formatu H:i brane z polozky Event::i_sFromColName
   * {FROM_DAY} - nazev dnu vis 'GetCzechDayName(date('w', Event::i_sFromColName))
   * 
   * -- obecne nazvy tabulek - pro post data ... 
   * {PK_COL} - $this->i_sPKColName
   * {FROM_COL} - $this->i_sFromColName
   * {STATE_COL} - $this->i_sStateColName
   * {CAPACITY_COL} - $this->i_sCapacityColName
   * {NAME_COL} - $this->i_sEventNameColName
   * {DESC_COL} - $this->i_sEventdescColName
   * 
   * -- automaticke doplneni hodnot
   * {`colname`_VAL} - nahradi za string hodnotu z kolekce sloupcu pro prislusny nazev sloupce `colname`
   * 
   * -- dalsi generovane constanty
   * {REGISTRATION_COUNT} - počet registrací
   * {CAPACITY_STATUS} - status o kapacitě podle funckce GetCapacityStatus()
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

    $html = str_replace('{NOW_DATE}', date('d.m.Y'), $html);
    $html = str_replace('{NOW_TIME}', date('H:i'), $html);

    $html = str_replace('{FROM_DATE}', date('d.m.Y', $this->GetColumnByName($this->i_sFromColName)->GetValue()), $html);
    $html = str_replace('{FROM_TIME}', date('H:i', $this->GetColumnByName($this->i_sFromColName)->GetValue()), $html);
    $html = str_replace('{FROM_DAY}', GetCzechDayName(date('w', $this->GetColumnByName($this->i_sFromColName)->GetValue())), $html);
    
    $html = str_replace('{PK_COL}', strtolower($this->i_sPKColName), $html);
    $html = str_replace('{FROM_COL}', strtolower($this->i_sFromColName), $html);
    $html = str_replace('{STATE_COL}', strtolower($this->i_sStateColName), $html);
    $html = str_replace('{CAPACITY_COL}', strtolower($this->i_sCapacityColName), $html);
    $html = str_replace('{NAME_COL}', strtolower($this->i_sEventNameColName), $html);
    $html = str_replace('{DESC_COL}', strtolower($this->i_sEventdescColName), $html);
    
    $html = str_replace('{REGISTRATION_COUNT}', strval(count($this->i_aRegistrations)), $html);
    $html = str_replace('{CAPACITY_STATUS}', $this->GetCapacityStatus(), $html);
    
    foreach ($this->i_aColumns as $column)
    {
      $html = str_replace('{' . strtolower($column->i_sName) . '_val}', $column->GetValueAsString(), $html);      
    }
    
    return $html;
  }
  
  
  // --------------------------- REGISTRATION MANAGEMENT ----------------------------------
  protected function LoadRegistrations($ExternTransaction)
  {
    if ($this->i_iPK === 0) // zadne registrace, tudis nacteni v poradku
      return true;
    
    // 1. najdeme vsechny pk registraci na konkreti udalost
    $v_oRegPrototype = REGISTRATION_TYPE;
    $v_oRegPrototype = new $v_oRegPrototype();

    $SQL = 
      'select'.
      '    ' . $v_oRegPrototype->i_sPKColName . ',' .
      '    ' . $v_oRegPrototype->i_aDBAliases['created'] .
      '  from'.
      '    ' . $v_oRegPrototype->i_sTableName .
      '  where'.
      '    ' . $v_oRegPrototype->i_aDBAliases['eventPK'] . ' = ?'.
      '  order by ' . $v_oRegPrototype->i_aDBAliases['created'] . ' desc';
    $fields = null;
    if (!MyDatabase::RunQuery($fields, $SQL, $ExternTransaction, $this->i_iPK))
    {
      Logging::WriteLog(LogType::Error, 'Event->LoadRegistrations() - failed to load Registrations PKs');
      return false;
    }
    
    // 2. naplnime registracemi pole
    for ($i = 0; $i < count($fields); $i++)
    {
      $v_oRegPrototype = REGISTRATION_TYPE;
      $v_oRegPrototype = new $v_oRegPrototype(intval($fields[$i][0]));
      if ($v_oRegPrototype->i_bLoad_Success)
        $this->i_aRegistrations[] = $v_oRegPrototype;
      else
      {
        Logging::WriteLog(LogType::Error, 'Event->LoadRegistrations() - failed to load Registration: pk: "' . $fields[$i][0] . '"');
        return false;                
      }
    }
    return true;
  }
  
  protected function AddRegistration($a_iPK)
  {
    if ($a_iPK === 0)
      foreach ($this->i_aRegistrations as $reg)
        if ($reg->i_tState === ObjectState::osNew) 
          return $reg;
        
    if ($this->GetState() == 'full')
    {
      $this->AddAction('CantAddNew');
      return null;
    }
    
    $v_oRegPrototype = REGISTRATION_TYPE;
    $v_oRegPrototype = new $v_oRegPrototype($a_iPK);
    
    if ($a_iPK === 0)
      $v_oRegPrototype->GetColumnByName($v_oRegPrototype->i_aDBAliases['eventPK'])->SetValue($this->i_iPK);
    
    array_unshift($this->i_aRegistrations, $v_oRegPrototype);
    return $v_oRegPrototype;
  }
  
  protected function DeleteRegistration($a_iPK)
  {
    $a_iIndex = 0;
    $v_sReg = $this->FindRegistration($a_iPK, $a_iIndex);
    if ($v_sReg == null)
    {
      Logging::WriteLog(LogType::Error, 
        'Event->DeleteRegistration(): failed to find regeistration Pk: "' . $a_iPK . '"');
      return false;
    }
    if (!$v_sReg->DeleteFromDB(false))
    {
      Logging::WriteLog(LogType::Error, 
        'Event->ProcessAjax: failed to delete registration from db Pk: "' . $a_iPK . '"');
      return false;
    }
    $this->RemoveRegistration($a_iPK);    
    return true;
  }
  
  protected function RemoveRegistration($a_iPK)
  {
    $v_Index = 0;
    $this->FindRegistration($a_iPK, $v_Index);
    unset($this->i_aRegistrations[$v_Index]);
    $this->i_aRegistrations = array_values($this->i_aRegistrations);
  }
  
  protected function FindRegistration($a_iPK, &$a_iIndex)
  {
    for ($a_iIndex = 0; $a_iIndex < count($this->i_aRegistrations); $a_iIndex++)
     if ($this->i_aRegistrations[$a_iIndex]->i_iPK === $a_iPK) 
       return $this->i_aRegistrations[$a_iIndex];
    return null;
  }
} 
