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

// nazev tridy potomka
define("EVENT_TYPE", "Course");

// nastaveni oznaceni udalosti
// original
/*

define("U_UDALOST", "Událost");
define("U_UDALOSTI", "Události");
define("U_NOVA_UDALOST", "Nová Událost");

*/
// novy
define("UDALOST", "kurz");
define("UDALOSTI", "kurzy");
define("UDALOSTI_2PD", "kurzu");
define("NOVA_UDALOST", "nový kurz");

// soubory se sablonami
define("NEW_EVENT_HTML", ".\\resources\\templates\\Events\\newEvent.html");

 
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
