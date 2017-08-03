<?php
session_start();
require_once 'php/RegSys/ClientRegistration.php';
require_once 'php/CourseRegSys/Language.php';

$v_iSelectedLangPK = 0;
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
      echo '<respxml><courhtml>' . GetChosenCourseHTML($v_oChosenCourse) . '</courhtml></respxml>';
    else 
      echo '<respxml><html>Chyba: nepodařilo se vyhledat kurz</html></resxml>';
    $_SESSION['chosencourse'] = serialize($v_oChosenCourse);
  }
  else
    ProcessGlobalAjaxRequest();
  
  die;
}

if (isset($_POST['c_submit']))
{
  $v_oRegistration->LoadFromPostData();
  if ($v_oRegistration->IsDataValid())
  {
    // pokracovat v ukládání    
  }
  else
  {
    // chyby    
  }
  $_SESSION['registration'] = serialize($v_oRegistration);
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
    <h3>Přehled jednorázových lekcí:</h3>
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
      </div>
    </div>
    <hr/>    
    <h4>Registrace:</h4> 
    <div class="reg-form">
      <table style="width: 100%;">
        <tbody>
         <tr>
          <td>Vybraný kurz:</td>
          <td class="selcourse"><?php echo GetChosenCourseHTML($v_oChosenCourse); ?></td>
        </tr>
        <tr>
          <td>Jméno:</td>
          <td>
            <input 
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
              type="text" 
              name="rgreg_vcltelnumber" 
              value="<?php echo $v_oRegistration->GetColumnByName('rgreg_vcltelnumber')->GetValueAsString(); ?>" 
              maxlength="100">&nbsp;*
          </td>
        </tr>
        <tr>
          <td>Poznámka:</td>
          <td>
            <textarea class="editarea" name="rgreg_vtexts" maxlength="4000">
              <?php echo $v_oRegistration->GetColumnByName('rgreg_vtext')->GetValueAsString(); ?>
            </textarea>
          </td>
        </tr>
      </tbody></table>
      <input type="submit" name="c_submit" value="Rezervovat">     
      <hr/>  
      * povinné údaje
    </div>
  </form>
</body>
</html>
<?php
