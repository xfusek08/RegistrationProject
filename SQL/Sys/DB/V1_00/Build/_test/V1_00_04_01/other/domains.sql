CREATE DOMAIN nd_bignum 
  AS NUMERIC(18,0);

CREATE DOMAIN nd_blobb 
  AS BLOB SUB_TYPE 0 SEGMENT SIZE 80;

CREATE DOMAIN nd_blobt 
  AS BLOB SUB_TYPE 1 SEGMENT SIZE 80 CHARACTER SET WIN1250;

CREATE DOMAIN nd_bool 
  AS CHAR(1) CHECK (VALUE IN ('0','1'));

CREATE DOMAIN nd_code 
  AS INTEGER;

CREATE DOMAIN nd_date 
  AS DATE;

CREATE DOMAIN nd_description 
  AS VARCHAR(4000) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN nd_id 
  AS VARCHAR(10) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN nd_int 
  AS INTEGER;

CREATE DOMAIN nd_shorttext 
  AS VARCHAR(20) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN nd_text 
  AS VARCHAR(100) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN nd_timestamp 
  AS TIMESTAMP;

CREATE DOMAIN nd_www 
  AS VARCHAR(300) CHARACTER SET WIN1250 COLLATE WIN1250;
