
SET ECHO OFF;

select 'Sys 1.00.00.00'
  from root;

/* tables */
INPUT tables/_tables.sql;

/* generators */
INPUT generators/_generators.sql;

/* procedures */
INPUT procs/_procs.sql;

/* views */
--INPUT views/_views.sql;

/* triggers */
INPUT triggers/_triggers.sql;

/* data */
INPUT data/_data.sql;

/* end */

select 'finished'
  from root;
