--------------------------------------------------------------------------------
-- file: gp_sy_userlogin.sql
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
CREATE OR ALTER PROCEDURE gp_sy_userlogin
(
 a_vIdent          VARCHAR(50), -- prihlasovaci jmeno uzivatele
 a_vPassword       VARCHAR(100) -- heslo
)
RETURNS (
 a_bIsLogin VARCHAR (1),
 a_iUserPK INTEGER
)
AS
  DECLARE VARIABLE l_iUserPK INTEGER;
  DECLARE VARIABLE l_iCnt INTEGER;
BEGIN
  a_bIsLogin = '0';
  a_iUserPK = 0;

  -- je uzivatel registrovan?
  SELECT count(*)
    FROM sy_tuser
   WHERE sytusr_vident = :a_vIdent AND
         sytusr_vpassword = :a_vPassword
    INTO l_iCnt;

  -- uzivatel je overen
  if (l_iCnt > 0) then
  begin
    a_bIsLogin = '1';
    -- vytazeni PK uzivatele
    SELECT sytusr_pk
      FROM sy_tuser
     WHERE sytusr_vident = :a_vIdent AND
           sytusr_vpassword = :a_vPassword
      INTO l_iUserPK;

    -- zalogovani do tabulky sy_tlog
    INSERT INTO sy_tlog (
       sytlog_fuser,
       sytlog_isessionid,
       sytlog_ttimestamp,                           
       sytlog_itype,
       sytlog_vtext)
      VALUES (
       :l_iUserPK,
       1,
       current_timestamp,
       1,
       'Uzivatel prihlasen'
      );
      
    a_iUserPK = l_iUserPK;
  end

  -- ulozeni do session promenne
  RDB$SET_CONTEXT ('USER_SESSION', 'user_code', l_iUserPK);
END ^
--
SET TERM ;^
