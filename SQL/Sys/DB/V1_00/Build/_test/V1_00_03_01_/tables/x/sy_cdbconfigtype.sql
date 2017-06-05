
--#prompt#sy_cconfigtype

CREATE TABLE sy_cconfigtype
(
  syccfgtp_pk     ND_CODE NOT NULL,
  syccfgtp_icode  ND_INT NOT NULL,
  syccfgtp_vname  ND_TEXT
);

/* Primary Keys */
ALTER TABLE sy_cdbconfigtype 
  ADD CONSTRAINT pk_syccfgtp_pk
    PRIMARY KEY (syccfgtp_pk);

/* Unique Constraints */
ALTER TABLE sy_cdbconfigtype 
  ADD CONSTRAINT ui_syccfgtp_icode
    UNIQUE (sycdbconfigtype_icode);

/* Generator */
???? CREATE GENERATOR gn_sy_filecounter;

CREATE GENERATOR gn_syccfgtp;
SET GENERATOR gn_syccfgtp TO 1;

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

comment on column ...
  syccfgtp_pk     ND_CODE NOT NULL,
  syccfgtp_icode  ND_INT NOT NULL,
  syccfgtp_vname  ND_TEXT

/* Privileges */

/* Privileges of users */
????
GRANT ALL ON sy_cdbconfigtype 
  TO noe WITH GRANT OPTION;

&fdfsd.
