
echo tg_sytprs_bfins

create trigger tg_sytprs_bfins
  for sy_tprmsection
  active
  before insert
  position 0
as
begin
  if (new.sytprs_pk is null) then
    new.sytprs_pk = gen_id (gn_sytprs_pk, 1);
end
^
