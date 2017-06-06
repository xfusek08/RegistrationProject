<?php
class CalendarEvent extends DatabaseEntity
{
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_EVENT';
    $this->i_sPKColName = 'RGEV_PK';
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    $this->AddColumn(DataType::Date, 'rgev_dtfrom', true);
    $this->AddColumn(DataType::Integer, 'rgev_istate', true);
  }  
}  
