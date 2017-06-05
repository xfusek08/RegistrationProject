
CREATE OR ALTER PROCEDURE sp_sy_profilesavestart
(
  a_vProfileIdent   VARCHAR (100)
)
RETURNS (
  a_iProfile INTEGER
)
AS
  DECLARE VARIABLE l_iUserPK INTEGER;
BEGIN
  a_iProfile = null;
  --
  -- prihlaseny uzivatel
  l_iUserPK = RDB$GET_CONTEXT ('USER_SESSION', 'user_code');
  if (l_iUserPK is null) then
  begin
    exit;
  end
  --
  -- dohledame profil
  execute procedure sp_sy_profileget (a_vProfileIdent)
    returning_values :a_iProfile;
  --
  -- pokud vraci null, musi se poridit nova veta
  if (a_iProfile is null) then
  begin
    insert into sy_tprofile (
        sytprof_fowner,
        sytprof_fuser,
        sytprof_vident,
        sytprof_vtext)
      values (
        2,
        :l_iUserPK,
        :a_vProfileIdent,
        'default user profile')
      returning sytprof_pk
      into :a_iProfile;
  end
  --
  RDB$SET_CONTEXT ('USER_SESSION', 'sy_profilesave', a_iProfile);
END
^
