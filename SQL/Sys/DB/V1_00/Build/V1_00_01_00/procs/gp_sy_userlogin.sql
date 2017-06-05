--------------------------------------------------------------------------------
-- file: gp_sy_userlogin.sql
--------------------------------------------------------------------------------
--
--
CREATE OR ALTER PROCEDURE gp_sy_userlogin
(
  a_vIdent          VARCHAR (50),   -- prihlasovaci jmeno uzivatele
  a_vPassword       VARCHAR (100),  -- heslo
  a_sText           VARCHAR (2000)  -- text do logu
)
RETURNS (
  a_iLoginRes INTEGER,
  a_iUserPK INTEGER,
  a_iSessionID INTEGER
)
AS
  DECLARE VARIABLE v_iUserPK INTEGER;
  DECLARE VARIABLE v_iCnt INTEGER;
  DECLARE VARIABLE v_iSessionID INTEGER;
BEGIN
  a_iLoginRes = -1;
  a_iUserPK = 0;
  v_iSessionID = 0;

  -- pripadne nejdrive odhlaseni
  v_iUserPK = RDB$GET_CONTEXT ('USER_SESSION', 'user_code');
  if (v_iUserPK is not null) then
  begin
    execute procedure gp_sy_userlogout;
  end

  -- je uzivatel registrovan?
  SELECT count(*)
    FROM sy_tuser
   WHERE sytusr_vident = :a_vIdent AND
         sytusr_vpassword = :a_vPassword
    INTO v_iCnt;

  -- uzivatel je overen
  if (v_iCnt > 0) then
  begin
    -- vytazeni PK uzivatele
    SELECT sytusr_pk
      FROM sy_tuser
     WHERE sytusr_vident = :a_vIdent AND
           sytusr_vpassword = :a_vPassword
      INTO v_iUserPK;

    v_iSessionID = GEN_ID (gn_sy_sessionid, 1);

    -- zalogovani do tabulky sy_tlog
    INSERT INTO sy_tlog (
        sytlog_fuser,
        sytlog_isessionid,
        sytlog_ttimestamp,                           
        sytlog_itype,
        sytlog_vtext)
      VALUES (
        :v_iUserPK,
        :v_iSessionID,
        current_timestamp,
        1,
        :a_sText
      );
      
    a_iLoginRes = 0;
    a_iUserPK = v_iUserPK;
    a_iSessionID = v_iSessionID;

    -- ulozeni do session promenne
    RDB$SET_CONTEXT ('USER_SESSION', 'user_code', v_iUserPK);
    RDB$SET_CONTEXT ('USER_SESSION', 'loginsession', v_iSessionID);
  end
END
^
--
