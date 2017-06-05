SET TERM ^;

---------------------------------------------------------------------
-- GetDayOfWeek
-- ------------------------------------------------------------------
-- Popis: Vraceni dne v tydnu dle predaneho data
--   a_sDate ... datum pro ktery se urci den    
--   ret INT ... den v tydnu 
---------------------------------------------------------------------
CREATE PROCEDURE GetDayOfWeek(a_sDate DATE)
 RETURNS (a_iDayOfWeek INTEGER)
AS
BEGIN
  a_iDayOfWeek = EXTRACT (WEEKDAY FROM a_sDate - 1) + 1;
END ^


---------------------------------------------------------------------
-- GetFirstDayOfMonth
-- ------------------------------------------------------------------
-- Popis: Vraceni prvniho dne v mesici dle predaneho data
--   a_sDate ... datum pro ktery se urci den    
--   ret INT ... den v tydnu 
---------------------------------------------------------------------
CREATE PROCEDURE GetFirstDayOfMonth(a_sDate DATE)
 RETURNS (a_iFirstDayOfMonth INTEGER)
AS
BEGIN
  a_iFirstDayOfMonth = EXTRACT (DAY FROM a_sDate) + 1;
END ^



---------------------------------------------------------------------
-- GetLastDayOfMonth
-- ------------------------------------------------------------------
-- Popis: Vraceni posledniho dne v mesici dle predaneho data
--   a_sDate ... datum pro ktery se urci den    
--   ret INT ... den v tydnu 
---------------------------------------------------------------------
CREATE PROCEDURE GetLastDayOfMonth(a_sDate DATE)
 RETURNS (a_iLastDayOfMonth INTEGER)
AS
  DECLARE VARIABLE l_dTmpDate DATE;
BEGIN
  l_dTmpDate = EXTRACT (DAY FROM a_sDate) + 32;
  a_iLastDayOfMonth = l_dTmpDate - EXTRACT (DAY FROM l_dTmpDate);
END ^




---------------------------------------------------------------------
-- GetNumberDaysOfMonth
-- ------------------------------------------------------------------
-- Popis: Vraceni posledniho dne v mesici dle predaneho data
--   a_sDate ... datum pro ktery se urci den    
--   ret INT ... den v tydnu 
---------------------------------------------------------------------
CREATE PROCEDURE GetNumberDaysOfMonth(a_sDate DATE)
 RETURNS (a_iLastDayOfMonth INTEGER)
AS
  DECLARE VARIABLE l_dTmpDate DATE;
BEGIN
  l_dTmpDate = a_sDate - EXTRACT (DAY FROM a_sDate) + 32;
  a_iLastDayOfMonth = EXTRACT (DAY FROM (l_dTmpDate - EXTRACT(DAY FROM l_dTmpDate)));
END ^


SET TERM ; ^

commit;
