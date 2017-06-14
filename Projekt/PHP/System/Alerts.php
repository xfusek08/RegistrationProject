<?php
class AlertStack
{
  private $i_aAlerts;
  public function __construct()
  {
    $this->i_aAlerts = array();
  }
  public function Count()
  {
    return count($this->i_aAlerts);
  }
  public function Push($a_sColor, $a_sMessage)
  {
    return array_push($this->i_aAlerts, new Alert($a_sColor, $a_sMessage)); 
  }
  public function Pop($a_sColor, $a_sMessage)
  {
    return array_pop($this->i_aAlerts);      
  }
  public function GetXML()
  {
    $xml = '<alerts>';
    for ($i = 0; $i < count($this->i_aAlerts) - 1; $i++)
      $this->Pop()->GetXML();
    $xml .= '</alerts>';
    return $xml;
  }
}
class Alert
{
  public $i_sMessage;
  public $i_sColor;  
  public function __construct($a_sColor, $a_sMessage)
  {
    $this->i_sColor = $a_sColor;
    $this->i_sMessage = $a_sMessage;
    Logging::WriteLog(LogType::Anouncement, 'Alert created: ' . $this->i_sColor . '; ' . $this->i_sMessage);
  }
  public function GetXML()
  {
    return '<alert><color>' . $a_oAlertObj->i_sColor . '</color><message>' . $a_oAlertObj->i_sMessage . '</message></alert>';
  }
}