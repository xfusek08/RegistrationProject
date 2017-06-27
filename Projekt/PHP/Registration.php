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
  
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    // inicializace poli predka
    $this->i_bSubmited = false;
    $this->i_aActionStack = array();
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
    return '<primary_key>' . $this->i_iPK . '</primary_key>';
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
   * {CREATED_COL}  - $this->i_sFromColName
   * {EVENTPK_COL}  - $this->i_sStateColName
   * {ISNEW_COL}    - $this->i_sCapacityColName
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
          'Event::LoadHTMLTemplate(): cannot read template file: "' . $a_sTemplatePath . '"');
      return;
    }    
    
    $html = str_replace('{NOW_DATE}', date('d.m.Y'), $html);
    $html = str_replace('{NOW_TIME}', date('H:i'), $html);

    $html = str_replace('{CREATED_DATE}', date('d.m.Y', $this->GetColumnByName($this->i_aDBAliases['created'])->GetValue()), $html);
    $html = str_replace('{CREATED_TIME}', date('H:i', $this->GetColumnByName($this->i_aDBAliases['created'])->GetValue()), $html);
    
    foreach ($this->i_aColumns as $column)
    {
      $html = str_replace('{' . strtolower($column->i_sName) . '_val}', $column->GetValueAsString(), $html);      
    }
    
    return $html;
  }
} 
