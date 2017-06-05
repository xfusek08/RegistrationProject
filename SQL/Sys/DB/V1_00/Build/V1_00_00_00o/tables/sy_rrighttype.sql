CREATE TABLE sy_rrighttype 
(
    syrrtype_pk    ND_CODE NOT NULL,
    syrrtype_name  ND_TEXT NOT NULL
);


/* Primary Keys */
ALTER TABLE sy_rrighttype 
  ADD CONSTRAINT pk_sy_rrighttype PRIMARY KEY (syrrtype_pk);

/* Generator */
CREATE GENERATOR gn_rrighttype;
SET GENERATOR gn_rrighttype TO 1;

/* Descriptions */
COMMENT ON TABLE sy_rrighttype IS 
 'Seznam prav';

COMMENT ON COLUMN sy_rrighttype.syrrtype_pk
  IS 'Primarni klic zaznamu';
  
COMMENT ON COLUMN sy_rrighttype.syrrtype_name
  IS 'Jmeno opravneni';  

/* Privileges */  
GRANT SELECT ON sy_rrighttype 
  TO noe WITH GRANT OPTION;  
