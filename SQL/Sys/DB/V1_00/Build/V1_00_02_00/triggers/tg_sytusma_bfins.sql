
CREATE TRIGGER tg_sytusma_bfins
  FOR sy_tusermodavail
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytusma_pk IS NULL) THEN
    NEW.sytusma_pk = GEN_ID (gn_sytusma, 1);
END
^
