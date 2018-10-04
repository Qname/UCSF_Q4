Alter view vw_SOM_AA_EmployeeCategorySummary
as
SELECT        SessionUserid, FiscalYear, FiscalPeriod, PositionTitleCategory, SUM(CASE WHEN RecType = 'FTE' AND RecType IS NOT NULL THEN M01 ELSE 0 END) AS FTEM01, SUM(CASE WHEN RecType = 'FTE' AND 
                         RecType IS NOT NULL THEN M02 ELSE 0 END) AS FTEM02, SUM(CASE WHEN RecType = 'FTE' AND RecType IS NOT NULL THEN M03 ELSE 0 END) AS FTEM03, SUM(CASE WHEN RecType = 'FTE' AND 
                         RecType IS NOT NULL THEN M04 ELSE 0 END) AS FTEM04, SUM(CASE WHEN RecType = 'FTE' AND RecType IS NOT NULL THEN M05 ELSE 0 END) AS FTEM05, SUM(CASE WHEN RecType = 'FTE' AND 
                         RecType IS NOT NULL THEN M06 ELSE 0 END) AS FTEM06, SUM(CASE WHEN RecType = 'FTE' AND RecType IS NOT NULL THEN M07 ELSE 0 END) AS FTEM07, SUM(CASE WHEN RecType = 'FTE' AND 
                         RecType IS NOT NULL THEN M08 ELSE 0 END) AS FTEM08, SUM(CASE WHEN RecType = 'FTE' AND RecType IS NOT NULL THEN M09 ELSE 0 END) AS FTEM09, SUM(CASE WHEN RecType = 'FTE' AND 
                         RecType IS NOT NULL THEN M10 ELSE 0 END) AS FTEM10, SUM(CASE WHEN RecType = 'FTE' AND RecType IS NOT NULL THEN M11 ELSE 0 END) AS FTEM11, SUM(CASE WHEN RecType = 'FTE' AND 
                         RecType IS NOT NULL THEN M12 ELSE 0 END) AS FTEM12, SUM(CASE WHEN RecType <> 'FTE' AND RecType IS NOT NULL THEN M01 ELSE 0 END) AS SalM01, SUM(CASE WHEN RecType <> 'FTE' AND 
                         RecType IS NOT NULL THEN M02 ELSE 0 END) AS SalM02, SUM(CASE WHEN RecType <> 'FTE' AND RecType IS NOT NULL THEN M03 ELSE 0 END) AS SalM03, SUM(CASE WHEN RecType <> 'FTE' AND 
                         RecType IS NOT NULL THEN M04 ELSE 0 END) AS SalM04, SUM(CASE WHEN RecType <> 'FTE' AND RecType IS NOT NULL THEN M05 ELSE 0 END) AS SalM05, SUM(CASE WHEN RecType <> 'FTE' AND 
                         RecType IS NOT NULL THEN M06 ELSE 0 END) AS SalM06, SUM(CASE WHEN RecType <> 'FTE' AND RecType IS NOT NULL THEN M07 ELSE 0 END) AS SalM07, SUM(CASE WHEN RecType <> 'FTE' AND 
                         RecType IS NOT NULL THEN M08 ELSE 0 END) AS SalM08, SUM(CASE WHEN RecType <> 'FTE' AND RecType IS NOT NULL THEN M09 ELSE 0 END) AS SalM09, SUM(CASE WHEN RecType <> 'FTE' AND 
                         RecType IS NOT NULL THEN M10 ELSE 0 END) AS SalM10, SUM(CASE WHEN RecType <> 'FTE' AND RecType IS NOT NULL THEN M11 ELSE 0 END) AS SalM11, SUM(CASE WHEN RecType <> 'FTE' AND 
                         RecType IS NOT NULL THEN M12 ELSE 0 END) AS SalM12
FROM            dbo.SOM_AA_EmployeeListRolling
Where Employee_Id != 'Total'
GROUP BY SessionUserid, FiscalYear, FiscalPeriod, PositionTitleCategory