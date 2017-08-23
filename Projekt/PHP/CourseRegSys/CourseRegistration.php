<?php
class CourseRegistration extends Registration
{
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_REGISTRATION';
    $this->i_sPKColName = 'RGREG_PK';
    
    //$this->i_aDBAliases['orderInTerm'] = 'RGREG_IORDER';
    $this->i_aDBAliases['eventPK'] = 'RGREG_FCOURSE';
    $this->i_aDBAliases['isNew'] = 'RGREG_ISNEW';
    $this->i_aDBAliases['created'] = 'RGREG_DTCREATED';
        
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    $this->AddColumn(DataType::String, 'rgreg_vclfirstname', true);
    $this->AddColumn(DataType::String, 'rgreg_vcllastname', true);
    $this->AddColumn(DataType::Email, 'rgreg_vclemail', true);
    $this->AddColumn(DataType::String, 'rgreg_vcltelnumber');
    $this->AddColumn(DataType::String, 'rgreg_vcladdress');
    $this->AddColumn(DataType::Integer, 'rgreg_flanguage');
    $this->AddColumn(DataType::String, 'rgreg_vtext');
    $this->AddColumn(DataType::Date, 'rgreg_dtCreated', true);
  }  
  
  /*
   * Upresneni pro jazyk kurzu
   * 
   * {laguage_text} - jazyk kurzu
   */
  protected function LoadHTMLTemplate($a_sTemplatePath)
  {
    $html = parent::LoadHTMLTemplate($a_sTemplatePath);
    
    if ($html === false) 
      return false;
    
    $v_oEvent = EVENT_TYPE;
    $v_oEvent = new $v_oEvent($this->GetColumnByName($this->i_aDBAliases['eventPK'])->GetValue());
    $v_oLanguage = new Language($v_oEvent->GetColumnByName('rgcour_flanguage')->GetValue());
    $html = str_replace('{laguage_text}', $v_oLanguage->GetColumnByName('rglng_text')->GetValue(), $html);
    
    return $html;
  }
}
