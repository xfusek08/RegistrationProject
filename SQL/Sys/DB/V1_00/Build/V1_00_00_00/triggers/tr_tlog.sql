/* Triggers */

CREATE TRIGGER tg_sytlog FOR sy_tlog
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytlog_pk IS NULL) THEN
    NEW.sytlog_pk = GEN_ID(gn_sytlog,1);
END
^
