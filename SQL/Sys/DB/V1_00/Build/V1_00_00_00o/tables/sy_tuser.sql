CREATE TABLE sy_tuser 
(
    sytusr_pk           ND_CODE NOT NULL,
    sytusr_vident       ND_TEXT NOT NULL,
    sytusr_vfirstname   ND_TEXT,
    sytusr_vsecname     ND_TEXT NOT NULL,
    sytusr_dcreatedate  ND_DATE NOT NULL,
    sytusr_vemail       ND_WWW,
    sytusr_vphone       ND_SHORTTEXT,
    sytusr_vpassword    ND_SHORTTEXT
);

/* Primary Keys */
ALTER TABLE sy_tuser 
  ADD CONSTRAINT pk_sy_tuser PRIMARY KEY (sytusr_pk);


/* Indices */
CREATE UNIQUE INDEX ui_sytusr_fstsecname 
  ON sy_tuser (sytusr_vfirstname, sytusr_vsecname);

/* Generator */
CREATE GENERATOR gn_sytuser;
SET GENERATOR gn_sytuser TO 1;


/* Descriptions */
COMMENT ON TABLE sy_tuser IS 
 'Seznam uzivatelu';

COMMENT ON COLUMN sy_tuser.sytusr_pk
  IS 'Primarni klic zaznamu';

COMMENT ON COLUMN sy_tuser.sytusr_vident
  IS 'Login uzivatele';  

COMMENT ON COLUMN sy_tuser.sytusr_vfirstname
  IS 'Krestni jmeno';

COMMENT ON COLUMN sy_tuser.sytusr_dcreatedate
  IS 'Prijmeni';

COMMENT ON COLUMN sy_tuser.sytusr_vemail
  IS 'Email';

COMMENT ON COLUMN sy_tuser.sytusr_vphone
  IS 'Telefon';

COMMENT ON COLUMN sy_tuser.sytusr_vpassword
  IS 'Heslo';          
  
/* Privileges */
GRANT SELECT ON sy_tuser 
  TO noe WITH GRANT OPTION;  
