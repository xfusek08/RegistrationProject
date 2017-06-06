<?php
class Course extends Event
{
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_EVENT';
    $this->i_sPKColName = 'RGEV_PK';
    $this->i_sFromColName = 'RGEV_DTFROM';
    $this->i_sStateColName = 'RGEV_ISTATE';
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
        
  }
}
