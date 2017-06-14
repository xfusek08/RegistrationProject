
create trigger tg_rgcour_bfins
  for rg_course
  active
  before insert position 0
as
begin
  if (new.rgcour_pk is null) then
    new.rgcour_pk = gen_id (gn_rgcour, 1);
end
^
