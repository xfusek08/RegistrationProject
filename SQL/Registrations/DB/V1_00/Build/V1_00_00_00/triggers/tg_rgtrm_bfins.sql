
create trigger tg_rgtrm_bfins
  for rg_term
  active
  before insert position 0
as
begin
  if (new.rgtrm_pk is null) then
    new.rgtrm_pk = gen_id (gn_rgtrm, 1);
end
^
