--------------------------------------------------------------------------------
-- Soubor: sp_sylogtype.sql
-- Popis:  Ulozene procedury pro tabulku sy_logtype
--
-- ----------------------------------------------------------------------------- 
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_sylogtpgetdescription
-- -----------------------------------------------------------------------------
-- Popis: Vrati popis z tabulky "sy_logtype" dle predaneho pk
--   a_iErrorCode ... primarni klic zaznamu    
--   ret INT      ... den v tydnu 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sylogtpgetdescription(a_iErrorCode INTEGER) 
 RETURNS (a_vDesctiption VARCHAR(1000))
AS
BEGIN
  SELECT sylogtp_description
    FROM sy_logtype
   WHERE sylogtp_pk = :a_iErrorCode    
    INTO a_vDesctiption;
END^
-- end procedure sp_sylogtpgetdescription-----------------------------------------
--
SET TERM ; ^
