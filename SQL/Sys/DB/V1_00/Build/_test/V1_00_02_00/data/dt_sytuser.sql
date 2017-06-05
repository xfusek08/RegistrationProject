--------------------------------------------------------------------------------
-- file: dt_sytuser.sql
--
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
        0,
        'admin',
        'Admin',
        'Admin',
        'NOW',
        'admin@admin.cz',
        '777 111 222',
        'noe');
--
commit;
