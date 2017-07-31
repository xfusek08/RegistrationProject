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
define("NEW_EVENT_HTML", ".\\resources\\templates\\Courses\\newCourse.html");
define("EDIT_EVENT_HTML", ".\\resources\\templates\\Courses\\editCourse.html");
define("OVERVIEW_EVENT_HTML", ".\\resources\\templates\\Courses\\overviewCourse.html");
define("SETTING_HTML", ".\\resources\\templates\\Courses\\CourseSettings.html");

// soubory se sablonami
define("NEW_REGISTRATION_HTML", ".\\resources\\templates\\Registrations\\newReg.html");
define("EDIT_REGISTRATION_HTML", ".\\resources\\templates\\Registrations\\editReg.html");
define("OVERVIEW_REGISTRATION_HTML", ".\\resources\\templates\\Registrations\\overviewReg.html");

 
/*
 * mozne konstanty, ktere budou substituovany
 * s
 * ---- nadefinovate v Event.php
 * {FROM_DATE} - datum ve formatu d.m.y brane z polozky Event::i_sFromColName
 * {FROM_TIME} - cas ve formatu H:i brane z polozky Event::i_sFromColName
 * {FROM_DAY} - nazev dnu vis 'GetCzechDayName(date('w', Event::i_sFromColName))
 * 
 */

/*
 * nepouzivane classes:
 * 
 *  .conndetail-inhtml
 *  .reservations
 */
