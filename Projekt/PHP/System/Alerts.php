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
    return $this->i_aAlerts[] = new Alert($a_sColor, $a_sMessage); 
  }
  public function Pop()
  {
    return array_pop($this->i_aAlerts);      
  }
  public function Clean()
  {
    $this->i_aAlerts = array();
  }
  public function GetXML()
  {
    $xml = '<alerts>';
    while (count($this->i_aAlerts) > 0)
      $xml .= $this->Pop()->GetXML();
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
    return '<alert><color>' . $this->i_sColor . '</color><message>' . $this->i_sMessage . '</message></alert>';
  }
}