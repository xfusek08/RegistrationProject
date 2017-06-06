<?php
function ProcessGlobalAjaxRequest()
{
  $type = $_POST['type'];
  switch ($type)
  {
    case 'GetCalendarData' : echo GetCalendarDataXML($_POST['fromdate'], $_POST['todate']); break;
    case 'GetDetailEventsOnDay' : echo GetDetailEventsOnDayXML($_POST['date']); break;
  }
}

//specificke funkce-------------------------------------------------------------

/**
* Vrati strukturovane XML obsahujici strukturu udalosti ktere jsou ulozene v danem casovem rozmezi,
* Struktura je rozrazena do mesicu a dni, kde v kazdem dni je odpovidajici pocet udalosti.
* @param time $from  - datum od ktereho jsou data vygenerovany
* @param time $to    - datum do ktereho jsem data vygenerovany
* @return string     - vysledne XML
*/
function GetCalendarDataXML($fromString, $toString)
{
  $DateFrom = date('d.m.Y' , strtotime($fromString));  
  $DateTo = date('d.m.Y' , strtotime($toString));
  $eventPrototype = EVENT_TYPE;
  $eventPrototype = new $eventPrototype();
  
  $SQL = 
    'select'.
    '    ' . $eventPrototype->i_sPKColName . ','.
    '    ' . $eventPrototype->i_sFromColName . ','.
    '    ' . $eventPrototype->i_sStateColName .
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
  
  $response = '<respxml>';
  $response .= '<moths>';
  
  $newDay = true;
  $newMonth = true;
  for($i = 0; $i < count($fields); $i++)
  {
    $actEvTime = strtotime($fields[$i][$eventPrototype->i_sFromColName]);
    $actMonthNum = intval(date('m', $actEvTime));
    $actDay = date('d.m.Y', $actEvTime);                
    $actDayNum = date('d', $actEvTime);              

    if ($newMonth)
      $response .= '<month monthnum="' . $actMonthNum . '">';

    if ($newDay)
      $response .= '<day date="' . $actDay . '">';

    $response .= 
      '<event'.
      ' pk="' . $fields[$i][$eventPrototype->i_sPKColName] . '"'.
      ' time="' . date('H:i', $actEvTime) . '"'.
      ' state="' . $fields[$i][$eventPrototype->i_sStateColName] . '"/>';

    if (($i + 1) < count($fields))
    {          
      $newDay = (date('d', strtotime($fields[$i + 1][$eventPrototype->i_sFromColName]))) != $actDayNum;
      $newMonth = intval((date('m', strtotime($fields[$i + 1][$eventPrototype->i_sFromColName])))) != $actMonthNum;
    }
    else 
    {
      $newDay = true;
      $newMonth = true;
    }

    if ($newDay)
      $response .= '</day>';

    if ($newMonth)
      $response .= '</month>';
  }
  
  $response .= '</moths>';
  $response .= '</respxml>';
  
  return $response;
}

/**
* Vrati XML obsahujici podrobna data o udalostech z konkretniho dne. XML struktura 
* obsahuje elementy vygenerovane pomoci database entity.
* Struktura je rozrazena do mesicu a dni, kde v kazdem dni je odpovidajici pocet udalosti.
* @param time $from  - datum od ktereho jsou data vygenerovany
* @param time $to    - datum do ktereho jsem data vygenerovany
* @return string     - vysledne XML
*/
function GetDetailEventsOnDayXML($dateString)
{
  $DateFrom = date('d.m.Y H:i' , strtotime($dateString));
  $DateTo = date('d.m.Y H:i' , strtotime($dateString) + (60 * 60 * 24));
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
    Logging::WriteLog(LogType::Error, 'GetDetailEventsOnDayXML(): Error while selecting events');
    return 'fail';  
  }
  
  $response = '<respxml>';
  for ($i = 0; $i < count($fields); $i++)
  {
    $event = EVENT_TYPE;
    $event = new $event(intval($fields[$i][$eventPrototype->i_sPKColName]));
    if ($event->i_bLoad_Success)    
      $response .= '<event_html>' . $event->GetDayOwerwiewHTML() . '</event_html>';
    else
    {
      Logging::WriteLog(LogType::Error, 'GetDetailEventsOnDayXML(): Error while loading events');
      $response = 'fail';
    }
  }
  
  $response .= '</respxml>';
  return $response;
}