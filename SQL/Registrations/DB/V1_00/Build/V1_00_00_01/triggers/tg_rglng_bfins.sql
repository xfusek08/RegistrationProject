
create trigger tg_rglng_bfins
  for rg_language
  active
  before insert position 0
as
begin
  if (new.rglng_pk is null) then
    new.rglng_pk = gen_id (gn_rglng, 1);
end
^
