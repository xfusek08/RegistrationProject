--------------------------------------------------------------------------------
-- Soubor: sy_user.sql
-- Popis:  Vytvoreni tabulky sy_user
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--------------------------------------------------------------------------------
--
--DROP TABLE sy_user;
--
CREATE TABLE sy_user 
(
  syusr_pk           INTEGER NOT NULL,
  syusr_ident        VARCHAR(50) CHARACTER SET WIN1250 NOT NULL,
  syusr_firstname    VARCHAR(50) CHARACTER SET WIN1250,
  syusr_secname      VARCHAR(50) CHARACTER SET WIN1250 NOT NULL,
  syusr_createdate   DATE NOT NULL,
  syusr_email        VARCHAR(100) CHARACTER SET WIN1250,
  syusr_phone        VARCHAR(20) CHARACTER SET WIN1250,
  syusr_password     VARCHAR(100) CHARACTER SET WIN1250,
 PRIMARY KEY (syusr_pk)
);
--
CREATE UNIQUE INDEX ui_syusr_fstsecname ON SY_USER(syusr_firstname,syusr_secname);
--
--DELETE FROM RDB$GENERATORS WHERE RDB$GENERATOR_NAME = gn_syuser;
CREATE GENERATOR gn_syuser;
