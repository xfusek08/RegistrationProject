
SET ECHO OFF;

/* tables */
INPUT tables/_tables.sql;

/* domains & exceptions*/
INPUT other/_other.sql;

/* functions */
--INPUT func/_func.sql;

/* end */

select 'finished'
  from root;
