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
    case 'GetCalendarData' : echo GetCalendarDataXML($_POST['fromdate'], $_POST['todate'], $_POST['language']); break;
  }
  echo '</respxml>';
}
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
 *          <language text="Angličtina">
 *            <event pk="43" time="01:00" state="open" name="asd"/>
 *            ...
 *          </language>
 *          ...
 *        </day>
 *        ...
 *      </month>
 *      ...
 *    <moths>
 *  </calendardata>
 */

function GetCalendarDataXML($fromString, $toString, $language)
{
  $DateFrom = date('d.m.Y' , strtotime($fromString));  
  $DateTo = date('d.m.Y' , strtotime($toString));
  $v_iLanguagePK = 0;
  
  if (intval($language)!== false)
    $v_iLanguagePK = intval($language);
  
  $SQL = 
    'select'.
    '    rgcour_pk,'.
    '    cast(rgcour_dtfrom as date) as datefrom,'.
    '    cast(rgcour_dtfrom as time) as timefrom,'.
    '    rgcour_vname,'.
    '    rglng_text'.
    '  from'.
    '    rg_course'.
    '    left join rg_language on rglng_pk = rgcour_flanguage'.
    '  where'.
    '    rgcour_istate != 1 and'.
    '    rgcour_dtfrom >= ? and'.
    '    rgcour_dtfrom <= ?';
  
  if ($v_iLanguagePK > 0)
    $SQL .= ' and rgcour_flanguage = ?'; 
 
  $SQL .= 
    '  order by'.
    '    datefrom,'.
    '    rglng_text collate NC_UTF8_CZ,'.
    '    timefrom';

  $fields = null;
  $params = array($DateFrom, $DateTo);
  
  if ($v_iLanguagePK > 0)
    array_push($params, $v_iLanguagePK);
  
  if (!MyDatabase::RunQuery($fields, $SQL, false, $params))
  {
    echo 'fail';
    return;
  }
  
  $response = '<calendardata>';
  $response .= '<moths>';
  
  $newDate = true;
  $newMonth = true;
  $newLanguage = true;
  for($i = 0; $i < count($fields); $i++)
  {
    $actEvent = new Course(intval($fields[$i]['RGCOUR_PK']));
    $actEvTime = strtotime($fields[$i]['DATEFROM']);
    $actDate = date('d.m.Y', $actEvTime);                
    $actMonthNum = date('m', $actEvTime);
    $actLanguage = $fields[$i]['RGLNG_TEXT'];
        
    if ($newMonth)
      $response .= '<month monthnum="' . $actMonthNum . '">';

    if ($newDate)
      $response .= '<day date="' . $actDate . '">';

    if ($newLanguage)
      $response .= '<language text="' . $actLanguage . '">';

    $response .= 
      '<course'.
      ' pk="' . $fields[$i]['RGCOUR_PK'] . '"'.
      ' time="' . date('H:i', strtotime($fields[$i]['TIMEFROM'])) . '"'.
      ' capacity="' . $actEvent->GetCapacityStatus() . '"'.
      ' state="' . $actEvent->GetState() . '"'.
      ' name="' . $fields[$i]['RGCOUR_VNAME'] . '"/>';

    if (($i + 1) < count($fields))
    {          
      $newDate = (date('d.m.Y', strtotime($fields[$i + 1]['DATEFROM']))) !== $actDate;
      $newMonth = (date('m', strtotime($fields[$i + 1]['DATEFROM']))) !== $actMonthNum;
      $newLanguage = $fields[$i + 1]['RGLNG_TEXT'] !== $actLanguage || $newDate;
    }
    else 
    {
      $newDate = true;
      $newMonth = true;
      $newLanguage = true;
    }
    
    if ($newLanguage)
      $response .= '</language>';
    
    if ($newDate)
      $response .= '</day>';

    if ($newMonth)
      $response .= '</month>';
  }
  
  $response .= '</moths>';
  $response .= '</calendardata>';
  
  return $response;
}

function GetChosenCourseHTML($a_oChosenCourse)
{
  ob_start();
  if ($a_oChosenCourse->i_iPK != 0)
  {
    ?>
    <div class="coursedetail" pk="<?php echo $a_oChosenCourse->i_iPK; ?>">
      <div class="caption">
        <div>
          <div><?php echo $a_oChosenCourse->GetColumnByName('rgcour_vname')->GetValueAsString(); ?></div> 
          <div> zahájení: 
            <?php 
              $timestamp = $a_oChosenCourse->GetColumnByName('rgcour_dtfrom')->GetValue();
              echo 
                date('h:i', $timestamp) . ', ' . 
                date('d.m.Y', $timestamp) . 
                ' (' . GetCzechDayName(date('w', $timestamp)) . ')';
            ?>
          </div>
        </div>
      </div>
      <div class="content overview">
        <div><b>Jazyk:</b>
          <?php 
            $v_iLangPK = $a_oChosenCourse->GetColumnByName('rgcour_flanguage')->GetValue();
            echo (new Language($v_iLangPK))->GetColumnByName('rglng_text')->GetValueAsString(); 
          ?>
        </div>
        <div class="desctiption"> 
          <div class="cap">Popis:</div>
        </div>
        <div><?php echo $a_oChosenCourse->GetColumnByName('rgcour_vdesc')->GetValueAsString(); ?></div> 
      </div>
    </div>
    <?php
  }  
  return ob_get_clean();
}