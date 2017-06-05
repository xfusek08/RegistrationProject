CREATE TABLE sy_cmimetype 
(
    sycmtype_apptype    ND_TEXT,
    sycmtype_extension  ND_SHORTTEXT
);

/* Unique Constraints */
ALTER TABLE sy_cmimetype
  ADD CONSTRAINT ui_sycmtype_ext 
    UNIQUE (sycmtype_extension);

/* Descriptions */
COMMENT ON TABLE sy_cmimetype IS 
 'popis tabulky';

COMMENT ON COLUMN sy_cmimetype.sycmtype_apptype
  IS 'Typ aplikace';

COMMENT ON COLUMN sy_cmimetype.sycmtype_extension
  IS 'Pripona souboru';  

/* Privileges */
GRANT SELECT ON sy_cmimetype 
  TO noe WITH GRANT OPTION;  
