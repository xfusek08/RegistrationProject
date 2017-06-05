CREATE TABLE sy_cmimetype 
(
    sycmimetype_apptype    ND_TEXT,
    sycmimetype_extension  ND_SHORTTEXT
);


/* Unique Constraints */
ALTER TABLE sy_cmimetype
  ADD CONSTRAINT ui_sycmimetype_ext UNIQUE (sycmimetype_extension);

/* Descriptions */
COMMENT ON TABLE sy_cmimetype IS 
 'popis tabulky';

/* Privileges */
GRANT ALL ON sy_cmimetype 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_cmimetype 
  TO noe WITH GRANT OPTION;  
