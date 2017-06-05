
echo sy_tusermodavail

CREATE TABLE sy_tusermodavail (
  sytusma_pk           nd_code not null,
  sytusma_fuser        nd_code not null,
  sytusma_fmodule      nd_code not null,
  sytusma_bclavailstd  nd_bool not null,
  sytusma_vclipsstd    nd_text,
  sytusma_bclavailadm  nd_bool not null,
  sytusma_vclipsadm    nd_text,
  sytusma_bwebavailstd nd_bool not null,
  sytusma_vwebipsstd   nd_text,
  sytusma_bwebavailadm nd_bool not null,
  sytusma_vwebipsadm   nd_text
);

ALTER TABLE sy_tusermodavail
  ADD CONSTRAINT pk_sytusma
    PRIMARY KEY (sytusma_pk);

alter table sy_tusermodavail
  add constraint ui_sytusma_fuserfmodule
    unique (sytusma_fuser, sytusma_fmodule);

alter table sy_tusermodavail
  add constraint fk_sytusma_fuser
    foreign key (sytusma_fuser)
    references sy_tuser (sytusr_pk);
alter table sy_tusermodavail
  add constraint fk_sytusma_fmodule
    foreign key (sytusma_fmodule)
    references sy_tmodule (sytmod_pk);

/* Generator */
CREATE GENERATOR gn_sytusma;

/* Descriptions */
COMMENT ON TABLE sy_tusermodavail IS 'User modules';

comment on column sy_tusermodavail.sytusma_pk           is 'prim. key';
comment on column sy_tusermodavail.sytusma_fuser        is 'ref. to user';
comment on column sy_tusermodavail.sytusma_fmodule      is 'ref. to module';
comment on column sy_tusermodavail.sytusma_bclavailstd  is 'is avail...';
comment on column sy_tusermodavail.sytusma_vclipsstd    is 'IPs availed for access';
comment on column sy_tusermodavail.sytusma_bclavailadm  is 'is avail for administration...';
comment on column sy_tusermodavail.sytusma_vclipsadm    is 'IPs availed for administration access';
comment on column sy_tusermodavail.sytusma_bwebavailstd is 'web - is avail...';
comment on column sy_tusermodavail.sytusma_vwebipsstd   is 'web - IPs availed for access';
comment on column sy_tusermodavail.sytusma_bwebavailadm is 'web - is avail for administration...';
comment on column sy_tusermodavail.sytusma_vwebipsadm   is 'web - IPs availed for administration access';
