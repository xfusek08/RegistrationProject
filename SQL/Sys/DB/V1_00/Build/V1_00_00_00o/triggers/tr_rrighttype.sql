/* Triggers */
SET TERM ^ ;

CREATE TRIGGER tg_syrrighttype FOR sy_rrighttype
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.syrrtype_pk IS NULL) THEN
    NEW.syrrtype_pk = GEN_ID(gn_rrighttype,1);
END
^

SET TERM ; ^
