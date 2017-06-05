
echo sy_tstatediagram (FK)

alter table sy_tstatediagram
  add constraint fk_sytsdgr_frootstate
    foreign key (sytsdgr_frootstate)
    references sy_tstate (sytst_pk);
