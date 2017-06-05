
echo tg_sytprv_bfins

create trigger tg_sytprv_bfins
  for sy_tprmvalue
  active
  before insert
  position 0
as
begin
  if (new.sytprv_pk is null) then
    new.sytprv_pk = gen_id (gn_sytprv_pk, 1);
end
^
