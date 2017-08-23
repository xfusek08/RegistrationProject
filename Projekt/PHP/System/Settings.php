<?php
// timeformats
define("DATE_TIME_FORMAT", "d.m.Y H:i:s");
define("DATE_FORMAT", "d.m.Y");

//time zone
date_default_timezone_set('Europe/Prague');

// database
define("DATABASE_FULLPATH", "D:\Work\Registration_project\DB\REGISTRATIONS.FDB ");
define("DATABASE_USER", "sysdba");
define("DATABASE_PASSWORD", "masterkey");

// select, ktery bere 2 parametry (?,?) odpovidajici zadanemu jmenu a hezlu
define("SQL_LOGIN_SELECT", 'select 1 from sy_tuser where sytusr_vident = ? and sytusr_vpassword = ?');

//Logs
define("LOG_FOLDER", "D:\Work\Registration_project\Repository\Projekt\logs");

// nazev tridy potomka udalosti
define("EVENT_TYPE", "Course");

// nazev tridy potomka registrace
define("REGISTRATION_TYPE", "CourseRegistration");

// nazev tridy pro stranu s nastavenim, potomek od ResponsivePage
define("SETTING_PAGEOJB", "CourseSettingPage");

// nastaveni oznaceni udalosti
// original
/*

define("S_EVENT_1P", "událost");
define("P_EVENT_1P", "události");
define("S_EVENT_2P", "události");
define("P_EVENT_2P", "událostí");
define("NEW_EVENT", "Nová Událost");

*/
// novy
define("S_EVENT_1P", "kurz");
define("P_EVENT_1P", "kurzy");
define("S_EVENT_2P", "kurzu");
define("P_EVENT_2P", "kurzů");
define("NEW_EVENT", "nový kurz");

// soubory se sablonami
define("NEW_EVENT_HTML", "resources\\templates\\Courses\\newCourse.html");
define("EDIT_EVENT_HTML", "resources\\templates\\Courses\\editCourse.html");
define("OVERVIEW_EVENT_HTML", "resources\\templates\\Courses\\overviewCourse.html");
define("SETTING_HTML", "resources\\templates\\Courses\\CourseSettings.html");

// soubory se sablonami
define("NEW_REGISTRATION_HTML", "resources\\templates\\Registrations\\newReg.html");
define("EDIT_REGISTRATION_HTML", "resources\\templates\\Registrations\\editReg.html");
define("OVERVIEW_REGISTRATION_HTML", "resources\\templates\\Registrations\\overviewReg.html");

 
// *********************** E-MAILS *****************************************************

// E-mail ze kterého bude odeslána zpráva pro klienta
define('FROM_EMAIL', 'Alasko-Rezervace@alasko.cz');

// na který se pošle oznámení o vytvořené rezervaci, může být stejný jako FROM_EMAIL
define('ADMIN_ANNOUNCEMENT_EMAIL', 'petr.fusek97@gmail.com');

// Předmět e-mailu pro klienta
define('TO_CLIENT_EMAIL_SUBJECT', 'Vytvoření rezervace');

// Cesta k HTML Šabloně e-mailu pro klienta
if ($_SESSION['accestype'] === 'client')
  define('TO_CLIENT_EMAIL_TEMPLATE_PATH', 'admin\\resources\\templates\\Registrations\\ToClientEmailTemplate.html');
else if ($_SESSION['accestype'] === 'admin')
  define('TO_CLIENT_EMAIL_TEMPLATE_PATH', 'resources\\templates\\Registrations\\ToClientEmailTemplate.html');

// Předmět e-mailu pro správce
define('TO_ADMIN_EMAIL_DEF_SUBJECT', 'Nová rezervace.');

// HTML Šablona e-mailu pro správce
if ($_SESSION['accestype'] === 'client')
  define('TO_ADMIN_EMAIL_TEMPLATE_PATH', 'admin\\resources\\templates\\Registrations\\ToAdminEmailTemplate.html');
else if ($_SESSION['accestype'] === 'admin')
  define('TO_ADMIN_EMAIL_TEMPLATE_PATH', 'resources\\templates\\Registrations\\ToAdminEmailTemplate.html');
