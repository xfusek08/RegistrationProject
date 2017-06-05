CREATE TABLE sy_rrighttype 
(
    syrrighttype_pk    ND_CODE NOT NULL,
    syrrighttype_name  ND_TEXT NOT NULL
);


/* Primary Keys */
ALTER TABLE sy_rrighttype 
  ADD CONSTRAINT pk_sy_rrighttype PRIMARY KEY (syrrighttype_pk);

/* Generator */
CREATE GENERATOR gn_rrighttype;
SET GENERATOR gn_rrighttype TO 1;

/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_syrrighttype FOR sy_rrighttype
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.syrrighttype_pk IS NULL) THEN
    NEW.syrrighttype_pk = GEN_ID(gn_rrighttype,1);
END
^

SET TERM ; ^

/* Descriptions */
COMMENT ON TABLE sy_rrighttype IS 
 'Seznam prav';


/* Privileges */
GRANT ALL ON sy_rrighttype 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_rrighttype 
  TO noe WITH GRANT OPTION;  
