
-- initializing

CREATE DOMAIN nd_code AS INTEGER;
CREATE DOMAIN nd_date AS DATE;
CREATE DOMAIN nd_shorttext AS VARCHAR(20) CHARACTER SET UTF8;

CREATE TABLE is_tcomponent (
  ist_pk            ND_CODE NOT NULL,
  ist_vident        ND_SHORTTEXT NOT NULL,
  ist_vprefix       ND_SHORTTEXT NOT NULL,
  ist_vversion      ND_SHORTTEXT);

ALTER TABLE is_tcomponent
  ADD CONSTRAINT pk_ist_pk
    PRIMARY KEY (ist_pk);
CREATE GENERATOR gn_is_tcomponent;

CREATE TABLE is_tcomphist (
  istch_pk            ND_CODE NOT NULL,
  istch_fcomponent    ND_CODE NOT NULL,
  istch_ddate         ND_DATE NOT NULL,
  istch_vversion      ND_SHORTTEXT NOT NULL);

ALTER TABLE is_tcomphist
  ADD CONSTRAINT pk_istch_pk
    PRIMARY KEY (istch_pk);
ALTER TABLE is_tcomphist
  ADD CONSTRAINT fk_is_tcomphistory
    FOREIGN KEY (istch_fcomponent)
    REFERENCES is_tcomponent (ist_pk);

CREATE GENERATOR gn_is_tcomphist;

CREATE TABLE is_tdbinfo (
  istdbi_vident     ND_SHORTTEXT NOT NULL,
  istdbi_vversion   ND_SHORTTEXT NOT NULL,
  istdbi_vmodif     ND_SHORTTEXT NOT NULL
);

-- basic component

insert into is_tcomponent (ist_pk, ist_vident, ist_vprefix, ist_vversion)
  values (gen_id (gn_is_tcomponent, 1), 'INSTSUPPORT', 'IS', '0.00.00.00');
