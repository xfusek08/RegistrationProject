<?php
abstract class DatabaseEntity
{
  public $i_iPK;
  public $i_sTableName;
  public $i_sPKColName;
  
  public $i_aColumns = array();
  public $i_bLoad_Success;
  public $i_eSaveToDBResult;
  
  private $i_iColCount;

  public function __construct($a_iPK = 0, $ExternTransaction = false)
  {
    $this->i_iPK = $a_iPK; 
    
    $this->i_bLoad_Success = false;    
    $this->i_eSaveToDBResult = SaveToDBResult::None;
    $this->DefColumns();    
    $this->i_iColCount = count($this->i_aColumns);    
    
    if ($this->i_iPK > 0)
      $this->i_bLoad_Success = $this->InitFromDB($ExternTransaction);
  }
  
  public function GetSelectSQL()
  {
    $SQL = 'select ';
    for ($i = 0; $i < $this->i_iColCount; $i++)
    {
      $SQL .= $this->i_aColumns[$i]->GetSelectSQL();
      if ($i + 1 < $this->i_iColCount)
       $SQL .= ', ';  
    }
      
    $SQL .= ' from '. $this->i_sTableName;
    
    return $SQL;    
  }
  public function InitFromDB($ExternTransaction)
  {
    if ($this->i_iPK < 1 || $this->i_sTableName == '' || $this->i_sPKColName == '')
      return false;

    $SQL = $this->GetSelectSQL() . ' where ' . $this->i_sPKColName . ' = ?';
    
    $fields = null;
    if(!MyDatabase::RunQuery($fields, $SQL, $ExternTransaction, $this->i_iPK))
      return false;
    
    if (count($fields) == 0 || count($fields) > 1)
      return false;
    
    for ($i = 0; $i < $this->i_iColCount; $i++)
    {
      if (!$this->i_aColumns[$i]->SetValueFromString(strval($fields[0][$this->i_aColumns[$i]->i_sName])))
      {
        Logging::WriteLog(LogType::Error, 
            'Database entity initialization error on index: ' . $i .' entity name: ' . $this->i_aColumns[$i]->i_sName);
        return false;
      }
    }
    return true;
  }    
  public function SaveToDB($ExternalTrans)
  {    
    $this->i_eSaveToDBResult = SaveToDBResult::OK;
    if (!$this->IsDataValid())
    {
      $this->i_eSaveToDBResult = SaveToDBResult::InvalidData;
      return false;
    }   
    $v_aCols = array();
    
    for ($i = 0; $i < $this->i_iColCount; $i++)
    {
      if (!is_a($this->i_aColumns[$i], 'SQLColumn')) // pokud se nejedna o vypocteny spoupec
        $v_aCols[] = $this->i_aColumns[$i];
    }

    $params = array();
    for ($i = 0; $i < count($v_aCols); $i++)
      $params[] = $v_aCols[$i]->GetValueAsString(false);

    $SQL = '';
    if ($this->i_iPK > 0) // update
    {
      $SQL = 'update ' . $this->i_sTableName . ' set ';
      for ($i = 0; $i < count($v_aCols); $i++)
      {
        $SQL .= $v_aCols[$i]->i_sName . ' = ?';
        if ($i + 1 < count($v_aCols))
          $SQL .= ', ';  
      }
      $SQL .= ' where ' . $this->i_sPKColName . ' = ?'; 
      
      $params[] = $this->i_iPK;
    }
    else // insert
    {
      $SQL = 'insert into ' . $this->i_sTableName . ' (';
      for ($i = 0; $i < count($v_aCols); $i++)
      {
        $SQL .= $v_aCols[$i]->i_sName;
        if ($i + 1 < count($v_aCols))
          $SQL .= ', ';  
      }
      $SQL .= ') values(';
      for ($i = 0; $i < count($v_aCols); $i++)
      {
        $SQL .= '?';
        if ($i + 1 < count($v_aCols))
          $SQL .= ', ';  
      }
      $SQL .= ') returning ' . $this->i_sPKColName . ';';      
    }

    $fields = null;

    if (!MyDatabase::RunQuery($fields, $SQL, $ExternalTrans, $params))
    {
      $this->i_eSaveToDBResult = SaveToDBResult::Error;
      return false;
    }
    
    if ($this->i_iPK == 0)
    {
      if (count($fields) == 0)
      {
        $this->i_eSaveToDBResult = SaveToDBResult::Error;
        return false;
      }
      
      $this->i_iPK = intval($fields[0][0]);
    }
      
    return true;      
  }
  public function DeleteFromDB($ExternalTrans)
  {
    if ($this->i_iPK < 1) 
      return true;
    
    $SQL = 'delete from ' . $this->i_sTableName . ' where ' . $this->i_sPKColName . ' = ?';
    $fields = null;
    if (!MyDatabase::RunQuery($fields, $SQL, $ExternalTrans, $this->i_iPK))
      return false;
    return true;
  }
  public function GetAsXML($Formated = true)
  {
    $res = '<' . strtolower($this->i_sTableName) . '>';
    $res .= '<pk>' . $this->i_iPK . '</pk>';
    for ($i = 0; $i < $this->i_iColCount; $i++)
    {
      $res .= 
        '<' . strtolower($this->i_aColumns[$i]->i_sName) . '>' . 
          $this->i_aColumns[$i]->GetValueAsString($Formated) . 
        '</' . strtolower($this->i_aColumns[$i]->i_sName) . '>';      
    }
    $res .= '</' . strtolower($this->i_sTableName) . '>';
    return $res;
  }
  public function LoadFromPostData($a_sPrefix = '')
  {
    $counter = 0;
    for ($i = 0; $i < $this->i_iColCount; $i++)
    {
      if (isset($_POST[$a_sPrefix . strtolower($this->i_aColumns[$i]->i_sName)]))
      {
        $this->i_aColumns[$i]->SetValueFromString($_POST[$a_sPrefix . strtolower($this->i_aColumns[$i]->i_sName)]);        
        $counter++;
      }
      else if ($this->i_aColumns[$i]->i_tDataType == DataType::Bool)
        $this->i_aColumns[$i]->SetValue(false);        
    }
    return $counter;
  }
  public function GetColumnByName($a_sColName)
  {
    $a_sColName = strtoupper($a_sColName);
    for ($i = 0; $i < count($this->i_aColumns); $i++)
      if ($this->i_aColumns[$i]->i_sName === $a_sColName)
        return $this->i_aColumns[$i];
      
    return null;
  }
  public function IsDataValid()
  {
    for ($i = 0; $i < count($this->i_aColumns); $i++)    
      if (!$this->i_aColumns[$i]->i_bValid) 
        return false;
    
    return true;
  }
  public function GetInvalidDataXML($a_sPrefix = '')
  {
    $res = '<invaliddata>';
    for ($i = 0; $i < count($this->i_aColumns); $i++)
      if (!$this->i_aColumns[$i]->i_bValid) 
      {
        $res .= 
        '<input name="' . $a_sPrefix . strtolower($this->i_aColumns[$i]->i_sName) . '" '.
          'message="' . $this->i_aColumns[$i]->GetInvalidDataMessage() . '" />';                
      }    
    $res .= '</invaliddata>';
    return $res;
  }
  public function BuildHTMLForm($edit)
  {
    $html = '<div class="DBEntForm"';
    for ($i = 0; $i < count($this->i_aColumns); $i++)
    {
      if ($this->i_aColumns[$i]->i_bShow && $this->i_aColumns[$i]->i_bIsHiddenAttr)
        $html .= ' ' . $this->i_aColumns[$i]->i_sShowName . '="' . $this->i_aColumns[$i]->GetValueAsString() . '" ';
    }
    $html .= '>'; 
    
    if ($edit)
      $html .= '<form class="" method="post">';
    $html .= '<table>';
    for ($i = 0; $i < count($this->i_aColumns); $i++)
    {
      if ($this->i_aColumns[$i]->i_bShow && !$this->i_aColumns[$i]->i_bIsHiddenAttr)
      {
        if ($edit)
          $html .= '<tr><td>' . $this->i_aColumns[$i]->i_sShowName . ': </td><td>' . $this->i_aColumns[$i]->BuildHTMLInput() . '</td></tr>';
        else
          $html .= '<tr><td>' . $this->i_aColumns[$i]->i_sShowName . ': </td><td>' . $this->i_aColumns[$i]->GetValueAsString() . '</td></tr>';
      }
    }
    $html .= '</table>';
    $html .= '<div class="footer"><input type="submit" value="Potrvdit" name="c_submit"><input type="submit" value="Zrušit" name="c_storno"></div>';
    if ($edit)
      $html .= '</form>';
    $html .= '</div>';
    return $html;
  }
  protected function AddColumn($a_tDataType, $a_sName, $a_bNotNull = false, $a_sDefValueString = '')
  {
    if ($this->GetColumnByName($a_sName) !== null)
        return $this->GetColumnByName($a_sName);
    $col = new DBEntColumn($a_tDataType, $a_sName, $a_bNotNull, $a_sDefValueString);
    $this->i_aColumns[] = $col;
    return $col;    
  }
  protected function AddSQLColumn($a_tDataType, $a_sName, $a_sSQL)
  {
    if ($this->GetColumnByName($a_sName) !== null)
        return $this->GetColumnByName($a_sName);
    $col = new SQLColumn($a_tDataType, $a_sName, $a_sSQL);
    $this->i_aColumns[] = $col;
    return $col;
  }
  protected abstract function DefColumns();
}
class DBEntColumn
{
  private $i_xValue;
  public $i_bNotNull;  
  public $i_sName;
  public $i_bValid;
  public $i_sInvalidDataMsg;
  public $i_bUnformated;  
  public $i_tDataType;

  public $i_bShow;
  public $i_sShowName;
  public $i_sDescription;
  public $i_bIsHiddenAttr;
  public $i_iMaxLenght;
  
  public function __construct($a_tDataType, $a_sName, $a_bNotNull = false, $a_sDefValueString = '')
  {
    $this->i_sInvalidDataMsg = '';
    $this->i_bUnformated = false;
    $this->i_bValid = false;
    $this->i_tDataType = $a_tDataType;    
    $this->i_bNotNull = $a_bNotNull;
    $this->i_sName = strtoupper($a_sName);
    $this->SetValueFromString($a_sDefValueString);
    
    $this->i_bShow = false;
    $this->i_sShowName = '';
    $this->i_sDescription = '';
    $this->i_bIsHiddenAttr = false;
    $this->i_iMaxLenght = -1;
  }
  public function GetValue()
  {
    return $this->i_xValue;
  }
  public function GetInvalidDataMessage()
  {
    return $this->i_sInvalidDataMsg;        
  }
  public function GetSelectSQL()
  {
    return $this->i_sName;    
  }
  public function SetValue($a_xValue)
  {
    $this->i_bValid = true;

    if ($this->i_tDataType == DataType::Bool)
      $this->i_xValue = BoolTo01($a_xValue);   
    else
      $this->i_xValue = $a_xValue; 

    if ($a_xValue === '')
      $this->i_xValue = null;
    
    if ($this->i_xValue === null)      
    {
      if ($this->i_bNotNull)
      {
        $this->i_bValid = false;
        $this->i_sInvalidDataMsg = 'Položka je povinná.';
      }
      return $this->i_bValid;
    }
    switch ($this->i_tDataType)
    {
      case DataType::String:    
        $this->i_bValid = is_string($this->i_xValue); 
        break;
      case DataType::Integer:   
        $this->i_bValid = is_int($this->i_xValue);              
        break;
      case DataType::Float:     
        $this->i_bValid = is_float($this->i_xValue);              
        break;
      case DataType::Date:    
      case DataType::DateTrnc:
      case DataType::Timestamp: 
        $this->i_bValid = IsTimestamp($this->i_xValue);        
        break;
      case DataType::Bool:
        $this->i_bValid = $this->i_xValue === 1 || $this->i_xValue === 0;
        break;
    }   
    
    if (!$this->i_bValid)
    {
      $this->i_sInvalidDataMsg = 'Chyba ve validaci';
      Logging::WriteLog(LogType::Anouncement, 
          'DBEntColumn.SetValue() - invalid got datatype. name="' . $this->i_sName . '" '.
          'value="' . $this->i_xValue . '" NotNull="' . BoolTo01Str($this->i_bNotNull) . '"');
    }

    return $this->i_bValid;
  }
  public function SetValueFromString($a_sValue)
  {
    if ($this->i_tDataType == DataType::Bool)
      return $this->SetValue(BoolTo01(boolval($a_sValue)));

    // pokud neni string tak nic nemenime a zapiseme upozorneni
    if (!is_string($a_sValue) && $a_sValue !== null)
    {
      Logging::WriteLog(LogType::Anouncement, 
        'DBEntColumn.SetValueFromString() - Trying to store non string value. name="' . $this->i_sName . '" '.
          'value="' . $this->GetValue() . '" NotNull="' . BoolTo01Str($this->i_bNotNull) . '"');
      return $this->i_bValid;
    }
    
    // prazdny string odpovida hodnote null
    if ($a_sValue == '' || $a_sValue === null)
      return $this->SetValue(null);
    
    switch ($this->i_tDataType)
    {
      case DataType::String: $this->SetValue($a_sValue); break;
      case DataType::Integer:
        $val = str_replace(' ', '', $a_sValue);
        if (is_numeric ($val))
          $this->SetValue(intval($val));
        else
        {
          $this->i_bValid = false;
          $this->i_sInvalidDataMsg = 'Položka není platné celé číslo.';
        }
        break;
      case DataType::Float:
        $val = str_replace(',', '.', $a_sValue);
        $val = str_replace(' ', '', $val);
        if (is_numeric ($val))
          $this->SetValue(floatval($val));
        else
        {
          $this->i_bValid = false;
          $this->i_sInvalidDataMsg = 'Položka není platné desetinné číslo.';
        }
        break;
      case DataType::Date:
      case DataType::DateTrnc:
      case DataType::Timestamp:
        if (strtotime($a_sValue) == false)
        {
          $this->i_bValid = false;
          $this->i_sInvalidDataMsg = 'Položka není platný časový údaj.';
        }
        else
          $this->SetValue(strtotime($a_sValue));
        break;
    }    
    return $this->i_bValid;
  }
  public function GetValueAsString($a_bformated = true)
  {
    if ($this->GetValue() === null) 
      return '';
    
    switch ($this->i_tDataType)
    {
      case DataType::String:
        return $this->GetValue();
      case DataType::Integer:
        if (!$a_bformated || $this->i_bUnformated) return strval($this->GetValue());
        return number_format($this->GetValue(), 0, '', ' ');
      case DataType::Float:
        if (!$a_bformated || $this->i_bUnformated) 
          return number_format($this->GetValue(), 2, '.', '');
        return number_format($this->GetValue(), 2, ',', ' ');
      case DataType::Date:
        return date(DATE_FORMAT, $this->GetValue());
      case DataType::DateTrnc:
        return date('d.m.', $this->GetValue());
      case DataType::Timestamp:
        return date(DATE_TIME_FORMAT, $this->GetValue());
      case DataType::Bool:
        return BoolTo01Str($this->GetValue());
    }    
  }
  public function BuildHTMLInput()
  {
    switch ($this->i_tDataType)
    {
      case DataType::Date:
        return 
          '<input class="datepicker" type="text"'.
          ' name="' . $this->i_sName . '"'.
          ' value="' . $this->GetValueAsString() . '"'.
          ' maxlenght="10"'. 
          '/>';
      case DataType::Bool:
        return 
          '<input type="checkbox"'.
          ' name="' . $this->i_sName . '"'.
          (($this->$this->GetValue()) ? ' checked="checked" ' : '') . 
          '/>';
      default:
        return 
          '<input type="text"'.
          ' name="' . $this->i_sName . '"'.
          ' value="' . $this->GetValueAsString() . '"'.
          (($this->i_iMaxLenght > 0) ? ' maxlenght="' . $this->i_iMaxLenght . '" ' : '') . 
          '/>';
    }    
  }
}
class SQLColumn extends DBEntColumn
{
  public $i_sSQL;
  public function __construct($a_tDataType, $a_sName, $a_sSQL)
  {
    parent::__construct($a_tDataType, $a_sName);
    $this->i_sSQL = $a_sSQL;
    $this->i_bValid = true;
  }
  public function GetSelectSQL()
  {
    return '(' . $this->i_sSQL . ') as ' . $this->i_sName;    
  }  
}
