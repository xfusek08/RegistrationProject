
echo rg_registration

drop table rg_registration;
drop generator gn_rgreg;

create table rg_registration (
  rgreg_pk             ND_CODE not null,
  rgreg_fcourse        ND_CODE,
  rgreg_iorder         ND_INT,
  rgreg_vclfirstname   ND_TEXT not null,
  rgreg_vcllastname    ND_TEXT not null,
  rgreg_vclemail       ND_WWW not null,
  rgreg_vcltelnumber   ND_TEXT,
  rgreg_vcladdress     ND_TEXT,
  rgreg_vtext          ND_DESCRIPTION,
  rgreg_dtCreated      ND_TIMESTAMP not null,
  rgreg_flanguage      ND_CODE,
  rgreg_isNew          ND_BOOL default '1');

alter table rg_registration
  add constraint pk_rgreg
    primary key (rgreg_pk);
    
alter table rg_registration
  add constraint fk_rgreg_fcourse
    foreign key (rgreg_fcourse)
    references rg_course (rgcour_pk);

alter table rg_registration
  add constraint fk_rgreg_flanguage
    foreign key (rgreg_flanguage)
    references rg_language (rglng_pk);

/* Generator */
create generator gn_rgreg;

/* Descriptions */
comment on table rg_registration IS 'Registration';

comment on column rg_registration.rgreg_pk            is 'pk';
comment on column rg_registration.rgreg_fcourse       is 'course';
comment on column rg_registration.rgreg_iorder        is 'order in term';
comment on column rg_registration.rgreg_vclfirstname  is 'client firstname';
comment on column rg_registration.rgreg_vcllastname   is 'client lastname';
comment on column rg_registration.rgreg_vclemail      is 'client email';
comment on column rg_registration.rgreg_vcltelnumber  is 'client telnumber';
comment on column rg_registration.rgreg_vcladdress    is 'client address';
comment on column rg_registration.rgreg_vtext         is 'text';
comment on column rg_registration.rgreg_dtCreated     is 'created';
comment on column rg_registration.rgreg_flanguage     is 'on language';
comment on column rg_registration.rgreg_isNew         is 'is new';
