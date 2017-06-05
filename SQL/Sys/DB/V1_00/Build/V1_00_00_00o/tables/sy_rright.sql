CREATE TABLE sy_tright
(
    sytright_pk       ND_CODE NOT NULL,
    sytright_fsyuser  ND_CODE NOT NULL,
    sytright_ftype    ND_CODE,
    sytright_bstatus  ND_BOOL DEFAULT '0'
);

/* Primary Keys */
ALTER TABLE sy_tright 
  ADD CONSTRAINT pk_sy_tright 
    PRIMARY KEY (sytright_pk);

/* Foreign Keys*/
ALTER TABLE sy_tright 
  ADD CONSTRAINT fk_sy_ttype 
    FOREIGN KEY (sytright_ftype) 
    REFERENCES sy_rrighttype (syrrtype_pk);

/* Generator */
CREATE GENERATOR gn_rright;
SET GENERATOR gn_rright TO 1;


/* Descriptions */
COMMENT ON TABLE sy_tright 
 IS 'Opravneni';

COMMENT ON COLUMN sy_tright.sytright_pk
  IS 'Primarni klic zaznamu';

COMMENT ON COLUMN sy_tright.sytright_fsyuser
  IS 'Vazba na uzivatele';

COMMENT ON COLUMN sy_tright.sytright_ftype
  IS 'Typ opravneni (metoda)';

COMMENT ON COLUMN sy_tright.sytright_bstatus
  IS 'Povoleno\zakaznano';
  
/* Privileges*/
GRANT SELECT ON sy_tright 
  TO noe WITH GRANT OPTION;  
