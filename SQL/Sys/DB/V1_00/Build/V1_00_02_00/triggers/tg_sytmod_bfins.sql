
CREATE TRIGGER tg_sytmod_bfins
  FOR sy_tmodule
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytmod_pk IS NULL) THEN
    NEW.sytmod_pk = GEN_ID (gn_sytmod, 1);
END
^
