--------------------------------------------------------------------------------
-- file: gp_sy_genersessionid.sql
--
SET TERM ^ ;
--
CREATE PROCEDURE gp_sy_genersessionid
 RETURNS (a_iGenID INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iGenID = gen_id (gn_sy_sessionid, 1);
END ^
--
SET TERM ;^
