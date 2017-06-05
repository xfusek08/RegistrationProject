--------------------------------------------------------------------------------
-- Soubor: sp_sytlog.sql
-- Popis:  Ulozene procedury pro tabulku sy_log
-- 
-- -----------------------------------------------------------------------------
-- TODO: dodelat check na a_iUser a a_iType -> check (select from ... ?)
--------------------------------------------------------------------------------
--
SET TERM ^ ;
--
--------------------------------------------------------------------------------
-- sp_sytlogins
-- -----------------------------------------------------------------------------
-- Popis: insert do tabulky "sy_tlog" 
--
--   return: INT    ... pk noveho zaznamu
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytlogins 
(
 a_iUser        INTEGER,        -- ID uzivatele
 a_iSessionId   INTEGER,        -- session ID databaze
 a_iType        INTEGER,        -- typ zaznamu
 a_sText        VARCHAR(1000)   -- popis zaznamu 
)
 RETURNS (a_iPK INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iPK = gen_id (gn_sylog, 1);
  -- insert do tabulky gn_sylog
  INSERT INTO sy_tlog (
           sytlog_pk,
           sytlog_fuser,
           sytlog_isessionid,
           sytlog_ttimestamp,
           sytlog_itype,
           sytlog_vtext) 
    VALUES (
           :a_iPK,
           :a_iUser,
           :a_iSessionId,
           'NOW',
           :a_iType,
           :a_sText);  
END^
-- end procedure sp_sytlogins ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_sytlogupd
-- -----------------------------------------------------------------------------
-- Popis: update dat nad tabulkou "sy_tlog"
--
--   return: neni
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytlogupd
(
 a_iPK          INTEGER,        -- PK meneneho zaznamu
 a_iUser        INTEGER,        -- ID uzivatele
 a_iSessionId   INTEGER,        -- session ID databaze
 a_iType        INTEGER,        -- typ zaznamu
 a_sText        VARCHAR(1000)   -- popis zaznamu
) 
AS
BEGIN
  -- update zaznamu 
  UPDATE sy_tlog 
     SET sytlog_fuser      =: a_iUser,
         sytlog_isessionid =: a_iSessionId,
         sytlog_itype      =: a_iType,
         sytlog_vtext      =: a_sText
   WHERE sytlog_pk = :a_iPK;     
END ^
-- end procedure sp_sytlogupd ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_sylogupd
-- -----------------------------------------------------------------------------
-- Popis: smazani zaznamu v tabulce "sy_tlog"
--
--   return: neni
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytlogdel
(
 a_iPK INTEGER        -- PK meneneho zaznamu
)
AS
BEGIN
  DELETE FROM sy_tlog
   WHERE sytlog_pk = :a_iPK;     
END ^
-- end procedure sp_sytlogdel ---------------------------------------------------
--
--------------------------------------------------------------------------------
-- sp_syloggetgenid
-- -----------------------------------------------------------------------------
-- Popis: Vraceni aktualniho stavu generatoru gn_sylog
--
--   return INT:    ... co asi...
--------------------------------------------------------------------------------
--
CREATE PROCEDURE sp_sytloggetgenid
 RETURNS (a_iGenID INTEGER)
AS
BEGIN
  -- zjisteni id generatoru
  a_iGenID = gen_id (gn_sytlog, 0);
END ^
-- end procedure sp_sytloggetgenid-----------------------------------------------
--
SET TERM ; ^
