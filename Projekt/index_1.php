<?php
session_start();
require_once 'php/RegSys/ClientRegistration.php';
require_once 'php/CourseRegSys/Language.php';

$v_iSelectedLangPK = 0;
$v_oChosenCourse = new Course(0);
$v_oRegistration = new CourseRegistration(0);
$v_oErrorMessage = '';
$v_bSubmit = false;
$v_bConfirm = false;
     
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
  if (!isset($_POST['type']))
    die;
  
  if ($_POST['type'] == 'SelectCourse')
  {
    if (!isset($_POST['pk']))
      die;
    if (intval($_POST['pk']) === false)
      die;
    $v_oChosenCourse = new Course(intval($_POST['pk']));
    if ($v_oChosenCourse->i_bLoad_Success)
    {
      $v_oRegistration->GetColumnByName('rgreg_fcourse')->SetValue($v_oChosenCourse->i_iPK);
      $v_oRegistration->GetColumnByName('rgreg_flanguage')->SetValue(
        $v_oChosenCourse->GetColumnByName('rgcour_flanguage')->GetValue());
      echo '<respxml><courhtml>' . GetChosenCourseHTML($v_oChosenCourse) . '</courhtml></respxml>';
    }
    else 
      echo '<respxml><html>Chyba: nepodařilo se vyhledat kurz</html></resxml>';
    $_SESSION['chosencourse'] = serialize($v_oChosenCourse);
    $_SESSION['registration'] = serialize($v_oRegistration);
  }
  else
    ProcessGlobalAjaxRequest();
  
  die;
}

if (isset($_POST['reload']))
{
  $v_bSubmit = false;
  unset($_SESSION['chosencourse']);
  unset($_SESSION['registration']);
  $v_oChosenCourse = new Course(0);
  $v_oRegistration = new CourseRegistration(0);
}

if (isset($_POST['c_submit']))
{
  $v_bSubmit = true;
  $v_oRegistration->LoadFromPostData();
  if ($v_oRegistration->GetColumnByName('rgreg_fcourse')->i_bValid)
  {
    if ($v_oRegistration->IsDataValid())
    {
      $v_oRegistration->GetColumnByName('rgreg_isnew')->SetValue(true);
      $v_bConfirm = true;
    }
  }
  else
    $v_oErrorMessage = 'Není vybrána lekce.';

  $_SESSION['registration'] = serialize($v_oRegistration);
}
else if (isset($_POST['confirm']))
{
  $v_oRegistration->GetColumnByName('rgreg_isnew')->SetValue(true);
  if ($v_oRegistration->SaveToDB(false))
  {
    echo '<h4 style="color: white;">Vaše registrace byla úspěšně odeslána.</h4>';
    echo '<form method="post"><input type="submit" name="reload" value="pokračovat"/></form>';
    die;
  }
  else
    $v_oErrorMessage = 'Registraci se nepodařilo uložit';
}
else if (!isset($_POST['bck']))
{
  $v_bSubmit = false;
  unset($_SESSION['chosencourse']);
  unset($_SESSION['registration']);
  $v_oChosenCourse = new Course(0);
  $v_oRegistration = new CourseRegistration(0);
}



?>

<!doctype html>
﻿<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="Content-Language" content="cs"/>
  <link rel="stylesheet" href="css/cm_css_lekol.css" type="text/css" media="screen"/>
  <link rel="stylesheet" href="css/CalendarStyle_Client.css" type="text/css" media="screen"/>
  
  <script type="text/javascript" src="jscripts/jQuery-1.12.4.min.js"></script>
  <script type="text/javascript" src="jscripts/jQuerry-ui_1.11.4.min.js"></script>
  <script type="text/javascript" charset="UTF-8" src="jscripts/utils.js"></script>
  <script type="text/javascript" charset="UTF-8" src="jscripts/ClientRegistration.js"></script> 
  <title>E-Registrace</title>
</head>
<body>
  <form method="post">    
  
    <?php if ($v_bConfirm) { ?>
    
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
      <input type="submit" name="confirm" value="Potvrdit">     
      <input type="submit" name="bck" value="Zpět">     
    </div>
    
    <?php } else { ?>
    
    <h3>Výběr jednorázových lekcí:</h3>
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
          <div class="header">Volné termíny</div>  
          <!--<div class="content-header">Kurzy:</div>  -->
          <div class="content"></div>
        </div>
        <br/>
        Obsazenost lekce: <b style="font-family: monospace; font-size: 14px">"x/y"</b> nebo <b style="font-family: monospace; font-size: 14px"">"x/-"</b><br/>
        <b style="font-family: monospace; font-size: 14px; margin-left: 8px;">x</b> = počet auktuálně zapsaných <br/>
        <b style="font-family: monospace; font-size: 14px; margin-left: 8px;">y</b> = maximální počet <br/>
        <b style="font-family: monospace; font-size: 14px; margin-left: 8px;">-</b> = neomezená kapacita <br/> 
      </div>
    </div>
    <hr/>    
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
      <input type="submit" name="c_submit" value="Rezervovat">     
      <hr/>  
      * povinné údaje <br/>
      <?php if ($v_oErrorMessage != '') echo $v_oErrorMessage;?>
    </div>
  
    <?php } ?>
  
  </form>
</body>
</html>
<?php
