
echo rg_course

create table rg_course (
  rgcour_pk          	ND_CODE not null,
  rgcour_flanguage    ND_CODE not null,
  rgcour_dtfrom       ND_TIMESTAMP not null,
  rgcour_istate       ND_INT default 0 not null,
  rgcour_icapacity    ND_INT default 0 not null,
  rgcour_vname        ND_TEXT,
  rgcour_vdesc        ND_DESCRIPTION);

alter table rg_course
  add constraint pk_rgcour
  primary key (rgcour_pk);

alter table rg_course
  add constraint fk_rgcour_flanguage
    foreign key (rgcour_flanguage)
    references rg_language (rglng_pk);

create unique index ui_rgcour_dtfrom
  on rg_course (rgcour_dtfrom);

/* Generator */
create generator gn_rgcour;

/* Descriptions */
comment on table rg_course is 'Course event for RG';

comment on column rg_course.rgcour_pk           is 'pk';
comment on column rg_course.rgcour_dtfrom       is 'from';
comment on column rg_course.rgcour_istate       is 'state';
comment on column rg_course.rgcour_icapacity    is 'capacity';
comment on column rg_course.rgcour_flanguage    is 'language';
comment on column rg_course.rgcour_vname        is 'name';
comment on column rg_course.rgcour_vdesc        is 'description';
