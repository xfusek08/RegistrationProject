CREATE TABLE sy_tdbconfig 
(
    sytdbconfig_pk      ND_CODE NOT NULL,
    sytdbconfig_ftype   ND_CODE NOT NULL,
    sytdbconfig_ivalue  ND_INT
);


/* Unique Constraints */
ALTER TABLE sy_tdbconfig 
  ADD CONSTRAINT ui_sytdbconfig UNIQUE (sytdbconfig_ftype)
  USING INDEX ui_sy_tdbconfig;


/* Primary Keys */
ALTER TABLE sy_tdbconfig 
  ADD CONSTRAINT pk_sy_tdbconfig PRIMARY KEY (sytdbconfig_pk);


/* Foreign Keys */
ALTER TABLE sy_tdbconfig 
  ADD CONSTRAINT fk_sytdbconfig FOREIGN KEY (sytdbconfig_ftype) 
  REFERENCES sy_cdbconfigtype (sycdbconfigtype_pk);


/* Generator*/
CREATE GENERATOR gn_sytdbconfig;
SET GENERATOR gn_sytdbconfig TO 1;


/* Triggers*/
SET TERM ^ ;

CREATE TRIGGER tg_sytdbconfig FOR sy_tdbconfig
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytdbconfig_pk IS NULL) THEN
    NEW.sytdbconfig_pk = GEN_ID(gn_sytdbconfig,1);
END
^

SET TERM ; ^



/* Descriptions */
COMMENT ON TABLE sy_tdbconfig IS 
 'Nastaveni databaze, konstanty,...';


/* Privileges */
GRANT ALL ON sy_tdbconfig 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_tdbconfig 
  TO noe WITH GRANT OPTION;  
