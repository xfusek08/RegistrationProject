CREATE TABLE sy_terrormsg 
(
    syterrmsg_pk     ND_CODE NOT NULL,
    syterrmsg_vtext  ND_TEXT
);


/* Primary Keys */
ALTER TABLE sy_terrormsg 
  ADD CONSTRAINT pk_syterrmsg PRIMARY KEY (syterrmsg_pk)
  USING INDEX syterrmsg_pk;

/* Generator */
CREATE GENERATOR gn_syterrmsg;
SET GENERATOR gn_syterrmsg TO 1;


/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_syterrormsg FOR sy_terrormsg
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.syterrmsg_pk IS NULL) THEN
    NEW.syterrmsg_pk = GEN_ID(gn_syterrmsg,1);
END
^

SET TERM ; ^

/* Descriptions */
COMMENT ON TABLE sy_terrormsg IS 
  'Chybove hlasky';

/* Privileges */
GRANT ALL ON sy_terrormsg 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_terrormsg 
  TO noe WITH GRANT OPTION;  
