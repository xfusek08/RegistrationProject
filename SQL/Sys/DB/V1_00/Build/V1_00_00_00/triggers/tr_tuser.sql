/* Triggers */

CREATE TRIGGER tg_sytuser FOR sy_tuser
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytusr_pk IS NULL) THEN
    NEW.sytusr_pk = GEN_ID(gn_sytuser,1);
END
^
