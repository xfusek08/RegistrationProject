<?php
class CourseRegistration extends Registration
{
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_REGISTRATION';
    $this->i_sPKColName = 'RGREG_PK';
    
    $this->i_aDBAliases['orderInTerm'] = 'RGREG_IORDER';
    $this->i_aDBAliases['eventPK'] = 'RGREG_FCOURSE';
    $this->i_aDBAliases['isNew'] = 'RGREG_ISNEW';
    $this->i_aDBAliases['created'] = 'RGREG_DTCREATED';
        
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    $this->AddColumn(DataType::String, 'rgreg_vclfirstname', true);
    $this->AddColumn(DataType::String, 'rgreg_vcllastname', true);
    $this->AddColumn(DataType::String, 'rgreg_vclemail', true);
    $this->AddColumn(DataType::String, 'rgreg_vcltelnumber');
    $this->AddColumn(DataType::String, 'rgreg_vcladdress');
    $this->AddColumn(DataType::String, 'rgreg_vtext');
    $this->AddColumn(DataType::Date, 'rgreg_dtCreated', true);
  }  
}
