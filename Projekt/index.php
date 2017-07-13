<?php
session_start();
require_once 'php/RegSys/ClientRegistration.php';

$v_oChosenCourse = new Course(0);
$v_oRegistration = new CourseRegistration(0);

if (isset($_POST['ajax']))
{
  ProcessGlobalAjaxRequest();
  die;
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
    <h3>Vyberte kruz:</h3>
    <div class="coursechoose" pk="<?php echo($v_oCosenCourse->i_iPK); ?>">
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
    <h4>Vybraný kurz:</h4>
    <div>
      <div></div>
    </div>
  </form>
  <hr/>
</body>
</html>
