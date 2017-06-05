
echo rg_term

create table rg_term (
  rgtrm_pk          	ND_CODE not null,
  rgtrm_dtfrom          ND_TIMESTAMP not null,
  rgtrm_istate          ND_INT not null);

alter table rg_term
  add constraint pk_rgtrm
    primary key (rgtrm_pk);
    
create unique index ui_rgtrm_dtfrom
  on rg_term (rgtrm_dtfrom);

/* Generator */
create generator gn_rgtrm;

/* Descriptions */
comment on table rg_term is 'Reservation terms';

comment on column rg_term.rgtrm_pk          is 'pk';
comment on column rg_term.rgtrm_dtfrom      is 'from';
comment on column rg_term.rgtrm_istate      is 'state';
