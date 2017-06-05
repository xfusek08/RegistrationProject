--------------------------------------------------------------------------------
-- file: sy_log.sql
--
--DROP TABLE sy_log;
--
CREATE TABLE sy_tlog 
(
  sytlog_pk          ND_CODE NOT NULL,
  sytlog_fuser       ND_CODE NOT NULL,
  sytlog_isessionid  ND_INT NOT NULL,
  sytlog_ttimestamp  ND_TIMESTAMP NOT NULL,
  sytlog_itype       ND_INT NOT NULL,
  sytlog_vtext       ND_TEXT,
  PRIMARY KEY (sytlog_pk)
);
--
ALTER TABLE sy_tlog 
  ADD CONSTRAINT fk_sytlog_fuser
    FOREIGN KEY (sytlog_fuser) 
    REFERENCES sy_tuser (sytusr_pk);
--  
ALTER TABLE sy_tlog 
  ADD CONSTRAINT fk_sytlog_itype
    FOREIGN KEY (sytlog_itype) 
    REFERENCES sy_tlogtype (sytlogtp_pk);
--
CREATE GENERATOR gn_sytlog;
