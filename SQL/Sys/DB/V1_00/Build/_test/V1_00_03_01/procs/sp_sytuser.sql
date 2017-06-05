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
SET TERM ; ^
