
echo sy_tstate

CREATE TABLE sy_tstate (
  sytst_pk           ND_CODE NOT NULL,
  sytst_fstdiagram   ND_CODE NOT NULL,
  sytst_vname        ND_TEXT NOT NULL,
  sytst_bstarting    ND_BOOL NOT NULL,
  sytst_bending      ND_BOOL NOT NULL
);

ALTER TABLE sy_tstate
  ADD CONSTRAINT pk_sytst
    PRIMARY KEY (sytst_pk);

alter table sy_tstate
  add constraint fk_sytst_fstdiagram
    foreign key (sytst_fstdiagram)
    references sy_tstatediagram (sytsdgr_pk);

/* Generator */
CREATE GENERATOR gn_sytst;
SET GENERATOR gn_sytst TO 1;

/* Descriptions */
COMMENT ON TABLE sy_tstate IS 'State diagram - state';

comment on column sy_tstate.sytst_pk           is 'prim. key';
comment on column sy_tstate.sytst_fstdiagram   is 'ref. to state diagram';
comment on column sy_tstate.sytst_vname        is 'name';
comment on column sy_tstate.sytst_bstarting    is 'is starting node';
comment on column sy_tstate.sytst_bending      is 'is ending node';
