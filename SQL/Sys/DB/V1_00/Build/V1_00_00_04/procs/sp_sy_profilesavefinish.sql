
CREATE OR ALTER PROCEDURE sp_sy_profilesavefinish
AS
BEGIN
  -- pripadne promazani udaju, ktere nebyly ukladany

  RDB$SET_CONTEXT ('USER_SESSION', 'sy_profilesave', null);
END
^
