
echo sy_cprofowner

CREATE TABLE sy_cprofowner (
  sycprow_pk           ND_CODE NOT NULL,
  sycprow_vdesc        ND_TEXT NOT NULL
);

ALTER TABLE sy_cprofowner
  ADD CONSTRAINT pk_sycprow
    PRIMARY KEY (sycprow_pk);

/* Descriptions */
COMMENT ON TABLE sy_cprofowner IS 'Profile owner type';

comment on column sy_cprofowner.sycprow_pk           is 'prim. key';
comment on column sy_cprofowner.sycprow_vdesc        is 'description';
