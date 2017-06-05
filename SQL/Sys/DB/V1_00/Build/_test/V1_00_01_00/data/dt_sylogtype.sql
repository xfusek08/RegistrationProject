--------------------------------------------------------------------------------
-- Soubor: dt_sylogtype.sql
-- Popis:  Data pro tabulku sy_logtype
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--------------------------------------------------------------------------------
--
INSERT INTO sy_logtype (sylogtp_pk,sylogtp_description) VALUES (1, 'Login');
INSERT INTO sy_logtype (sylogtp_pk,sylogtp_description) VALUES (2, 'Logout');
INSERT INTO sy_logtype (sylogtp_pk,sylogtp_description) VALUES (3, 'Error');
INSERT INTO sy_logtype (sylogtp_pk,sylogtp_description) VALUES (4, 'Info');
INSERT INTO sy_logtype (sylogtp_pk,sylogtp_description) VALUES (0, 'Debug');
--
commit;
