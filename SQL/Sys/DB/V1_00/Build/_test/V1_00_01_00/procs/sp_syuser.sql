--------------------------------------------------------------------------------
-- Soubor: sp_syuser.sql
-- Popis:  Ulozene procedury pro tabulku sy_user
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--
-- TODO: hash pro heslo. Zatim je ukladano jako VARCHAR
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_syuserins
-- -----------------------------------------------------------------------------
-- Popis: insert do tabulky "sy_user" 
--   a_vIdent          ... prihlasovaci jmeno uzivatele
--   a_vFirstName      ... krestni jmeno
--   a_vSecName        ... prijmeni
--   a_vEmail          ... emailova adresa
--   a_vPhone          ... telefon
--   a_vPassword       ... heslo (hash code)
--
--   return: INT    ... pk noveho zaznamu
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_syuserins
( 
 a_vIdent          VARCHAR(50),
 a_vFirstName      VARCHAR(50),
 a_vSecName        VARCHAR(50),
 a_vEmail          VARCHAR(100),
 a_vPhone          VARCHAR(20),
 a_vPassword       VARCHAR(100)
)  
RETURNS(a_iPK INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iPK = gen_id (gn_syuser, 1);
  -- insert do tabulky sy_user
  INSERT INTO sy_user (syusr_pk,syusr_ident,syusr_firstname,
                      syusr_secname,syusr_createdate,syusr_email,syusr_phone,syusr_password) 
  VALUES (:a_iPK,:a_vIdent,:a_vFirstName,:a_vSecName,'NOW',:a_vEmail,:a_vPhone,:a_vPassword);      
END^
-- end procedure sp_syuserins ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syuserupd
-- -----------------------------------------------------------------------------
-- Popis: update tabulky "sy_user" 
--   a_iPK             ... PK zaznamu 
--   a_vIdent          ... prihlasovaci jmeno uzivatele
--   a_vFirstName      ... krestni jmeno
--   a_vSecName        ... prijmeni
--   a_vEmail          ... emailova adresa
--   a_vPhone          ... telefon
--   a_vPassword       ... heslo (hash code)
--
--   return: neni
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_syuserupd
(
 a_iPK             INTEGER,
 a_vIdent          VARCHAR(50),
 a_vFirstName      VARCHAR(50),
 a_vSecName        VARCHAR(50),
 a_vEmail          VARCHAR(100),
 a_vPhone          VARCHAR(20),
 a_vPassword       VARCHAR(100)
) 
AS
BEGIN
  UPDATE sy_user 
     SET syusr_ident      =: a_vIdent,
         syusr_firstname  =: a_vFirstName,
         syusr_secname    =: a_vSecName,
         syusr_email      =: a_vEmail,
         syusr_phone      =: a_vPhone,
         syusr_password   =: a_vPassword
   WHERE syusr_pk =: a_iPK;     
END ^
-- end procedure sp_syuserupd --------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syuserdel
-- -----------------------------------------------------------------------------
-- Popis: smazani zaznamu z tabulky "sy_user" 
--   a_iPK            ... PK zaznamu 
--
--   return: neni
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_syuserdel(a_iPK INTEGER) 
AS
BEGIN
  DELETE FROM sy_user
   WHERE syusr_pk =: a_iPK;     
END^
-- end procedure sp_syuserdel --------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syusergetgenid
-- -----------------------------------------------------------------------------
-- Popis: Vraceni aktualniho stavu generatoru gn_syuser
--
--   return INT:    ... co asi...
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_syusergetgenid
 RETURNS (a_iGenID INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iGenID = gen_id (gn_syuser,0);
END ^
--
-- end procedure sp_syusergetgenid ---------------------------------------------
--
SET TERM ; ^
