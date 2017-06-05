
echo tr_sytst_bfins

CREATE TRIGGER tr_sytst_bfins
  FOR sy_tstate
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytst_pk IS NULL) THEN
    NEW.sytst_pk = GEN_ID (gn_sytst, 1);
END
^
