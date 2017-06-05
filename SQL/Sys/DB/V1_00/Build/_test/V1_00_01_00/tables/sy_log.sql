--------------------------------------------------------------------------------
-- Soubor: sy_log.sql
-- Popis:  Vytvoreni tabulky sy_log
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--------------------------------------------------------------------------------
--
--DROP TABLE sy_log;
--
CREATE TABLE sy_log 
(
  sylog_pk         INTEGER NOT NULL,
  sylog_user      INTEGER NOT NULL,
  sylog_sessionid  INTEGER NOT NULL,
  sylog_timestamp  TIMESTAMP NOT NULL,
  sylog_type       INTEGER NOT NULL,
  sylog_text       VARCHAR(1000) CHARACTER SET WIN1250,
  PRIMARY KEY (sylog_pk)
);
--
ALTER TABLE sy_log ADD FOREIGN KEY (sylog_user) REFERENCES sy_user (syusr_pk);
ALTER TABLE sy_log ADD FOREIGN KEY (sylog_type) REFERENCES sy_logtype (sylogtp_pk);
--
--DELETE FROM RDB$GENERATORS WHERE RDB$GENERATOR_NAME = gn_sylog;
CREATE GENERATOR gn_sylog;
