<?php
session_start();
require_once '../PHP/Enums.php';
require_once '../PHP/Utils.php';
require_once '../PHP/Settings.php';
require_once '../PHP/Logs.php';
require_once '../PHP/Database.php';
require_once '../PHP/DatabaseEntity.php';
require_once '../PHP/CalendarEvent.php';
require_once '../PHP/Registration.php';

$IsSigned = isset($_SESSION['logged']);

if (isset($_POST['ajax']) && $IsSigned)
{
  echo 'ajax resp.';
  die;
}
?>
<!doctype html>
<html>
  <head>
    <title>Správce rezervací</title>
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