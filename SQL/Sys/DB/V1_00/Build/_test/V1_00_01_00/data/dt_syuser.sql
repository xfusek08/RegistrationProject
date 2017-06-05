--------------------------------------------------------------------------------
-- Soubor: dt_syuser.sql
-- Popis:  Data pro tabulku sy_user
-- 
-- -----------------------------------------------------------------------------
-- Historie:
-- -----------------------------------------------------------------------------
--   29.12.2005 Sperl Uvodni verze
--------------------------------------------------------------------------------
--
INSERT INTO sy_user (syusr_pk,syusr_ident,syusr_firstname,syusr_secname,syusr_createdate,syusr_email,syusr_phone,SYUSR_PASSWORD) 
VALUES (0, 'admin', 'Admin', 'Admin', 'NOW', 'admin@admin.cz', '777 111 222', 'noe');
--
commit;
