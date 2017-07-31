<?php
session_start();
// system
require_once '../PHP/System/Enums.php';
require_once '../PHP/System/Utils.php';
require_once '../PHP/System/Settings.php';
require_once '../PHP/System/Logs.php';
require_once '../PHP/System/Database.php';
require_once '../PHP/System/DatabaseEntity.php';
require_once '../PHP/System/Alerts.php';

// registracni system
require_once '../PHP/RegSys/ResponsiveObject.php';
require_once '../PHP/RegSys/ResponsivePage.php';
require_once '../PHP/RegSys/Event.php';
require_once '../PHP/RegSys/Registration.php';
require_once '../PHP/RegSys/AjaxXMLFunctions.php';

//specializovany registracni system pro kurzy
require_once '../PHP/CourseRegSys/Language.php';
require_once '../PHP/CourseRegSys/Course.php';
require_once '../PHP/CourseRegSys/CourseRegistration.php';
require_once '../PHP/CourseRegSys/CourseSettingPage.php';

$IsSigned = isset($_SESSION['logged']);

if (isset($_POST['ajax']) && $IsSigned)
{
  ProcessGlobalAjaxRequest();
  die;
}
?>
<!doctype html>
<html>
  <head>
    <title>Správce registrací kurzů</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content='cs' />                  

    <link rel="stylesheet" href="resources/styles/adminstyle.css" type="text/css" media="screen" />  
    <link rel="stylesheet" href="resources/styles/CalendarStyle_admin.css" type="text/css" media="screen" />  
    <link rel="stylesheet" href="resources/styles/TimepickerStyle.css" type="text/css" media="screen" />  
    
    <script type="text/javascript" src="../jscripts/jQuery-1.12.4.min.js"></script>
    <script type="text/javascript" src="../jscripts/jQuerry-ui_1.11.4.min.js"></script>
    <script type="text/javascript" charset="UTF-8" src="../jscripts/jQuery-animate-shadow.min.js"></script>
    <script type="text/javascript" charset="UTF-8" src="../jscripts/utils.js"></script>
  </head>
  <body>  
    <?php
    if ($IsSigned)
      require 'main.php';
    else
      require 'login.php';
    ?>        

  </body>    
</html>