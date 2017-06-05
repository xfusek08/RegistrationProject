--------------------------------------------------------------------------------
-- Soubor: sp_sylog.sql
-- Popis:  Ulozene procedury pro tabulku sy_log
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--
-- TODO: dodelat check na a_iUser a a_iType -> check (select from ... ?)
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_sylogins
-- -----------------------------------------------------------------------------
-- Popis: insert do tabulky "sy_log" 
--   a_iUser        ... ID uzivatele,
--   a_iSessionId   ... session ID databaze,
--   a_iType        ... typ zaznamu,
--   a_sText        ... popis zaznamu
--
--   return: INT    ... pk noveho zaznamu
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sylogins 
(
 a_iUser        INTEGER,
 a_iSessionId   INTEGER,
 a_iType        INTEGER,
 a_sText        VARCHAR(1000)
)
 RETURNS (a_iPK INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iPK = gen_id (gn_sylog, 1);
  -- insert do tabulky gn_sylog
  INSERT INTO sy_log (sylog_pk,sylog_user,sylog_sessionid,
                        sylog_timestamp,sylog_type,sylog_text) 
  VALUES (:a_iPK,:a_iUser,:a_iSessionId,'NOW',:a_iType,:a_sText);  
END^
-- end procedure sp_sylogins ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_sylogupd
-- -----------------------------------------------------------------------------
-- Popis: update dat nad tabulkou "sy_log"
--   a_iPK          ... PK meneneho zaznamu
--   a_iUser        ... ID uzivatele,
--   a_iSessionId   ... session ID databaze,
--   a_iType        ... typ zaznamu,
--   a_sText        ... popis zaznamu
--
--   return: neni
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sylogupd
(
 a_iPK          INTEGER,  
 a_iUser        INTEGER,
 a_iSessionId   INTEGER,
 a_iType        INTEGER,
 a_sText        VARCHAR(1000)
) 
AS
BEGIN
  -- update zaznamu 
  UPDATE sy_log 
     SET sylog_user      =: a_iUser,
         sylog_sessionid =: a_iSessionId,
         sylog_type      =: a_iType,
         sylog_text      =: a_sText
   WHERE sylog_pk = :a_iPK;     
END ^
-- end procedure sp_sylogupd ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_sylogupd
-- -----------------------------------------------------------------------------
-- Popis: smazani zaznamu v tabulce "sy_log"
--   a_iPK          ... PK meneneho zaznamu
--
--   return: neni
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sylogdel(a_iPK INTEGER)
AS
BEGIN
  DELETE FROM sy_log
   WHERE sylog_pk = :a_iPK;     
END ^
-- end procedure sp_sylogdel ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syloggetgenid
-- -----------------------------------------------------------------------------
-- Popis: Vraceni aktualniho stavu generatoru gn_sylog
--
--   return INT:    ... co asi...
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005  Sperl  Uvodni verze
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_syloggetgenid
 RETURNS (a_iGenID INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iGenID = gen_id (gn_sylog, 0);
END ^
-- end procedure sp_syloggetgenid-----------------------------------------------
--
SET TERM ; ^
