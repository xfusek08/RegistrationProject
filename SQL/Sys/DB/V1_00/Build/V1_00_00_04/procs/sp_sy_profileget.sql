
CREATE OR ALTER PROCEDURE sp_sy_profileget
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
  -- dohledame profil - uzivatelsky
  select sytprof_pk
    from sy_tprofile
    where
      sytprof_fowner = 2 and
      sytprof_fuser = :l_iUserPK and
      sytprof_vident = :a_vProfileIdent
    into :a_iProfile;
END
^
