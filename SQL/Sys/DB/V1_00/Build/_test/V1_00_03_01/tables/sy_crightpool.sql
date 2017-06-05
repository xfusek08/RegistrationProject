CREATE TABLE sy_crightpool 
(
    sycrightpool_pk     ND_CODE NOT NULL,
    sycrightpool_vname  ND_TEXT NOT NULL,
    sycrightpool_imin   ND_INT NOT NULL,
    sycrightpool_imax   ND_INT
);


/* Primary Keys */
ALTER TABLE sy_crightpool 
  ADD CONSTRAINT pk_sycrightpool PRIMARY KEY (sycrightpool_pk);


/* Generator */
CREATE GENERATOR gn_sycrightpool;
SET GENERATOR gn_sycrightpool TO 1;

/* Triggers */
SET TERM ^ ;

CREATE TRIGGER TR_SYCRIGHTPOOL FOR SY_CRIGHTPOOL
ACTIVE BEFORE INSERT OR UPDATE POSITION 0
AS
  declare variable a_iMaxPool INTEGER ;
  -- TODO neexistuje "old.sloupec"?
  -- TODO pejmenovat __MAX_POOL na rozsah
begin
  -- max hodnota poolu neni vyplnena
  if (new.sycrightpool_imax is null ) then
    begin
      -- vytazeni hodnoty z DB_CONFIG
      SELECT sytdbconfig_ivalue
        FROM sy_tdbconfig c,
             sy_cdbconfigtype t
       WHERE t.sycdbconfigtype_pk = c.sytdbconfig_ftype AND
             t.sycdbconfigtype_vname = '__MAX_POOL'
        INTO a_iMaxPool;

      if (a_iMaxPool is null) then
        a_iMaxPool = 1000;

      -- nasetovani rozsahu
      new.sycrightpool_imax = new.sycrightpool_imin + a_iMaxPool;
      
    END
end
^

SET TERM ; ^

/* Descriptions */
COMMENT ON TABLE sy_crightpool IS 
  'Ciselnik rozsahu metod aplikaci';

/* Privileges */
GRANT ALL ON sy_crightpool 
  TO sysdba WITH GRANT OPTION;
  
GRANT SELECT ON sy_crightpool 
  TO noe WITH GRANT OPTION;  
