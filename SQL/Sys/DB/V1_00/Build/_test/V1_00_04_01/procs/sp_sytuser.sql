--------------------------------------------------------------------------------
-- Soubor: sp_sytuser.sql
-- Popis:  Ulozene procedury pro tabulku sy_user
-- -----------------------------------------------------------------------------
-- TODO: hash pro heslo. Zatim je ukladano jako VARCHAR
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_sytuserins
-- -----------------------------------------------------------------------------
-- Popis: insert do tabulky "sy_tuser" 
--
--   return: INT    ... pk noveho zaznamu
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytuserins
( 
  a_vIdent          VARCHAR(50),  -- prihlasovaci jmeno uzivatele
 a_vFirstName      VARCHAR(50),  -- krestni jmeno
 a_vSecName        VARCHAR(50),  -- prijmeni
 a_vEmail          VARCHAR(100), -- emailova adresa
 a_vPhone          VARCHAR(20),  -- telefon
 a_vPassword       VARCHAR(100)  -- heslo (hash code)
)  
RETURNS(a_iPK INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iPK = gen_id (gn_sytuser, 1);
  -- insert do tabulky sy_user
  INSERT INTO sy_tuser (
           sytusr_pk,
           sytusr_vident,
           sytusr_vfirstname,
           sytusr_vsecname,
           sytusr_dcreatedate,
           sytusr_vemail,
           sytusr_vphone,
           sytusr_vpassword) 
    VALUES (
           :a_iPK,
           :a_vIdent,
           :a_vFirstName,
           :a_vSecName,
           'NOW',
           :a_vEmail,
           :a_vPhone,
           :a_vPassword);      
END^
-- end procedure sp_syuserins ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syuserupd
-- -----------------------------------------------------------------------------
-- Popis: update tabulky "sy_user" 
--
--   return: neni
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytuserupd
(
 a_iPK             INTEGER,     -- PK zaznamu 
 a_vIdent          VARCHAR(50), -- prihlasovaci jmeno uzivatele
 a_vFirstName      VARCHAR(50), -- krestni jmeno
 a_vSecName        VARCHAR(50), -- prijmeni
 a_vEmail          VARCHAR(100),-- emailova adresa 
 a_vPhone          VARCHAR(20), -- telefon 
 a_vPassword       VARCHAR(100) -- heslo (hash code)
) 
AS
BEGIN
  UPDATE sy_tuser 
     SET sytusr_vident      =: a_vIdent,
         sytusr_vfirstname  =: a_vFirstName,
         sytusr_vsecname    =: a_vSecName,
         sytusr_vemail      =: a_vEmail,
         sytusr_vphone      =: a_vPhone,
         sytusr_vpassword   =: a_vPassword
   WHERE sytusr_pk =: a_iPK;     
END ^
-- end procedure sp_syuserupd --------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syuserdel
-- -----------------------------------------------------------------------------
-- Popis: smazani zaznamu z tabulky "sy_user" 
--
--   return: neni
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytuserdel
(
 a_iPK INTEGER     -- PK zaznamu 
) 
AS
BEGIN
  DELETE FROM sy_tuser
   WHERE sytusr_pk =: a_iPK;     
END^
-- end procedure sp_syuserdel --------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syusergetgenid
-- -----------------------------------------------------------------------------
-- Popis: Vraceni aktualniho stavu generatoru gn_syuser
--
--   return INT:    ... co asi...
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytusergetgenid
 RETURNS (a_iGenID INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iGenID = gen_id (gn_sytuser,0);
END ^
--
-- end procedure sp_syusergetgenid ---------------------------------------------
--
--
--------------------------------------------------------------------------------
-- sp_sytuserlogin
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytuserlogin
ALTER PROCEDURE sp_sytuserlogin
(
 a_vIdent          VARCHAR(50), -- prihlasovaci jmeno uzivatele
 a_vPassword       VARCHAR(100) -- heslo
)
 RETURNS (a_bIsLogin VARCHAR(1))
AS
  DECLARE VARIABLE l_iUserPK INTEGER;
  DECLARE VARIABLE l_iCnt INTEGER;
BEGIN
  a_bIsLogin = '0';

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
      
    RDB$SET_CONTEXT ('USER_SESSION', 'user_code', l_iUserPK);
  end
END
^
--
-- end procedure sp_sytuserlogin ---------------------------------------------
--
--
--------------------------------------------------------------------------------
-- sp_sytuserunlogin
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytuserlogout
ALTER PROCEDURE sp_sytuserlogout
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
END
^
--
-- end procedure sp_sytuserunlogin ---------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_sytusergetpkfromident
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytusergetpkfromident
(
 a_vIdent          VARCHAR(50)
)
 RETURNS (l_iUserPK INTEGER)
AS
BEGIN
  SELECT sytusr_pk
    FROM sy_tuser
    WHERE sytusr_vident = :a_vIdent
    INTO l_iUserPK;
END ^
--
-- end procedure sp_sytusergetpkfromident ---------------------------------------------
--
SET TERM ; ^
