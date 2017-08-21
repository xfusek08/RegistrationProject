<?php
class Language extends DatabaseEntity
{
  public $i_oAlertStack;
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_sTableName = 'RG_LANGUAGE';
    $this->i_sPKColName = 'RGLNG_PK';
    $this->i_oAlertStack = new AlertStack();
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    $this->AddColumn(DataType::String, 'rglng_ident', true);
    $this->AddColumn(DataType::String, 'rglng_text', true);
    $this->AddColumn(DataType::String, 'rglng_desc');
  }
  
  public function GetLangSelectOptions($a_iSelPK = 0)
  {
    $SQL = 
      'select'.
      '    rglng_pk,'.
      '    rglng_text'.
      '  from'.
      '    rg_language'.
      '  order by rglng_text';
    
    $fields = null;
    if (!MyDatabase::RunQuery($fields, $SQL, false))
    {
      $this->i_oAlertStack->Push('red', 'Chyba při hledání jazyků.');
      Logging::WriteLog(LogType::Error, 'Language->GetLangSelectOptions(): Error while selecting languages.');
      return '';
    }
    
    $v_sResult = '';
    for ($i = 0; $i < count($fields); $i++)
    {
      $v_sResult .= 
        '<option' .
          (($fields[$i]['RGLNG_PK'] == $a_iSelPK) ? ' selected' : '') . 
          ' value=' . $fields[$i]['RGLNG_PK'] . '>' . $fields[$i]['RGLNG_TEXT'] . 
        '</option>' . PHP_EOL;
    }
    return $v_sResult;
  }
  public function GetLanguageXMLData()
  {
    $SQL = 
      'select'.
      '    rglng_pk,'.
      '    rglng_text,'.
      '    rglng_desc,'.
      '    rglng_ident,'.
      '    (case when'.
      '        exists (select 1 from rg_course where rgcour_flanguage = rglng_pk) or'.
      '        exists (select 1 from rg_registration where rgreg_flanguage = rglng_pk)'.
      '      then 0 else 1 end) as deletable'.
      '  from'.
      '    rg_language'.
      '  order by'.
      '    rglng_text';
    
    $fields = null;
    if (!MyDatabase::RunQuery($fields, $SQL, false))
    {
      $this->i_oAlertStack->Push('red', 'Chyba při hledání jazyků.');
      Logging::WriteLog(LogType::Error, 'Language->GetLangSelectOptions(): Error while selecting languages.');
      return '';
    }
    
    $v_sResult = '<languages>';
    for ($i = 0; $i < count($fields); $i++)
    {
      $v_sResult .= 
        '<language'.
          ' pk="' . $fields[$i]['RGLNG_PK'] . '"' . 
          ' name="' . $fields[$i]['RGLNG_TEXT'] . '"' . 
          ' shortcut="' . $fields[$i]['RGLNG_IDENT'] . '"' . 
          ' desc="' . $fields[$i]['RGLNG_DESC'] . '"' . 
          ' deletable="' . $fields[$i]['DELETABLE'] . '"' . 
        '/>';
    }
    $v_sResult .= '</languages>';
    return $v_sResult;
  }
}