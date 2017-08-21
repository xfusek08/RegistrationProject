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
    $this->i_sEventNameColName = 'RGCOUR_VNAME';
    $this->i_sEventdescColName  = 'RGCOUR_VDESC';
    parent::__construct($a_iPK, $ExternTransaction);
  }
  protected function DefColumns()
  {
    $this->AddColumn(DataType::Integer, 'rgcour_flanguage', true);
  } 
  public function LoadFromPostData($a_sPrefix = "")
  {
    $v_sLangPK = '';
    $v_oLangCol = $this->GetColumnByName('rgcour_flanguage');
    $v_bIsOK = false;
    if (isset($_POST['rgcour_flanguage']))
    {
      $v_sLangPK = $_POST['rgcour_flanguage'];
      unset($_POST['rgcour_flanguage']);
      if (intval($v_sLangPK) !== false)
      {
        $Val = 0;
        $v_bIsOK = MyDatabase::GetOneValue($Val, 'select 1 from rg_language where rglng_pk = ?', intval($v_sLangPK));
        $v_bIsOK = $v_bIsOK && $Val == 1; 
      }
    }
    if ($v_bIsOK)
    {
      $v_oLangCol->SetValueFromString($v_sLangPK);
    }
    else
    {
      $v_oLangCol->i_bValid = false;
      $v_oLangCol->i_sInvalidDataMsg = 'Jazyk není platný.';
    }
    return parent::LoadFromPostData($a_sPrefix);
  }
  protected function LoadHTMLTemplate($a_sTemplatePath)
  {
    $v_oLanguage = new Language($this->GetColumnByName('rgcour_flanguage')->GetValue());
    $html = parent::LoadHTMLTemplate($a_sTemplatePath);
    $html = 
      str_replace(
        '{COURSE_LANG_SEL_OPTIONS}', 
        $v_oLanguage->GetLangSelectOptions($this->GetColumnByName('rgcour_flanguage')->GetValue()), 
        $html
      );
    $html = str_replace('{LANGUAGE_TEXT}', $v_oLanguage->GetColumnByName('rglng_text')->GetValue(), $html);
    
    while ($v_oLanguage->i_oAlertStack->Count() > 0)
    {
      $alert = $v_oLanguage->i_oAlertStack->Pop();
      $this->i_oAlertStack->Push($alert->i_sColor, $alert->i_sMessage);
    }
    return $html;
  }
  protected function GetDayOwerwiewHTML_content()
  {
    $v_iCapacity = $this->GetColumnByName($this->i_sCapacityColName)->GetValue();
    $v_oLanguage = new Language($this->GetColumnByName('rgcour_flanguage')->GetValue());
    $v_sHtml = 
      '<div>' . $this->GetColumnByName($this->i_sEventNameColName)->GetValueAsString() . '</div>'.
      '<table>' .
        '<tr>'.
          '<td>jazyk:</td><td>'. $v_oLanguage->GetColumnByName('rglng_text')->GetValue() . '</td>'.
        '</tr><tr>'.
          '<td>obsazenost:</td><td>' . $this->GetCapacityStatus() . '</td>'.
        '</tr>'.
      '</table>';
    
    return $v_sHtml;
  }
  
}
