<?php
class Language extends DatabaseEntity
{
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_LANGUAGE';
    $this->i_sPKColName = 'RGLNG_PK';
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    $this->AddColumn(DataType::String, 'rglng_ident', true);
    $this->AddColumn(DataType::String, 'rglng_text', true);
    $this->AddColumn(DataType::String, 'rglng_desc');
  }
}