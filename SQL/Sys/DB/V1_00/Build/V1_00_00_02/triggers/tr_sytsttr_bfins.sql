
echo tr_sytsttr_bfins

CREATE TRIGGER tr_sytsttr_bfins
  FOR sy_tstatetrans
  ACTIVE
  BEFORE INSERT
  POSITION 0
AS
BEGIN
  IF (NEW.sytsttr_pk IS NULL) THEN
    NEW.sytsttr_pk = GEN_ID (gn_sytsttr, 1);
END
^
