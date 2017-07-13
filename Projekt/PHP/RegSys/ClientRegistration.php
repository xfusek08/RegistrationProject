<?php
require_once 'PHP/System/Enums.php';
require_once 'PHP/System/Utils.php';
require_once 'PHP/System/Settings.php';
require_once 'PHP/System/Logs.php';
require_once 'PHP/System/Database.php';
require_once 'PHP/System/DatabaseEntity.php';
require_once 'PHP/System/Alerts.php';

require_once 'ResponsiveObject.php';
require_once 'Event.php';
require_once 'Registration.php';

require_once 'PHP/CourseRegSys/Language.php';
require_once 'PHP/CourseRegSys/Course.php';
require_once 'PHP/CourseRegSys/CourseRegistration.php';

function ProcessGlobalAjaxRequest()
{
  $type = $_POST['type'];
  echo '<respxml>';
  switch ($type)
  {
    case 'GetCalendarData' : echo GetCalendarDataXML($_POST['fromdate'], $_POST['todate']); break;
    case 'SelectDay' : echo SelectDay($_POST['date']); break;
    case 'SelectEvent' : echo SelectEvent($_POST['pk']); break;    
  }
  echo '</respxml>';
}

function GetCalendarDataXML($fromString, $toString)
{
  $DateFrom = date('d.m.Y' , strtotime($fromString));  
  $DateTo = date('d.m.Y' , strtotime($toString));
  $eventPrototype = EVENT_TYPE;
  $eventPrototype = new $eventPrototype();
  
  $SQL = 
    'select'.
    '    ' . $eventPrototype->i_sPKColName . ','.
    '    ' . $eventPrototype->i_sFromColName .
    '  from'.
    '    ' . $eventPrototype->i_sTableName .
    '  where'.
    '    ' . $eventPrototype->i_sFromColName . ' >= ? and'.
    '    ' . $eventPrototype->i_sFromColName . ' <= ?'.
    '  order by ' . $eventPrototype->i_sFromColName . '';
  
  $fields = null;
  if (!MyDatabase::RunQuery($fields, $SQL, false, array($DateFrom, $DateTo)))
  {
    echo 'fail';
    return;
  }
  
  $response = '<calendardata>';
  
  for($i = 0; $i < count($fields); $i++)
  {
    $actEvent = EVENT_TYPE; 
    $actEvent = new $actEvent($fields[$i][$eventPrototype->i_sPKColName]);

    $actEvTime = strtotime($fields[$i][$eventPrototype->i_sFromColName]);

    if ($actEvent->GetState() !== 'open') continue;
    $response .= 
      '<course'.
      ' day="' .  date('d.m.Y', $actEvTime) . '"'.
      ' pk="' . $actEvent->i_iPK . '"'.
      ' name="' . $actEvent->GetColumnByName($actEvent->i_sEventNameColName)->GetValueAsString() . '"'.
      ' capacity="' . $actEvent->GetCapacityStatus() . '"'.
      ' time="' . date('H:i', $actEvTime) . '"/>';
  }
  $response .= '</calendardata>';
  
  return $response;
}