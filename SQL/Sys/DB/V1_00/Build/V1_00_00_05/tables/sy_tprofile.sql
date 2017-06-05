
alter table sy_tprofile
  drop constraint ui_sytprof_fownervident;

alter table sy_tprofile
  add constraint ui_sytprof_fownerfuservident
    unique (sytprof_fowner, sytprof_fuser, sytprof_vident);
