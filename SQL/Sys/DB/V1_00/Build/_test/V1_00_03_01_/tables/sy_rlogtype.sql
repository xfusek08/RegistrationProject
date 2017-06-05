CREATE TABLE sy_rlogtype 
(
    syrlogtp_pk            ND_CODE NOT NULL,
    syrlogtp_vdescription  ND_DESCRIPTION
);


/* Primary Keys */
ALTER TABLE sy_rlogtype 
  ADD CONSTRAINT pk_syrlogtp PRIMARY KEY (syrlogtp_pk);

/* Generator*/
CREATE GENERATOR gn_syrlogtype;
SET GENERATOR gn_syrlogtype TO 1;

/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_syrlogtype FOR sy_rlogtype
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.syrlogtp_pk IS NULL) THEN
    NEW.syrlogtp_pk = GEN_ID(gn_syrlogtype,1);
END
^

SET TERM ; ^


/* Descriptions */
COMMENT ON TABLE sy_rlogtype IS 
  'Ciselnik druhu logovani';

/* Privileges */
GRANT ALL ON sy_rlogtype 
  TO noe WITH GRANT OPTION;
