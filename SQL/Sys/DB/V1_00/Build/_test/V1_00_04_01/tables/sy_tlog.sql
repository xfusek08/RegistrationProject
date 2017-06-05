CREATE TABLE sy_tlog 
(
    sytlog_pk          ND_CODE NOT NULL,
    sytlog_fuser       ND_CODE NOT NULL,
    sytlog_isessionid  ND_INT NOT NULL,
    sytlog_ttimestamp  ND_TIMESTAMP NOT NULL,
    sytlog_itype       ND_INT NOT NULL,
    sytlog_vtext       ND_TEXT
);


/* Primary Keys */
ALTER TABLE sy_tlog 
  ADD CONSTRAINT pk_sytlog 
    PRIMARY KEY (sytlog_pk);

/* Generator */
CREATE GENERATOR gn_sytlog;
SET GENERATOR gn_sytlog TO 1;

/* Descriptions */
COMMENT ON TABLE sy_tlog 
  IS 'Logovani';

COMMENT ON COLUMN sy_tlog.sytlog_pk
  IS 'Primarni klic zaznamu';

COMMENT ON COLUMN sy_tlog.sytlog_fuser
  IS 'Odkaz na seznam uzivatelu';

COMMENT ON COLUMN sy_tlog.sytlog_isessionid
  IS 'session ID';

COMMENT ON COLUMN sy_tlog.sytlog_ttimestamp
  IS 'Datum a cas zapisu';    

COMMENT ON COLUMN sy_tlog.sytlog_itype
  IS 'Typ zapisu';
  
COMMENT ON COLUMN sy_tlog.sytlog_vtext
  IS 'Popis';
  
/* Privileges */
GRANT ALL ON sy_tlog 
  TO noe WITH GRANT OPTION;
