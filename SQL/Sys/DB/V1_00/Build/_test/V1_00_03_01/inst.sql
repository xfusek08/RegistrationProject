
SET ECHO ON;

-- create user "noe"
SHELL GSEC -add noe -pw noe -password masterkey -user sysdba;

/* domains & exceptions*/
INPUT other/_other.sql;

/* tables */
INPUT tables/_tables.sql;

/* trigers */
INPUT trigers/_trigers.sql;

/* data */
INPUT data/_data.sql;

/* procedures */
INPUT procs/_procs.sql;

/* views */
INPUT views/_views.sql;

/* end */
