--------------------------------------------------------------------------------
-- file: gp_sy_userlogout.sql
-- -----------------------------------------------------------------------------
--
--
CREATE OR ALTER PROCEDURE gp_sy_userlogout
AS
  DECLARE VARIABLE v_iUserPK INTEGER;
  DECLARE VARIABLE v_iSessionID INTEGER;
BEGIN
  v_iUserPK = RDB$GET_CONTEXT ('USER_SESSION', 'user_code');
  v_iSessionID = RDB$GET_CONTEXT ('USER_SESSION', 'loginsession');

  INSERT INTO sy_tlog(
    sytlog_fuser,
    sytlog_isessionid,
    sytlog_ttimestamp,                           
    sytlog_itype)
  VALUES(
    :v_iUserPK,
    :v_iSessionID,
    current_timestamp,
    2
    );

  -- nothing after logout...
  RDB$SET_CONTEXT ('USER_SESSION', 'user_code', null);
  RDB$SET_CONTEXT ('USER_SESSION', 'loginsession', null);
END
^
--
