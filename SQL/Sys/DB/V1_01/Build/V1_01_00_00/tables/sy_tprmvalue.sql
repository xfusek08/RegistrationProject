
echo sy_tprmvalue

create table sy_tprmvalue (
  sytprv_pk           nd_code not null,
  sytprv_fsection     nd_code not null,
  sytprv_iident       nd_int not null,
  sytprv_ftype        nd_code not null,
  sytprv_vvalue       nd_text);

alter table sy_tprmvalue
  add constraint pk_sytprv_pk
    primary key (sytprv_pk);

alter table sy_tprmvalue
  add constraint ui_sytprv_sectionident
    unique (sytprv_fsection, sytprv_iident);

alter table sy_tprmvalue
  add constraint fk_sytprv_fsection
    foreign key (sytprv_fsection)
    references sy_tprmsection (sytprs_pk);
--alter table sy_tprmvalue
--  add constraint fk_sytprv_ftype
--    foreign key (sytprv_ftype)
--    references xxx (xxx);

/* generator */
create generator gn_sytprv_pk;

/* descriptions */
comment on table sy_tprmvalue is 'Parameter value';

comment on column sy_tprmvalue.sytprv_pk           is 'prim. key';
comment on column sy_tprmvalue.sytprv_fsection     is 'section';
comment on column sy_tprmvalue.sytprv_iident       is 'identification number';
comment on column sy_tprmvalue.sytprv_ftype        is 'type of the value';
comment on column sy_tprmvalue.sytprv_vvalue       is 'value string';
