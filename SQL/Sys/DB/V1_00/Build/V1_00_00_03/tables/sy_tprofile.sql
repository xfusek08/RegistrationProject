
echo sy_tprofile

CREATE TABLE sy_tprofile (
  sytprof_pk           ND_CODE NOT NULL,
  sytprof_fowner       ND_CODE NOT NULL,
  sytprof_fuser        ND_CODE,
  sytprof_vident       ND_TEXT NOT NULL,
  sytprof_vtext        ND_TEXT NOT NULL
);

ALTER TABLE sy_tprofile
  ADD CONSTRAINT pk_sytprof
    PRIMARY KEY (sytprof_pk);

alter table sy_tprofile
  add constraint ui_sytprof_fownervident
    unique (sytprof_fowner, sytprof_vident);

alter table sy_tprofile
  add constraint fk_sytprof_fowner
    foreign key (sytprof_fowner)
    references sy_cprofowner (sycprow_pk);
alter table sy_tprofile
  add constraint fk_sytprof_fuser
    foreign key (sytprof_fuser)
    references sy_tuser (sytusr_pk);

/* Generator */
CREATE GENERATOR gn_sytprof;

/* Descriptions */
COMMENT ON TABLE sy_tprofile IS 'Profile header';

comment on column sy_tprofile.sytprof_pk           is 'prim. key';
comment on column sy_tprofile.sytprof_fowner       is 'owner (default/user/...)';
comment on column sy_tprofile.sytprof_fuser        is 'ref. to user';
comment on column sy_tprofile.sytprof_vident       is 'profile identification';
comment on column sy_tprofile.sytprof_vtext        is 'text';
