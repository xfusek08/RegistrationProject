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
