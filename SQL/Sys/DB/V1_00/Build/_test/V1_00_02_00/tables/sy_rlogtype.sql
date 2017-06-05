--------------------------------------------------------------------------------
-- file: sy_rlogtype.sql
--
CREATE TABLE sy_rlogtype 
(
  syrlogtp_pk            ND_CODE NOT NULL,
  syrlogtp_vdescription  ND_DESCRIPTION,
  PRIMARY KEY (syrlogtp_pk)
);
--
CREATE GENERATOR gn_syrlogtp;
