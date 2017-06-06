
echo rg_registration

create table rg_registration (
  rgreg_pk             ND_CODE not null,
  rgreg_fevent         ND_CODE,
  rgreg_iorder         ND_INT,
  rgreg_vclfirstname   ND_TEXT not null,
  rgreg_vcllastname    ND_TEXT not null,
  rgreg_vclemail       ND_WWW not null,
  rgreg_vcltelnumber   ND_TEXT,
  rgreg_vcladdress     ND_TEXT,
  rgreg_vtext          ND_DESCRIPTION,
  rgreg_dtCreated      ND_TIMESTAMP not null,
  rgreg_isNew          ND_BOOL default '1');

alter table rg_registration
  add constraint pk_rgreg
    primary key (rgreg_pk);
    
alter table rg_registration
  add constraint fk_rgreg_fevent
    foreign key (rgreg_fevent)
    references rg_event (rgev_pk);

/* Generator */
create generator gn_rgreg;

/* Descriptions */
comment on table rg_registration IS 'Registration';

comment on column rg_registration.rgreg_pk            is 'pk';
comment on column rg_registration.rgreg_fevent        is 'event';
comment on column rg_registration.rgreg_iorder        is 'order in term';
comment on column rg_registration.rgreg_vclfirstname  is 'client firstname';
comment on column rg_registration.rgreg_vcllastname   is 'client lastname';
comment on column rg_registration.rgreg_vclemail      is 'client email';
comment on column rg_registration.rgreg_vcltelnumber  is 'client telnumber';
comment on column rg_registration.rgreg_vcladdress    is 'client address';
comment on column rg_registration.rgreg_vtext         is 'text';
comment on column rg_registration.rgreg_dtCreated     is 'created';
comment on column rg_registration.rgreg_isNew         is 'is new';
