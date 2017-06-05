
echo sy_tstatediagram

CREATE TABLE sy_tstatediagram (
  sytsdgr_pk           ND_CODE NOT NULL,
  sytsdgr_vname        ND_TEXT NOT NULL,
  sytsdgr_frootstate   ND_CODE,
  sytsdgr_bvalid       ND_BOOL DEFAULT '0' NOT NULL
);

ALTER TABLE sy_tstatediagram
  ADD CONSTRAINT pk_sytsdgr
    PRIMARY KEY (sytsdgr_pk);

/* Generator */
CREATE GENERATOR gn_sytsdgr;
SET GENERATOR gn_sytsdgr TO 1;

-- fk na frootstate az pozdeji


/* Descriptions */
COMMENT ON TABLE sy_tstatediagram IS 'State diagram';

comment on column sy_tstatediagram.sytsdgr_pk           is 'prim. klíè';
comment on column sy_tstatediagram.sytsdgr_vname        is 'název';
comment on column sy_tstatediagram.sytsdgr_frootstate   is 'root state';
comment on column sy_tstatediagram.sytsdgr_bvalid       is 'diagram validity';
