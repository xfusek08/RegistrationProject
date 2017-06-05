--------------------------------------------------------------------------------
-- Soubor: sp_syrlogtype.sql
-- Popis:  Ulozene procedury pro tabulku sy_logtype
--
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_syrlogtpgetdescription
-- -----------------------------------------------------------------------------
-- Popis: Vrati popis z tabulky "sy_logtype" dle predaneho pk
--   ret INT      ... den v tydnu 
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_syrlogtpgetdescription
(
 a_iErrorCode INTEGER       -- primarni klic zaznamu    
) 
 RETURNS (a_vDesctiption VARCHAR(1000))
AS
BEGIN
  SELECT syrlogtp_vdescription
    FROM sy_rlogtype
   WHERE syrlogtp_pk = :a_iErrorCode    
    INTO a_vDesctiption;
END^
-- end procedure sp_syrlogtpgetdescription-----------------------------------------
--
SET TERM ; ^
