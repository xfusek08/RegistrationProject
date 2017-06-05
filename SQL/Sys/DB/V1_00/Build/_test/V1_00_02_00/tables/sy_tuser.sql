--------------------------------------------------------------------------------
-- file: sy_user.sql
--
CREATE TABLE sy_tuser 
(
  sytusr_pk            ND_CODE NOT NULL,
  sytusr_vident        ND_TEXT NOT NULL,
  sytusr_vfirstname    ND_TEXT,
  sytusr_vsecname      ND_TEXT NOT NULL,
  sytusr_dcreatedate   ND_DATE NOT NULL,
  sytusr_vemail        ND_WWW,
  sytusr_vphone        ND_SHORTTEXT,
  sytusr_vpassword     ND_SHORTTEXT,
 PRIMARY KEY (sytusr_pk)
);
--
CREATE UNIQUE INDEX ui_sytusr_fstsecname 
  ON sy_tuser(sytusr_vfirstname,sytusr_vsecname);
--
CREATE GENERATOR gn_sytuser;
