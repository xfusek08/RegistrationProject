
SET ECHO OFF;

select 'Sys 1.00.00.01'
  from root;

/* procedures */
INPUT procs/_procs.sql;

/* end */

select 'finished'
  from root;
