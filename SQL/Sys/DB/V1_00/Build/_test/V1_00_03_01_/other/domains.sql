CREATE DOMAIN ND_BIGNUM 
  AS NUMERIC(18,0);

CREATE DOMAIN ND_BLOBB 
  AS BLOB SUB_TYPE 0 SEGMENT SIZE 80;

CREATE DOMAIN ND_BLOBT 
  AS BLOB SUB_TYPE 1 SEGMENT SIZE 80 CHARACTER SET WIN1250;

CREATE DOMAIN ND_BOOL
  AS CHAR(1) CHARACTER SET WIN1250 COLLATE WIN1250
  CHECK (VALUE IN ('0','1'));

CREATE DOMAIN ND_CODE 
  AS INTEGER;

CREATE DOMAIN ND_DATE 
  AS DATE;

CREATE DOMAIN ND_DESCRIPTION 
  AS VARCHAR(4000) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN ND_ID 
  AS VARCHAR(10) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN ND_INT 
  AS INTEGER;

CREATE DOMAIN ND_SHORTTEXT 
  AS VARCHAR(20) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN ND_TEXT 
  AS VARCHAR(100) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE DOMAIN ND_TIMESTAMP 
  AS TIMESTAMP;

CREATE DOMAIN ND_WWW 
  AS VARCHAR(300) CHARACTER SET WIN1250 COLLATE WIN1250;
