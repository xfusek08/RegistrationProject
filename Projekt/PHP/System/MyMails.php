<?php
// emaily nejjednodussim spusobem

class MailState
{  
  const NotReady = 0;
  const Success = 1;
  const NotValidData = 2;
  const SendFailed = 3;
}

class MyMail
{
  public $To;
  public $From;
  public $Headers;
  public $Subject;
  public $Message;
  public $State = MailState::NotReady;
  
  public function Send()
  {
    if (!$this->Validate()) return false;
    $headers = 'From: ' . $this->From . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8';
    if (!@mail($this->To, $this->Subject, $this->Message, $headers))
    {
      $this->State = MailState::SendFailed;

      Logging::WriteLog(LogType::Error, 'MyMail->Send - Mail send failed.');
      Logging::WriteLog(LogType::Anouncement, 'MyMail->Send - mail data:' .
          ' $this->To: ' . $this->To .
          ' $this->From: ' . $this->From .
          ' $this->Message: ' . $this->Message .
          ' $this->Subject: ' . $this->Subject);
      return false; 
    }
    $this->State = MailState::Success;
    return true;    
  }
  public function Validate()
  {
    if (
      !filter_var($this->To, FILTER_VALIDATE_EMAIL) || 
      !filter_var($this->From, FILTER_VALIDATE_EMAIL) ||
      $this->Message == '' ||
      $this->Subject == '')
    {
      $this->State = MailState::NotValidData;
      Logging::WriteLog(LogType::Error, 'MyMail->Validate - invalid mail data');
      Logging::WriteLog(LogType::Anouncement, 'MyMail->Validate - mail data:' .
          ' $this->To: ' . $this->To .
          ' $this->From: ' . $this->From .
          ' $this->Message: ' . $this->Message .
          ' $this->Subject: ' . $this->Subject);
      return false;
    }
    return true;
  }
}
