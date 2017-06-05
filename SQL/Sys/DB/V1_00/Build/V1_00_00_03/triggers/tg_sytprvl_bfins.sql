
CREATE TRIGGER tg_sytprvl_bfins
  FOR sy_tprofvalue
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytprvl_pk IS NULL) THEN
    NEW.sytprvl_pk = GEN_ID (gn_sytprvl, 1);
END
^
