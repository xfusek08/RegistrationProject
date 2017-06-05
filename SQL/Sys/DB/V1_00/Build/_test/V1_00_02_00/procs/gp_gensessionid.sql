--------------------------------------------------------------------------------
-- file: sp_genid.sql
--
CREATE GENERATOR gn_sessionid;
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_getsessionid
-- -----------------------------------------------------------------------------
-- Popis: Vraceni hodnoty generatoru sp_getsessionid - 1++
--
--   return INT:    ... co asi...
--------------------------------------------------------------------------------
--
CREATE PROCEDURE gp_getsessionid
 RETURNS (a_iGenID INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iGenID = gen_id (gn_sessionid,1);
END ^
--
-- end procedure sp_getsessionid --------------------------------------------------
--
