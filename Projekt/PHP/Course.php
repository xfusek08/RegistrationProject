<?php
class Course extends Event
{
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_COURSE';
    $this->i_sPKColName = 'RGCOUR_PK';
    $this->i_sFromColName = 'RGCOUR_DTFROM';
    $this->i_sStateColName = 'RGCOUR_ISTATE';
    $this->i_sCapacityColName = 'RGCOUR_ICAPACITY';
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    
  }  
}
