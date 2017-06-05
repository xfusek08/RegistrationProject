
CREATE TABLE is_tdbinfo (
  istdbi_vident     ND_SHORTTEXT NOT NULL,
  istdbi_vversion   ND_SHORTTEXT NOT NULL,
  istdbi_vmodif     ND_SHORTTEXT NOT NULL
);

COMMENT ON TABLE is_tdbinfo IS 'Database informations';
COMMENT ON COLUMN is_tdbinfo.istdbi_vident IS 'Database ident.';
COMMENT ON COLUMN is_tdbinfo.istdbi_vversion IS 'Version';
COMMENT ON COLUMN is_tdbinfo.istdbi_vmodif IS 'Modification (a, b, ...)';
