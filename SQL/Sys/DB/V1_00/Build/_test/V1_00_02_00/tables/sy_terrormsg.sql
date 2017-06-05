--------------------------------------------------------------------------------
-- file: sy_errormsg.sql
-- 
--DROP TABLE sy_errormsg;
--
CREATE TABLE sy_errormsg
(
  syerrmsg_pk           ND_CODE NOT NULL,
  syerrmsg_vtext        ND_TEXT,
  PRIMARY KEY (syerrmsg_pk)
);
--
CREATE GENERATOR gn_syerrmsg;
