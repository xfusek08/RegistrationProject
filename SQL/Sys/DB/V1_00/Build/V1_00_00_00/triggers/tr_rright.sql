/* Triggers */

CREATE TRIGGER tg_sytright FOR sy_tright
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.sytright_pk IS NULL) THEN
    NEW.sytright_pk = GEN_ID(gn_rright,1);
END
^
