CREATE TABLE sy_rlogtype 
(
    syrlogtp_pk            ND_CODE NOT NULL,
    syrlogtp_vname         ND_TEXT
);

/* Primary Keys */
ALTER TABLE sy_rlogtype 
  ADD CONSTRAINT pk_syrlogtp 
    PRIMARY KEY (syrlogtp_pk);

/* Generator*/
CREATE GENERATOR gn_syrlogtype;
SET GENERATOR gn_syrlogtype TO 1;

/* Descriptions */
COMMENT ON TABLE sy_rlogtype IS 
  'Ciselnik druhu logovani';
  
COMMENT ON COLUMN sy_rlogtype.syrlogtp_pk IS 
  'Primarni klic zaznamu';  

COMMENT ON COLUMN sy_rlogtype.syrlogtp_vname IS 
  'Jmeno urovne logovani';  

/* Privileges */
GRANT ALL ON sy_rlogtype 
  TO noe WITH GRANT OPTION;
