
echo sy_tmodule

CREATE TABLE sy_tmodule (
  sytmod_pk           nd_code not null,
  sytmod_iident       nd_int not null,
  sytmod_vname        nd_text not null
);

ALTER TABLE sy_tmodule
  ADD CONSTRAINT pk_sytmod
    PRIMARY KEY (sytmod_pk);

alter table sy_tmodule
  add constraint ui_sytmod_iident
    unique (sytmod_iident);

/* Generator */
CREATE GENERATOR gn_sytmod;

/* Descriptions */
COMMENT ON TABLE sy_tmodule IS 'Module';

comment on column sy_tmodule.sytmod_pk           is 'prim. key';
comment on column sy_tmodule.sytmod_iident       is 'identification number';
comment on column sy_tmodule.sytmod_vname        is 'name';
