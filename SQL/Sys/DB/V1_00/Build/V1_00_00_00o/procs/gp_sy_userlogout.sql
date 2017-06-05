--------------------------------------------------------------------------------
-- file: gp_sy_userlogout.sql
-- -----------------------------------------------------------------------------
--
SET TERM ^ ;
--
CREATE PROCEDURE gp_sy_userlogout
AS
  DECLARE VARIABLE l_iUserPK INTEGER;
BEGIN
  l_iUserPK = RDB$GET_CONTEXT ('USER_SESSION', 'user_code');

  INSERT INTO sy_tlog(
    sytlog_fuser,
    sytlog_isessionid,
    sytlog_ttimestamp,                           
    sytlog_itype,
    sytlog_vtext)
  VALUES(
    :l_iUserPK,
    1,
    current_timestamp,
    2,
    'Uzivatel odhlasen'
    );

  -- nothing after logout...
  RDB$SET_CONTEXT ('USER_SESSION', 'user_code', null);
END
^
--
SET TERM ; ^
