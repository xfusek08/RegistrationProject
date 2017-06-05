/* Triggers */

CREATE TRIGGER tg_syrlogtype FOR sy_rlogtype
ACTIVE BEFORE INSERT POSITION 0
AS
BEGIN
  IF (NEW.syrlogtp_pk IS NULL) THEN
    NEW.syrlogtp_pk = GEN_ID(gn_syrlogtype,1);
END
^
