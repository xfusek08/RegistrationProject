
create trigger tg_rgreg_bfins
  for rg_registration
  active
  before insert position 0
as
begin
  if (new.rgreg_pk is null) then
    new.rgreg_pk = gen_id (gn_rgreg, 1);
end
^
