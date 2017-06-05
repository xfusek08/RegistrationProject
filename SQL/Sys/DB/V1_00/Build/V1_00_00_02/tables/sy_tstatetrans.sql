
echo sy_tstatetrans

CREATE TABLE sy_tstatetrans (
  sytsttr_pk           ND_CODE NOT NULL,
  sytsttr_vtext        ND_TEXT NOT NULL,
  sytsttr_fstateprev   ND_CODE NOT NULL,
  sytsttr_fstatenext   ND_CODE NOT NULL
);

ALTER TABLE sy_tstatetrans
  ADD CONSTRAINT pk_sytsttr
    PRIMARY KEY (sytsttr_pk);

/* Generator */
CREATE GENERATOR gn_sytsttr;
SET GENERATOR gn_sytsttr TO 1;

alter table sy_tstatetrans
  add constraint fk_sytsttr_fstateprev
    foreign key (sytsttr_fstateprev)
    references sy_tstate (sytst_pk);
alter table sy_tstatetrans
  add constraint fk_sytsttr_fstatenext
    foreign key (sytsttr_fstatenext)
    references sy_tstate (sytst_pk);

/* Descriptions */
COMMENT ON TABLE sy_tstatetrans IS 'State diagram - transition';

comment on column sy_tstatetrans.sytsttr_pk           is 'prim. klíè';
comment on column sy_tstatetrans.sytsttr_vtext        is 'text';
comment on column sy_tstatetrans.sytsttr_fstateprev   is 'state - from';
comment on column sy_tstatetrans.sytsttr_fstatenext   is 'state - to';
