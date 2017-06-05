
CREATE TRIGGER tg_sytprof_bfins
  FOR sy_tprofile
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytprof_pk IS NULL) THEN
    NEW.sytprof_pk = GEN_ID (gn_sytprof, 1);
END
^
