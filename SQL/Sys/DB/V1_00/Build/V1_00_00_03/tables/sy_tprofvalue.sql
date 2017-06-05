
echo sy_tprofvalue

CREATE TABLE sy_tprofvalue (
  sytprvl_pk           ND_CODE NOT NULL,
  sytprvl_fprofile     ND_CODE NOT NULL,
  sytprvl_iinfotype    ND_INT NOT NULL,
  sytprvl_vident       ND_TEXT NOT NULL,
  sytprvl_ivaluetype   ND_INT NOT NULL,
  sytprvl_gvalue       ND_BLOBT NOT NULL
);

ALTER TABLE sy_tprofvalue
  ADD CONSTRAINT pk_sytprvl
    PRIMARY KEY (sytprvl_pk);

alter table sy_tprofvalue
  add constraint ui_sytprvl_fprofileitypevident
    unique (sytprvl_fprofile, sytprvl_iinfotype, sytprvl_vident);

alter table sy_tprofvalue
  add constraint fk_sytprvl_fprofile
    foreign key (sytprvl_fprofile)
    references sy_tprofile (sytprof_pk);

/* Generator */
CREATE GENERATOR gn_sytprvl;

/* Descriptions */
COMMENT ON TABLE sy_tprofvalue IS 'Profile value';

comment on column sy_tprofvalue.sytprvl_pk           is 'prim. key';
comment on column sy_tprofvalue.sytprvl_fprofile     is 'ref. to profile header';
comment on column sy_tprofvalue.sytprvl_iinfotype    is 'information type (win. position/layout/...)';
comment on column sy_tprofvalue.sytprvl_vident       is 'identification (window id...)';
comment on column sy_tprofvalue.sytprvl_ivaluetype   is 'value type (number, coordinate, ...)';
comment on column sy_tprofvalue.sytprvl_gvalue       is 'value as blob';
