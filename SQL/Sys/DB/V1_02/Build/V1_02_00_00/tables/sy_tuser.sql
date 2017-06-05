
echo sys_tuser

drop index ui_sytusr_fstsecname;

alter table sy_tuser
  add constraint ui_sytusr_vident
    unique (sytusr_vident);
