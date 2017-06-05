
echo tr_sytsdgr_bfins

CREATE TRIGGER tr_sytsdgr_bfins
  FOR sy_tstatediagram
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytsdgr_pk IS NULL) THEN
    NEW.sytsdgr_pk = GEN_ID (gn_sytsdgr, 1);
END
^
