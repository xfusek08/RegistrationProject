CREATE TABLE sy_cdbconfigtype 
(
    sycdbconfigtype_pk     ND_CODE NOT NULL,
    sycdbconfigtype_icode  ND_INT NOT NULL,
    sycdbconfigtype_vname  ND_TEXT
);


/* Unique Constraints */
ALTER TABLE sy_cdbconfigtype 
  ADD CONSTRAINT ui_sycdbconfigtype UNIQUE (sycdbconfigtype_icode);

/* Primary Keys */
ALTER TABLE sy_cdbconfigtype 
  ADD CONSTRAINT pk_sycdbconfigtype PRIMARY KEY (sycdbconfigtype_pk);

/* Generator */
CREATE GENERATOR gn_sycdbconfigtype;
SET GENERATOR gn_sycdbconfigtype TO 1;

/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_sycdbconfigtype FOR sy_cdbconfigtype
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sycdbconfigtype_pk IS NULL) THEN
    NEW.sycdbconfigtype_pk = GEN_ID(gn_sycdbconfigtype,1);
END
^

SET TERM ; ^

/* Descriptions */
COMMENT ON TABLE sy_cdbconfigtype IS 
 'Ciselnik typu pro sy_tdbconfig';

/* Privileges */

/* Privileges of users */
GRANT ALL ON sy_cdbconfigtype 
  TO noe WITH GRANT OPTION;
