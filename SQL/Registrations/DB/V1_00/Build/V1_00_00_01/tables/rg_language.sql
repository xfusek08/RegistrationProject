
echo rg_language

create table rg_language (
  rglng_pk            ND_CODE not null,
  rglng_ident         ND_ID not null,
  rglng_text          ND_TEXT not null,
  rglng_desc          ND_DESCRIPTION
  );

alter table rg_language
  add constraint pk_rglng
    primary key (rglng_pk);

/* Generator */
create generator gn_rglng;

/* Descriptions */
comment on table rg_language is 'language';

comment on column rg_language.rglng_pk      is 'pk';
comment on column rg_language.rglng_ident   is 'ident';
comment on column rg_language.rglng_text    is 'text';
comment on column rg_language.rglng_desc    is 'description';
