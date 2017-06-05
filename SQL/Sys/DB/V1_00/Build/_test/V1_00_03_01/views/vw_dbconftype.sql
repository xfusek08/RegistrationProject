CREATE VIEW vw_dbconftype
(
    SYTDBCONFIG_PK,
    SYCDBCONFIGTYPE_VNAME,
    SYTDBCONFIG_IVALUE,
    SYCDBCONFIGTYPE_ICODE
)
 AS
   SELECT 
       c.sytdbconfig_pk,
       t.sycdbconfigtype_vname,
       c.sytdbconfig_ivalue,
       t.sycdbconfigtype_icode
     FROM sy_tdbconfig c
     LEFT JOIN sy_cdbconfigtype t ON (t.sycdbconfigtype_pk=c.sytdbconfig_ftype);

/* Descriptions */
COMMENT ON VIEW VW_DBCONFTYPE IS 
  'Popis';

/* Privileges */
GRANT ALL ON sy_tright 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_tright 
  TO noe WITH GRANT OPTION;  
