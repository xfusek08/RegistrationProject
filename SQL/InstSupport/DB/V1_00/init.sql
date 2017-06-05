
/*
CREATE DATABASE 'F:\NOE\Pas_101\Exe\DB\EV.FDB'
  page_size 8192
  user 'SYSDBA'
  password 'masterkey';
*/

--  character set WIN1250;

--------------------------------

CREATE DOMAIN nd_bignum AS NUMERIC(18,0);
CREATE DOMAIN nd_blobb AS BLOB SUB_TYPE 0 SEGMENT SIZE 80;
CREATE DOMAIN nd_blobt AS BLOB SUB_TYPE 1 SEGMENT SIZE 80 CHARACTER SET WIN1250;
CREATE DOMAIN nd_bool AS CHAR(1) CHECK (VALUE IN ('0','1'));
CREATE DOMAIN nd_code AS INTEGER;
CREATE DOMAIN nd_date AS DATE;
CREATE DOMAIN nd_description AS VARCHAR(4000) CHARACTER SET WIN1250 COLLATE WIN1250;
CREATE DOMAIN nd_id AS VARCHAR(10) CHARACTER SET WIN1250 COLLATE WIN1250;
CREATE DOMAIN nd_int AS INTEGER;
CREATE DOMAIN nd_shorttext AS VARCHAR(20) CHARACTER SET WIN1250 COLLATE WIN1250;
CREATE DOMAIN nd_text AS VARCHAR(100) CHARACTER SET WIN1250 COLLATE WIN1250;
CREATE DOMAIN nd_timestamp AS TIMESTAMP;
CREATE DOMAIN nd_www AS VARCHAR(300) CHARACTER SET WIN1250 COLLATE WIN1250;

CREATE EXCEPTION EX_NO_DATA_FOUND 'No data found';
CREATE EXCEPTION EX_VAL_IS_NULL 'Value is null';

CREATE TABLE root (x integer);
insert into root (x)values (0);

CREATE TABLE is_tcomponent (
  ist_pk            ND_CODE NOT NULL,
  ist_vident        ND_SHORTTEXT NOT NULL,
  ist_vprefix       ND_SHORTTEXT NOT NULL,
  ist_vversion      ND_SHORTTEXT
);

ALTER TABLE is_tcomponent
  ADD CONSTRAINT pk_ist_pk
    PRIMARY KEY (ist_pk);
CREATE GENERATOR gn_is_tcomponent;

set term ^;
CREATE TRIGGER tg_istcomponent FOR is_tcomponent
  ACTIVE BEFORE INSERT POSITION 0
  AS
begin
  IF (NEW.ist_pk IS NULL) THEN
    NEW.ist_pk = GEN_ID(gn_is_tcomponent,1);
end;
^
set term ;^

COMMENT ON TABLE is_tcomponent IS 'Seznam komponent';
COMMENT ON COLUMN is_tcomponent.ist_pk IS 'Primarni klic zaznamu';
COMMENT ON COLUMN is_tcomponent.ist_vident IS 'Identifikator komponenty';
COMMENT ON COLUMN is_tcomponent.ist_vprefix IS 'Prefix komponenty';
COMMENT ON COLUMN is_tcomponent.ist_vversion IS 'Aktualne nainstalovana verze';

CREATE TABLE is_tcomphist (
  istch_pk            ND_CODE NOT NULL,
  istch_fcomponent    ND_CODE NOT NULL,
  istch_ddate         ND_DATE NOT NULL,
  istch_vversion      ND_SHORTTEXT NOT NULL
);

ALTER TABLE is_tcomphist
  ADD CONSTRAINT pk_istch_pk
    PRIMARY KEY (istch_pk);
ALTER TABLE is_tcomphist
  ADD CONSTRAINT fk_is_tcomphistory
    FOREIGN KEY (istch_fcomponent)
    REFERENCES is_tcomponent (ist_pk);

CREATE GENERATOR gn_is_tcomphist;

COMMENT ON TABLE is_tcomphist IS 'Historie komponent';
COMMENT ON COLUMN is_tcomphist.istch_pk IS 'Primarni klic zaznamu';
COMMENT ON COLUMN is_tcomphist.istch_fcomponent IS 'Odkaz na komponentu';
COMMENT ON COLUMN is_tcomphist.istch_ddate IS 'Datum instalace';
COMMENT ON COLUMN is_tcomphist.istch_vversion IS 'Cislo verze';
