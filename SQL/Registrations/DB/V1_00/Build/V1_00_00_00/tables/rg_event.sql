
echo rg_event

create table rg_event (
  rgev_pk          	ND_CODE not null,
  rgev_dtfrom       ND_TIMESTAMP not null,
  rgev_istate       ND_INT not null);

alter table rg_event
  add constraint pk_rgev
    primary key (rgev_pk);
    
create unique index ui_rgev_dtfrom
  on rg_event (rgev_dtfrom);

/* Generator */
create generator gn_rgev;

/* Descriptions */
comment on table rg_event is 'Calendar event';

comment on column rg_event.rgev_pk          is 'pk';
comment on column rg_event.rgev_dtfrom      is 'from';
comment on column rg_event.rgev_istate      is 'state';
