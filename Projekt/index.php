<?php
require_once 'PHP/Settings.php';
require_once 'PHP/Logs.php';
require_once 'PHP/Database.php';
require_once 'PHP/Registration.php';
require_once 'PHP/MyMails.php';
require_once 'PHP/AjaxXMLFunctions.php';

if (isset($_POST['ajax']))
{
  if ($_POST['type'] === 'getterms')  
  {    
    GetTermsXML();    
  }
  die;
}
?>
<!doctype html>
﻿<html>
  <body>
    <a href="admin/index.php">admin...</a>
  </body>
</html>
