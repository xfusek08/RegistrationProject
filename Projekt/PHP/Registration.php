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
   *    - orderInTerm   - number      urcuje poradi v udalosti
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
    if (gettype($this->i_aDBAliases) !== "array" || count($this->i_aDBAliases) !== 4)
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
            $this->AddColumn(DataType::Timestamp, $value, true);
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
  
  protected function GetResponseAddition() {}
  protected function DefColumns() {}
  
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

    
    foreach ($this->i_aColumns as $column)
    {
      $html = str_replace('{' . strtolower($column->i_sName) . '_val}', $column->GetValueAsString(), $html);      
    }
    
    return $html;
  }
} 
