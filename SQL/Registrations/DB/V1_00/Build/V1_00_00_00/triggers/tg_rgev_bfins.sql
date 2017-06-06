
create trigger tg_rgev_bfins
  for rg_event
  active
  before insert position 0
as
begin
  if (new.rgev_pk is null) then
    new.rgev_pk = gen_id (gn_rgev, 1);
end
^
