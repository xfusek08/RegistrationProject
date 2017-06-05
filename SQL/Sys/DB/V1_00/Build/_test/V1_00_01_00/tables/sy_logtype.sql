--------------------------------------------------------------------------------
-- Soubor: sy_logtype.sql
-- Popis:  Vytvoreni tabulky sy_logtype
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--------------------------------------------------------------------------------
--
--DROP TABLE sy_logtype;
--
CREATE TABLE sy_logtype 
(
  sylogtp_pk           INTEGER NOT NULL,
  sylogtp_description  VARCHAR(1000) CHARACTER SET WIN1250,
  PRIMARY KEY (sylogtp_pk)
);
--
--DELETE FROM RDB$GENERATORS WHERE RDB$GENERATOR_NAME = gn_sylogtp;
CREATE GENERATOR gn_sylogtp;
