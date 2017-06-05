
drop collation NC_UTF8_CZ;

create collation NC_UTF8_CZ
   for UTF8 
   from UNICODE 
   case insensitive 
   'LOCALE=cs_CZ;ICU-VERSION=4.8';
