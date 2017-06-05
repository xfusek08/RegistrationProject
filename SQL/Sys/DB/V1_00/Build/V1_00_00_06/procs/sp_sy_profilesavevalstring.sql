
CREATE OR ALTER PROCEDURE sp_sy_profilesavevalstring
(
  a_iInfoType    INTEGER,
  a_vName        VARCHAR (100),
  a_vString      VARCHAR (2000)
)
AS
  DECLARE VARIABLE l_iProfile INTEGER;
BEGIN
  l_iProfile = RDB$GET_CONTEXT ('USER_SESSION', 'sy_profilesave');
  if (l_iProfile is null) then
  begin
    exit;
  end

  -- rovnou update
  -- teprve pokud toto nic neudela, vklada se
  update sy_tprofvalue
    set
      sytprvl_ivaluetype = 1,
      sytprvl_gvalue = :a_vString
    where
      sytprvl_fprofile = :l_iProfile and
      sytprvl_iinfotype = :a_iInfoType and
      sytprvl_vident = :a_vName;
  if (ROW_COUNT = 0) then
  begin
    insert into sy_tprofvalue (
        sytprvl_fprofile,
        sytprvl_iinfotype,
        sytprvl_vident,
        sytprvl_ivaluetype,
        sytprvl_gvalue)
      values (
        :l_iProfile,
        :a_iInfoType,
        :a_vName,
        1,
        :a_vString);
  end
END
^
