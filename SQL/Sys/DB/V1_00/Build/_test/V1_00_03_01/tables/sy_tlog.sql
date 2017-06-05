CREATE TABLE sy_tlog 
(
    sytlog_pk          ND_CODE NOT NULL,
    sytlog_fuser       ND_CODE NOT NULL,
    sytlog_isessionid  ND_INT NOT NULL,
    sytlog_ttimestamp  ND_TIMESTAMP NOT NULL,
    sytlog_itype       ND_INT NOT NULL,
    sytlog_vtext       ND_TEXT
);


/* Primary Keys */
ALTER TABLE sy_tlog 
  ADD CONSTRAINT pk_sytlog PRIMARY KEY (sytlog_pk);


/* Generator */
CREATE GENERATOR gn_sytlog;
SET GENERATOR gn_sytlog TO 1;

/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_sytlog FOR sy_tlog
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytlog_pk IS NULL) THEN
    NEW.sytlog_pk = GEN_ID(gn_sytlog,1);
END
^

SET TERM ; ^

/* Descriptions */
COMMENT ON TABLE sy_tlog IS 
  'Logovani';

/* Privileges */
GRANT ALL ON sy_tlog 
  TO noe WITH GRANT OPTION;
