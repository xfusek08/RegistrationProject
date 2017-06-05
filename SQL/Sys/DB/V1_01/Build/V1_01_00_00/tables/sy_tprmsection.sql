
echo sy_tprmsection

create table sy_tprmsection (
  sytprs_pk           nd_code not null,
  sytprs_fuser        nd_code not null,
  sytprs_iident       nd_int not null);

alter table sy_tprmsection
  add constraint pk_sytprs_pk
    primary key (sytprs_pk);

alter table sy_tprmsection
  add constraint fk_sytprs_fuser
    foreign key (sytprs_fuser)
    references sy_tuser (sytusr_pk);

alter table sy_tprmsection
  add constraint ui_sytprs_userident
    unique (sytprs_fuser, sytprs_iident);

/* Generator */
create generator gn_sytprs_pk;

/* descriptions */
comment on table sy_tprmsection is 'Section of parameters';

comment on column sy_tprmsection.sytprs_pk           is 'prim. key';
comment on column sy_tprmsection.sytprs_fuser        is 'user';
comment on column sy_tprmsection.sytprs_iident       is 'identification number';
