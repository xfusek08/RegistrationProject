<?php
class Event extends DatabaseEntity
{
  public $i_sFromColName = '';
  public $i_sStateColName = '';
  
  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->AddColumn(DataType::Timestamp, $this->i_sFromColName, true);
    $this->AddColumn(DataType::Integer, $this->i_sStateColName, true);
    parent::__construct($a_iPK, $ExternTransaction);
  }  
  protected function DefColumns(){}
  
  public function GetDayOwerwiewHTML()
  {
   $html = '<div class="event" state="' . $this->GetColumnByName($this->i_sStateColName)->GetValue() . '">';
    $html .= '<div class="time">' . date('H:i', $this->GetColumnByName($this->i_sFromColName)->GetValue()) . '</div>';
    $html .= '<div class="content">' . $this->GetDayOwerwiewHTML_content() . '</div>';
    $html .= '</div>';
    return $html;
  }
  protected function GetDayOwerwiewHTML_content()
  {
    return 'Událost';
  }
}  
