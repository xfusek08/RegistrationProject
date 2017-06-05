CREATE TABLE sy_tright
(
    sytright_pk       ND_CODE NOT NULL,
    sytright_fsyuser  ND_CODE NOT NULL,
    sytright_ftype    ND_CODE,
    sytright_bstatus  ND_BOOL DEFAULT '0',
    sytright_fpool    ND_CODE
);

/* Primary Keys */
ALTER TABLE sy_tright 
  ADD CONSTRAINT pk_sy_tright PRIMARY KEY (sytright_pk);

/* Foreign Keys*/
ALTER TABLE sy_tright 
  ADD CONSTRAINT fk_sytrightpool FOREIGN KEY (sytright_fpool) 
  REFERENCES sy_crightpool (sycrightpool_pk);
  
ALTER TABLE sy_tright 
  ADD CONSTRAINT fk_sy_ttype FOREIGN KEY (sytright_ftype) 
  REFERENCES sy_rrighttype (syrrighttype_pk);

/* Generator */
CREATE GENERATOR gn_rright;
SET GENERATOR gn_rright TO 1;

/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_sytright FOR sy_tright
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytright_pk IS NULL) THEN
    NEW.sytright_pk = GEN_ID(gn_rright,1);
END
^

SET TERM ; ^



/* Descriptions */
COMMENT ON TABLE sy_tright IS 
'Opravneni';

/* Privileges*/
GRANT ALL ON sy_tright 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_tright 
  TO noe WITH GRANT OPTION;  
