<?php
class CourseSettingPage extends ResponsivePage
{
  public $i_oLanguage;
  public $i_bSubmited;
  public function __construct()
  {
    $this->i_bSubmited = false;
    $this->i_oLanguage = new Language();
    parent::__construct();
  }
  
  public function GetResponse()
  {
    $v_sResponse = '<page_response reload="' . BoolTo01Str($this->i_bReload) . '">';
    $v_sResponse .= $this->i_oAlertStack->GetXML();
    
    if ($this->i_bSubmited)
      if (!$this->i_oLanguage->IsDataValid())
        $v_sResponse .= $this->i_oLanguage->GetInvalidDataXML(); 
    
    $v_sResponse .= '<actions>';
    if ($this->i_bClosed)
      $v_sResponse .= '<action>Close</action>';
    else
      $v_sResponse .= '<action>ShowHtml</action>';      
    $v_sResponse .= '</actions>';
    $v_sResponse .= '<showhtml>' . $this->LoadHtml() . '</showhtml>';
    
    $v_sResponse .= '</page_response>';
    $this->i_bSubmited = false;
    return $v_sResponse;
  }
  public function ProcessAjax()
  {
    $this->i_oLanguage = new Language();
    $v_sAjaxType = '';
    if (isset($_POST['RespPageAjaxType']))
      $v_sAjaxType = $_POST['RespPageAjaxType'];
    else
      return;
    
    if ($v_sAjaxType === 'new_language')    
    {
      $this->i_bSubmited = true;
      $this->i_oLanguage->LoadFromPostData();
      if ($this->i_oLanguage->IsDataValid())
      {
        if (!$this->i_oLanguage->SaveToDB(false))
          $this->i_oAlertStack->Push('red', 'Jazyk se nepodařilo uložit.');    
        else
          $this->i_oAlertStack->Push('green', 'Uloženo.');    
      }
      else
        $this->i_oAlertStack->Push('red', 'Formulář obsahuje nevalidní data.');    
    }
    else if ($v_sAjaxType === 'delete_language' && isset($_POST['pk']))
    {
      $v_iPK = intval($_POST['pk']);
      if ($v_iPK !== false)
      {
        $this->i_oLanguage = new Language($v_iPK);
        if ($this->i_oLanguage->i_bLoad_Success)
        {
          if ($this->i_oLanguage->DeleteFromDB(false))
            $this->i_oAlertStack->Push('green', 'Smazáno.');    
          else
            $this->i_oAlertStack->Push('red', 'Jazyk se nepodařilo smazat.');    
        }
      }
    }
    else if ($v_sAjaxType === 'edit_language' && isset($_POST['pk']))
    {
      $v_iPK = intval($_POST['pk']);
      if ($v_iPK !== false)
      {
        $this->i_oLanguage = new Language($v_iPK);
        $this->i_oLanguage->LoadFromPostData('edit');
        if ($this->i_oLanguage->IsDataValid())
        {
          if (!$this->i_oLanguage->SaveToDB(false))
            $this->i_oAlertStack->Push('red', 'Jazyk se nepodařilo uložit.');    
          else
            $this->i_oAlertStack->Push('green', 'Uloženo.');    
        }
        else
          $this->i_oAlertStack->Push('red', 'Formulář obsahuje nevalidní data.');    
      }
    }
  }
  protected function LoadHtml()
  {
    return $this->LoadHTMLTemplate(SETTING_HTML);
  }
  
  protected function LoadHTMLTemplate($a_sTemplatePath)
  {
    $html = file_get_contents($a_sTemplatePath);    
    if ($html === false)
    {
      $this->i_oAlertStack->Push('red', 'Error: cannot read template file.');      
      Logging::WriteLog(LogType::Error, 
          'CourseSettingPage::LoadHTMLTemplate(): cannot read template file: "' . $a_sTemplatePath . '"');
      return;
    }    
    $html = str_replace('{LANGUAGE_XML_DATA}', $this->i_oLanguage->GetLanguageXMLData(), $html);
     return $html;
  }
}
