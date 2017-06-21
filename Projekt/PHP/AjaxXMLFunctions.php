<?php
function ProcessGlobalAjaxRequest()
{
  $type = $_POST['type'];
  echo '<respxml>';
  switch ($type)
  {
    case 'GetCalendarData' : echo GetCalendarDataXML($_POST['fromdate'], $_POST['todate']); break;
    case 'SelectDay' : echo SelectDay($_POST['date']); break;
    case 'GetNavigation' : echo GetNavigation(); break;
    case 'CreateEvent' : echo CreateEvent($_POST['date']); break;
    case 'OpenEvent' : echo OpenEvent($_POST['pk']); break;
    case 'CloseEvent' : echo CloseEvent(); break;
    case 'EventAjax' : echo EventAjax(); break;
  }
  echo '</respxml>';
}

//specificke funkce-------------------------------------------------------------

/**
 * Vrati strukturovane XML obsahujici strukturu udalosti ktere jsou ulozene v danem casovem rozmezi,
 * Struktura je rozrazena do mesicu a dni, kde v kazdem dni je odpovidajici pocet udalosti.
 * @param time $from  - datum od ktereho jsou data vygenerovany
 * @param time $to    - datum do ktereho jsem data vygenerovany
 * @return struktura:
 *  <calendardata>
 *    <moths>
 *      <month monthnum="6">
 *        <day date="20.06.2017">
 *          <event pk="43" time="01:00" state="open"/>
 *          ...
 *        </day>
 *        ...
 *      </month>
 *      ...
 *    <moths>
 *  </calendardata>
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
  $response .= '<moths>';
  
  $newDay = true;
  $newMonth = true;
  for($i = 0; $i < count($fields); $i++)
  {
    $actEvent = EVENT_TYPE; 
    $actEvent = new $actEvent($fields[$i][$eventPrototype->i_sPKColName]);
    
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
      ' state="' . $actEvent->GetState() . '"/>';

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
  $response .= '</calendardata>';
  
  return $response;
}

/**
 * Vrati XML obsahujici podrobna data o udalostech z konkretniho dne. XML struktura 
 * obsahuje elementy vygenerovane pomoci database entity.
 * Struktura je rozrazena do mesicu a dni, kde v kazdem dni je odpovidajici pocet udalosti.
 * @param time $from  - datum od ktereho jsou data vygenerovany
 * @param time $to    - datum do ktereho jsem data vygenerovany
 * @return strutura:
 *  <dayevents date="21.06.2017">
 *    <event_html pk="36" state="open" selected> -- sekvence dat o udalostech v konkretnim dni
 *      {html data}
 *    </event_html>
 *    ...
 *  </dayevents>
 */
function SelectDay($a_sDateTimeString)
{
  $v_iTime = strtotime($a_sDateTimeString);
  if ($v_iTime === false)
  {
    Logging::WriteLog(LogType::Error, 'SelectDay(' . $a_sDateTimeString . ') - time error');
    return (new Alert('red', 'Invalid time value'))->GetXML();
  }
  
  $_SESSION['actday'] = date('d.m.Y', $v_iTime);
  $DateFrom = date('d.m.Y H:i' , $v_iTime);
  $DateTo = date('d.m.Y H:i' , $v_iTime + (60 * 60 * 24));
  
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
    return (new Alert('red', 'Chyba vyhledávání ' . P_EVENT_2P))->GetXML();
  }
  $v_iacktOpenPK = 0;
  if (isset($_SESSION['openevent']))
    $v_iacktOpenPK = unserialize($_SESSION['openevent'])->i_iPK;
  
  $response = '<dayevents date="' . $_SESSION['actday'] . '">';
  for ($i = 0; $i < count($fields); $i++)
  {
    $event = EVENT_TYPE;
    $event = new $event(intval($fields[$i][$eventPrototype->i_sPKColName]));
    if ($event->i_bLoad_Success)
    {
      $response .= 
        '<event_html pk="' . $event->i_iPK . '" state="' .$event->GetState(). '"' . 
            (($v_iacktOpenPK == $event->i_iPK) ? ' selected' : '' ). '>' . 
          $event->GetDayOwerwiewHTML() . 
        '</event_html>';
    }
    else
    {
      Logging::WriteLog(LogType::Error, 'GetDetailEventsOnDayXML(): Error while loading events');
      return (new Alert('red', 'Chyba vyhledávání ' . P_EVENT_2P))->GetXML();
    }
  }
  
  $response .= '</dayevents>';
  return $response;
}
/**
 * Vrati XML obsahujici informaci o aktualni vybranem datu a status prave otevrene udalosti
 * @returns Struktura: 
 *  <navigation>
 *    <actday>{aktualnidatum}</actday> -- dnesni datum, pokud neni definovano 
 *    <openevent>
 *        {struktura stavu objektu openevent vis. ResponsiveObject::GetResponse()}  -- prazdne, pokdu neni nic otevreno
 *    </openevent>
 *  </navigation>
 */
function GetNavigation()
{
  $v_sActDayString = '';
  $v_iOpenEventPK = '';
  if (isset($_SESSION['actday']))
    $v_sActDayString = $_SESSION['actday'];
  else
  {
    $v_sActDayString = date('d.m.Y');
    $_SESSION['actday'] = $v_sActDayString;
  }
  
  $v_sRes = '<navigation>';
  $v_sRes .= '<actday>' . $v_sActDayString . '</actday>';
  $v_sRes .= 
    '<openevent>' . 
      (isset($_SESSION['openevent']) ? unserialize($_SESSION['openevent'])->GetResponse() : '') . 
    '</openevent>';
  $v_sRes .= '</navigation>';
  return $v_sRes;
}
// obsluha aktivní události

/**
 * Vytvori udalost ve vychozim stavu pro datum na konkretnim predanem dni
 * udalost zadefinuje do ssession pod nazvem 'openevent'
 * 
 * @param string $dateString - datum ve formatu d.m.Y pro kter7 se m8 udalost vytvorit
 * @return string xml ktere obsahuje popis stavu udalosti pro obsluhu v javascriptu
 */
function CreateEvent($dateString)
{
  $event = EVENT_TYPE;
  $event = new $event();
  $resp = $event->GetColumnByName($event->i_sFromColName)->SetValueFromString($dateString);  
  $resp = $event->GetResponse();
  $_SESSION['openevent'] = serialize($event);
  return $resp;
}
/**
 * Otevre novou udalost a prepise ji stavajici otevrenou
 * 
 * @param string $a_sPK - primarni klic udalosti, ktera se ma otevrit v textove podobe
 * @return string xml ktere obsahuje popis stavu udalosti pro obsluhu v javascriptu
 */
function OpenEvent($a_sPK)
{
  $v_iPK = intval($a_sPK);
  if ($v_iPK === false)
  {
    Logging::WriteLog(LogType::Error, 'OpenEvent(' . $a_sPK . ') - pk ins not valid integer');
    return (new Alert('red', 'Cyba při otevírání ' . S_EVENT_2P))->GetXML();    
  }
  
  $event = EVENT_TYPE;
  $event = new $event($v_iPK);
  if (!$event->i_bLoad_Success)
  {
    Logging::WriteLog(LogType::Error, 'OpenEvent(' . $a_sPK . ') - failed to load event');
    return (new Alert('red', 'Cyba při otevírání ' . S_EVENT_2P))->GetXML();    
  }
  $resp = $event->GetResponse();
  $_SESSION['openevent'] = serialize($event);
  return $resp;
}
/**
 * Oddefinuje udalost a nic nevraci
 * pouzivat az jako posledni krok
 */
function CloseEvent()
{
  if (isset($_SESSION['openevent']))
    unset($_SESSION['openevent']);
}

/**
 * Necha otevrenou udalost spracovat ajax dotaz
 */
function EventAjax()
{
  if (isset($_SESSION['openevent']))
  {
    $event = unserialize($_SESSION['openevent']);
    $event->ProcessAjax($_POST['EventAjaxType']);
    $resp = $event->GetResponse();
    $_SESSION['openevent'] = serialize($event);
    return $resp;
  }
  else
    WriteAlert('red', 'Událost není vytvořena.');
}
