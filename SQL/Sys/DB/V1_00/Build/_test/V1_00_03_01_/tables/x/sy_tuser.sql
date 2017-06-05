CREATE TABLE SY_TUSER 
(
    sytusr_pk           ND_CODE NOT NULL,
    sytusr_vident       ND_TEXT NOT NULL,
    sytusr_vfirstname   ND_TEXT,
    sytusr_vsecname     ND_TEXT NOT NULL,
    sytusr_dcreatedate  ND_DATE NOT NULL,
    sytusr_vemail       ND_WWW,
    sytusr_vphone       ND_SHORTTEXT,
    sytusr_vpassword    ND_SHORTTEXT
);

/* Primary Keys */
ALTER TABLE sy_tuser 
  ADD CONSTRAINT pk_sy_tuser PRIMARY KEY (sytusr_pk);


/* Indices */
CREATE UNIQUE INDEX ui_sytusr_fstsecname 
  ON sy_tuser (sytusr_vfirstname, sytusr_vsecname);

/* Generator */
CREATE GENERATOR gn_sytuser;
SET GENERATOR gn_sytuser TO 1;


/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_sytuser FOR sy_tuser
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytusr_pk IS NULL) THEN
    NEW.sytusr_pk = GEN_ID(gn_sytuser,1);
END
^

SET TERM ; ^

/* Descriptions */
COMMENT ON TABLE sy_tuser IS 
 'Seznam uzivatelu';

/* Privileges */
GRANT ALL ON sy_tuser 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_tuser 
  TO noe WITH GRANT OPTION;  
