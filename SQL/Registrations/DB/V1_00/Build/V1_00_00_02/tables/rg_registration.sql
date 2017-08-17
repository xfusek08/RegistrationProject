
echo rg_registration

update rdb$relation_fields
  set
    rdb$null_flag = 1,
    rdb$default_source = 'default ''0'''
  where
    rdb$relation_name = 'RG_REGISTRATION' and
    rdb$field_name = 'RGREG_ISNEW';
