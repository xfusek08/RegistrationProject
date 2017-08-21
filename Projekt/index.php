<?php
session_start();
require_once 'php/RegSys/ClientRegistration.php';
require_once 'php/CourseRegSys/Language.php';

// vychozi promenne
$v_sState = 'courseSelect';
$v_iSelectedLangPK = 0;
$v_oChosenCourse = new Course(0);
$v_oRegistration = new CourseRegistration(0);
$v_sErrorMessage = '';
$v_bSubmit = false;

// nacteme hodnoty promennych z session
if (isset($_SESSION['state']))
    $v_sState = $_SESSION['state'];

if (isset($_SESSION['chosencourse']))
  $v_oChosenCourse = unserialize ($_SESSION['chosencourse']);

if (!($v_oChosenCourse instanceof Course))
  $v_oChosenCourse = new Course(0);

if (isset($_SESSION['registration']))
  $v_oRegistration = unserialize ($_SESSION['registration']);

if (!($v_oRegistration instanceof CourseRegistration))
  $v_oRegistration = new CourseRegistration(0);

if (isset($_POST['ajax']))
{
  if ($_POST['type'] == 'SelectCourse')
  {
    if (!isset($_POST['pk']))
      SaveAndDie();
    if (intval($_POST['pk']) === false)
      SaveAndDie();
    
    $v_oChosenCourse = new Course(intval($_POST['pk']));
    if ($v_oChosenCourse->i_bLoad_Success)
    {
      $v_oRegistration->GetColumnByName('rgreg_fcourse')->SetValue($v_oChosenCourse->i_iPK);
      $v_oRegistration->GetColumnByName('rgreg_flanguage')->SetValue(
        $v_oChosenCourse->GetColumnByName('rgcour_flanguage')->GetValue());
      echo '<respxml><courhtml><h3>Detail vybrané lekce:</h3>' . GetChosenCourseHTML($v_oChosenCourse) . '</courhtml></respxml>';
    }
    else 
      echo '<respxml><courhtml>Chyba: nepodařilo se vyhledat kurz</courhtml></resxml>';
  }
  else
  {
    if ($_POST['language'])
      if (intval($_POST['language']))
        $v_iSelectedLangPK = intval($_POST['language']);
    ProcessGlobalAjaxRequest();
  }
  SaveAndDie(); 
}
else if (isset($_POST['submit']))
{
  switch ($v_sState)
  {
    case 'courseSelect':
      if ($v_oChosenCourse->i_iPK > 0)
        $v_sState = 'registration';
      else
        $v_sErrorMessage = 'Lekce musí být vybrána.';  
      break;
    case 'registration':
      $v_bSubmit = true;
      $v_oRegistration->LoadFromPostData();
      if ($v_oRegistration->IsDataValid())
      {
        $v_oRegistration->GetColumnByName($v_oRegistration->i_aDBAliases['isNew'])->SetValue(true);
        $v_sState = 'confirm';
      }  
      break;
    case 'confirm':
      // kontrola jestli je jeste porad misto
      $v_oChosenCourse = new Course($v_oChosenCourse->i_iPK);
      if ($v_oChosenCourse->GetState() == 'full')
      {
        $v_sErrorMessage = 'Bohužel, vybraný kurz je již obsazený.';
        break;
      }
      // ulozime registraci
      $v_oRegistration->GetColumnByName('rgreg_isnew')->SetValue(true);
      if ($v_oRegistration->SaveToDB(false))
        $v_sState = 'finished';
      else
        $v_sErrorMessage = 'Registraci se nepodařilo uložit';
      break;
  } 
}
else if (isset($_POST['back']))
{
  switch ($v_sState)
  {
    case 'registration': 
      $v_oRegistration->LoadFromPostData();
      $v_sState = 'courseSelect'; 
      break;
    case 'confirm': $v_sState = 'registration'; break;
  } 
}
else
{
  unset($_SESSION['chosencourse']);
  unset($_SESSION['registration']);$v_sState = 'courseSelect';
  $v_iSelectedLangPK = 0;
  $v_oChosenCourse = new Course(0);
  $v_oRegistration = new CourseRegistration(0);
  $v_sErrorMessage = '';
  $v_bSubmit = false;
}
?>

<!doctype html>
﻿<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="Content-Language" content="cs"/>
  <link rel="stylesheet" href="css/cm_css_lekol.min.css" type="text/css" media="screen"/>
  <link rel="stylesheet" href="css/CalendarStyle_Client.min.css" type="text/css" media="screen"/>
  <script type="text/javascript" src="jscripts/jQuery-1.12.4.min.js"></script>
  <script type="text/javascript" src="jscripts/jQuerry-ui_1.11.4.min.js"></script>
  <script type="text/javascript" charset="UTF-8" src="jscripts/utils.js"></script>
  <script type="text/javascript" charset="UTF-8" src="jscripts/ClientRegistration.min.js"></script> 
  <title>E-Registrace</title>
</head>
<body>
  <form method="post">    
    <?php if ($v_sState == 'courseSelect') { ?>
    
    <h2>Krok 1: Vyberte lekci:</h2>
    <p>
      Preferovaný jazyk:
      <select name="languagesel">
        <option value="0">všechny</option>
        <?php
          $v_oLanguage = new Language($v_iSelectedLangPK);
          echo $v_oLanguage->GetLangSelectOptions();
        ?>        
      </select>
    </p>
    <div class="coursechoose" pk="<?php echo($v_oChosenCourse->i_iPK); ?>">
      <div class="monthview">
        <div id="datepicker"></div>
      </div>
      <div class="dayview">
        <div class="conn">
          <h3>Přehled lekcí pro den: <span class="date">nevybráno</span></h3>
          <div class="content"></div>
        </div>
      </div>
    </div>
    <div style="height: 5px;">
      <input type="submit" name="submit" value="další"/> 
    </div>
    <br/><hr/>
    <?php
      if ($v_sErrorMessage != '')
        echo '<p class="error">' . $v_sErrorMessage . '</p><hr/>';
    ?>
    Vyberte lekci kliknutím.<br/><br/>
    Obsazenost lekce: <b style="font-family: monospace; font-size: 14px">"x/y"</b> nebo <b style="font-family: monospace; font-size: 14px"">"x/-"</b><br/>
    <b style="font-family: monospace; font-size: 14px; margin-left: 8px;">x</b> = počet auktuálně zapsaných <br/>
    <b style="font-family: monospace; font-size: 14px; margin-left: 8px;">y</b> = maximální počet <br/>
    <b style="font-family: monospace; font-size: 14px; margin-left: 8px;">-</b> = neomezená kapacita <br/> 
    
    <?php } else if ($v_sState == 'registration') { ?>
    
    <h2>Registrace:</h2> 
    <div class="reg-form">
      <table style="width: 100%;">
        <tbody>
          <tr>
            <td>Vybraná lekce:</td>
            <td class="selcourse"><?php echo GetChosenCourseHTML($v_oChosenCourse); ?></td>
          </tr>
          <tr>
            <td>Jméno:</td>
            <td>
              <input 
                valid="<?php echo BoolTo01Str($v_oRegistration->GetColumnByName('rgreg_vclfirstname')->i_bValid || !$v_bSubmit); ?>"
                message="<?php echo $v_oRegistration->GetColumnByName('rgreg_vclfirstname')->i_sInvalidDataMsg; ?>"
                type="text" 
                name="rgreg_vclfirstname" 
                value="<?php echo $v_oRegistration->GetColumnByName('rgreg_vclfirstname')->GetValueAsString(); ?>" 
                maxlength="100">&nbsp;*
            </td>
          </tr>
          <tr>
            <td>Příjmení:</td>
            <td>
              <input 
                valid="<?php echo BoolTo01Str($v_oRegistration->GetColumnByName('rgreg_vcllastname')->i_bValid || !$v_bSubmit); ?>"
                message="<?php echo $v_oRegistration->GetColumnByName('rgreg_vcllastname')->i_sInvalidDataMsg; ?>"
                type="text" 
                name="rgreg_vcllastname" 
                value="<?php echo $v_oRegistration->GetColumnByName('rgreg_vcllastname')->GetValueAsString(); ?>" 
                maxlength="100">&nbsp;*
            </td>
          </tr>
          <tr>
            <td>E-mail:</td>
            <td>
              <input 
                valid="<?php echo BoolTo01Str($v_oRegistration->GetColumnByName('rgreg_vclemail')->i_bValid || !$v_bSubmit); ?>"
                message="<?php echo $v_oRegistration->GetColumnByName('rgreg_vclemail')->i_sInvalidDataMsg; ?>"
                type="text" 
                name="rgreg_vclemail" 
                value="<?php echo $v_oRegistration->GetColumnByName('rgreg_vclemail')->GetValueAsString(); ?>" 
                maxlength="300">&nbsp;*
            </td>
          </tr>
          <tr>
            <td>Telefonní číslo:</td>
            <td>
              <input 
                valid="<?php echo BoolTo01Str($v_oRegistration->GetColumnByName('rgreg_vcltelnumber')->i_bValid || !$v_bSubmit); ?>"
                message="<?php echo $v_oRegistration->GetColumnByName('rgreg_vcltelnumber')->i_sInvalidDataMsg; ?>"
                type="text" 
                name="rgreg_vcltelnumber" 
                value="<?php echo $v_oRegistration->GetColumnByName('rgreg_vcltelnumber')->GetValueAsString(); ?>" 
                maxlength="100">&nbsp;*
            </td>
          </tr>
          <tr>
            <td>Poznámka:</td>
            <td>
              <textarea 
                valid="<?php echo BoolTo01Str($v_oRegistration->GetColumnByName('rgreg_vtexts')->i_bValid || !$v_bSubmit); ?>"
                message="<?php echo $v_oRegistration->GetColumnByName('rgreg_vtexts')->i_sInvalidDataMsg; ?>"
                class="editarea" 
                name="rgreg_vtext" 
                maxlength="4000"><?php echo $v_oRegistration->GetColumnByName('rgreg_vtext')->GetValueAsString(); ?></textarea>
            </td>
          </tr>
        </tbody>
      </table>
      <div style="height: 20px;">
        <input type="submit" name="submit" value="Rezervovat" />     
        <input style="float: left" type="submit" name="back" value="zpět" />     
      </div>
      <hr/> 
      <?php
      if ($v_sErrorMessage != '')
        echo '<p class="error">' . $v_sErrorMessage . '</p><hr/>';
      ?>
      * povinné údaje
    </div>
    
    <?php } else if ($v_sState == 'confirm') { ?>
    
    <h2>Potvrzení registrace</h2>
    <div class="reg-form">
      <table style="width: 100%;">
        <tbody>
          <tr>
            <td>Vybraná lekce:</td>
            <td class="selcourse">
              <?php echo GetChosenCourseHTML($v_oChosenCourse); ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td>Jméno:</td>
            <td>
              <?php echo $v_oRegistration->GetColumnByName('rgreg_vclfirstname')->GetValueAsString(); ?>
            </td>
          </tr>
          <tr>
            <td>Příjmení:</td>
            <td>
              <?php echo $v_oRegistration->GetColumnByName('rgreg_vcllastname')->GetValueAsString(); ?>
            </td>
          </tr>
          <tr>
            <td>E-mail:</td>
            <td>
              <?php echo $v_oRegistration->GetColumnByName('rgreg_vclemail')->GetValueAsString(); ?>
            </td>
          </tr>
          <tr>
            <td>Telefonní číslo:</td>
            <td>
              <?php echo $v_oRegistration->GetColumnByName('rgreg_vcltelnumber')->GetValueAsString(); ?>
            </td>
          </tr>
          <tr>
            <td>Poznámka:</td>
            <td>
              <textarea readonly><?php echo $v_oRegistration->GetColumnByName('rgreg_vtext')->GetValueAsString(); ?></textarea>
            </td>
          </tr>
        </tbody>
      </table>
       <div style="height: 20px;">
        <input type="submit" name="submit" value="Potvrdit" />     
        <input style="float: left" type="submit" name="back" value="zpět" />     
      </div>
    </div>
    <?php
    if ($v_sErrorMessage != '')
      echo 
        '<hr/><p class="error">' . $v_sErrorMessage . '</p>'.
        '<p>Pomocí tlačítka zpět můžete vybrat jiný kurz bez zrtáty dat.</p>';
    ?>
    
    <?php } else if ($v_sState == 'finished') { ?>
    
    <h2>Hotovo</h2>
    <p>Vaše registrace byla úspěšně odeslána.</p>
    <input type="submit" name="reload" value="pokračovat"/>
    
    <?php } ?>
  </form>
</body>
</html>
<?php

SaveAndDie();

function SaveAndDie()
{
  global 
    $v_oChosenCourse,
    $v_oRegistration,
    $v_sState;
  
  $_SESSION['chosencourse'] = serialize($v_oChosenCourse);
  $_SESSION['registration'] = serialize($v_oRegistration);
  $_SESSION['state'] = $v_sState;
  die;
}
