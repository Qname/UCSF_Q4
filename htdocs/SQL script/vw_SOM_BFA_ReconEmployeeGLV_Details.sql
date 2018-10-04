USE [GLVData]
GO



Alter view [dbo].[vw_SOM_BFA_ReconEmployeeGLV_Details]
as
SELECT        dbo.SOM_BFA_ReconEmployeeGLV.uniqueid, dbo.SOM_BFA_ReconEmployeeGLV.FiscalYear, dbo.SOM_BFA_ReconEmployeeGLV.FiscalPeriod, dbo.SOM_BFA_ReconEmployeeGLV.RecType, 
                         dbo.SOM_BFA_ReconEmployeeGLV.Employee_Id, dbo.SOM_BFA_ReconEmployeeGLV.Employee_name, dbo.SOM_BFA_ReconEmployeeGLV.HomeDept, dbo.SOM_BFA_ReconEmployeeGLV.PositionTitleCd, 
                         dbo.SOM_BFA_ReconEmployeeGLV.PositionTitleCdTitle, dbo.SOM_BFA_ReconEmployeeGLV.PositionTitleCategory, dbo.SOM_BFA_ReconEmployeeGLV.PositionTitleRank, 
                         dbo.SOM_BFA_ReconEmployeeGLV.PositionTitleSeries, dbo.SOM_BFA_ReconEmployeeGLV.PlanTitleCd, dbo.SOM_BFA_ReconEmployeeGLV.PlanTitleCdTitle, 
                         dbo.SOM_BFA_ReconEmployeeGLV.PlanTitleCategory, dbo.SOM_BFA_ReconEmployeeGLV.PlanTitleRank, dbo.SOM_BFA_ReconEmployeeGLV.PlanTitleSeries, 
                         dbo.SOM_BFA_ReconEmployeeGLV.BusinessUnitCd, dbo.SOM_BFA_ReconEmployeeGLV.DeptCd, dbo.SOM_BFA_ReconEmployeeGLV.FundCd, dbo.SOM_BFA_ReconEmployeeGLV.ProjectCd, 
                         dbo.SOM_BFA_ReconEmployeeGLV.FunctionCd, dbo.SOM_BFA_ReconEmployeeGLV.FlexCd, dbo.SOM_BFA_ReconEmployeeGLV.ProjectOTC, dbo.SOM_BFA_ReconEmployeeGLV.HomeDeptTitle, 
                         dbo.SOM_BFA_ReconEmployeeGLV.DeptTitle, dbo.SOM_BFA_ReconEmployeeGLV.DeptSite, dbo.SOM_BFA_ReconEmployeeGLV.DeptTreeCd, dbo.SOM_BFA_ReconEmployeeGLV.DeptLevel1Cd, 
                         dbo.SOM_BFA_ReconEmployeeGLV.DeptLevel2Cd, dbo.SOM_BFA_ReconEmployeeGLV.DeptLevel3Cd, dbo.SOM_BFA_ReconEmployeeGLV.DeptLevel4Cd, dbo.SOM_BFA_ReconEmployeeGLV.DeptLevel5Cd, 
                         dbo.SOM_BFA_ReconEmployeeGLV.DeptLevel6Cd, dbo.SOM_BFA_ReconEmployeeGLV.FundTitle, dbo.SOM_BFA_ReconEmployeeGLV.FundLevelATitle, dbo.SOM_BFA_ReconEmployeeGLV.FundLevelCTitle, 
                         dbo.SOM_BFA_ReconEmployeeGLV.FundRestricted, dbo.SOM_BFA_ReconEmployeeGLV.FunctionTitle, dbo.SOM_BFA_ReconEmployeeGLV.ProjectTitle, dbo.SOM_BFA_ReconEmployeeGLV.ProjectUseShort, 
                         dbo.SOM_BFA_ReconEmployeeGLV.ProjectManagerCd, dbo.SOM_BFA_ReconEmployeeGLV.ProjectManagerTitleCd, dbo.SOM_BFA_ReconEmployeeGLV.ProjectStartDt, 
                         dbo.SOM_BFA_ReconEmployeeGLV.ProjectEndDt, dbo.SOM_BFA_ReconEmployeeGLV.ProjectCap, dbo.SOM_BFA_ReconEmployeeGLV.ProjectParentCd, dbo.SOM_BFA_ReconEmployeeGLV.ProjectParentTitleCd, 
                         dbo.SOM_BFA_ReconEmployeeGLV.ProjectParentStartDt, dbo.SOM_BFA_ReconEmployeeGLV.ProjectParentEndDt, dbo.SOM_BFA_ReconEmployeeGLV.ProjectUse, 
                         dbo.SOM_BFA_ReconEmployeeGLV.GroupLevelA, dbo.SOM_BFA_ReconEmployeeGLV.GroupLevelB, dbo.SOM_BFA_ReconEmployeeGLV.Clinical, dbo.SOM_BFA_ReconEmployeeGLV.P01_Jul, 
                         dbo.SOM_BFA_ReconEmployeeGLV.P02_Aug, dbo.SOM_BFA_ReconEmployeeGLV.P03_Sep, dbo.SOM_BFA_ReconEmployeeGLV.P04_Oct, dbo.SOM_BFA_ReconEmployeeGLV.P05_Nov, 
                         dbo.SOM_BFA_ReconEmployeeGLV.P06_Dec, dbo.SOM_BFA_ReconEmployeeGLV.P07_Jan, dbo.SOM_BFA_ReconEmployeeGLV.P08_Feb, dbo.SOM_BFA_ReconEmployeeGLV.P09_Mar, 
                         dbo.SOM_BFA_ReconEmployeeGLV.P10_Apr, dbo.SOM_BFA_ReconEmployeeGLV.P11_May, dbo.SOM_BFA_ReconEmployeeGLV.P12_Jun, dbo.SOM_BFA_ReconEmployeeGLV.P99_YTD, 
                         dbo.SOM_BFA_ReconEmployeeGLV.P99_Tot, dbo.SOM_BFA_ReconEmployeeGLV.S01_Jul, dbo.SOM_BFA_ReconEmployeeGLV.S02_Aug, dbo.SOM_BFA_ReconEmployeeGLV.S03_Sep, 
                         dbo.SOM_BFA_ReconEmployeeGLV.S04_Oct, dbo.SOM_BFA_ReconEmployeeGLV.S05_Nov, dbo.SOM_BFA_ReconEmployeeGLV.S06_Dec, dbo.SOM_BFA_ReconEmployeeGLV.S07_Jan, 
                         dbo.SOM_BFA_ReconEmployeeGLV.S08_Feb, dbo.SOM_BFA_ReconEmployeeGLV.S09_Mar, dbo.SOM_BFA_ReconEmployeeGLV.S10_Apr, dbo.SOM_BFA_ReconEmployeeGLV.S11_May, 
                         dbo.SOM_BFA_ReconEmployeeGLV.S12_Jun, dbo.SOM_BFA_ReconEmployeeGLV.S99_YTD, dbo.SOM_BFA_ReconEmployeeGLV.S99_Tot, dbo.SOM_BFA_ReconEmployeeGLV.B01_Jul, 
                         dbo.SOM_BFA_ReconEmployeeGLV.B02_Aug, dbo.SOM_BFA_ReconEmployeeGLV.B03_Sep, dbo.SOM_BFA_ReconEmployeeGLV.B04_Oct, dbo.SOM_BFA_ReconEmployeeGLV.B05_Nov, 
                         dbo.SOM_BFA_ReconEmployeeGLV.B06_Dec, dbo.SOM_BFA_ReconEmployeeGLV.B07_Jan, dbo.SOM_BFA_ReconEmployeeGLV.B08_Feb, dbo.SOM_BFA_ReconEmployeeGLV.B09_Mar, 
                         dbo.SOM_BFA_ReconEmployeeGLV.B10_Apr, dbo.SOM_BFA_ReconEmployeeGLV.B11_May, dbo.SOM_BFA_ReconEmployeeGLV.B12_Jun, dbo.SOM_BFA_ReconEmployeeGLV.B99_YTD, 
                         dbo.SOM_BFA_ReconEmployeeGLV.B99_Tot, dbo.SOM_BFA_ReconEmployeeGLV.V01_Jul, dbo.SOM_BFA_ReconEmployeeGLV.V02_Aug, dbo.SOM_BFA_ReconEmployeeGLV.V03_Sep, 
                         dbo.SOM_BFA_ReconEmployeeGLV.V04_Oct, dbo.SOM_BFA_ReconEmployeeGLV.V05_Nov, dbo.SOM_BFA_ReconEmployeeGLV.V06_Dec, dbo.SOM_BFA_ReconEmployeeGLV.V07_Jan, 
                         dbo.SOM_BFA_ReconEmployeeGLV.V08_Feb, dbo.SOM_BFA_ReconEmployeeGLV.V09_Mar, dbo.SOM_BFA_ReconEmployeeGLV.V10_Apr, dbo.SOM_BFA_ReconEmployeeGLV.V11_May, 
                         dbo.SOM_BFA_ReconEmployeeGLV.V12_Jun, dbo.SOM_BFA_ReconEmployeeGLV.V99_YTD, dbo.SOM_BFA_ReconEmployeeGLV.V99_Tot, dbo.SOM_BFA_ReconEmployeeGLV.CYTarget, 
                         dbo.SOM_BFA_ReconEmployeeGLV.ReconStatusCd, dbo.SOM_BFA_ReconEmployeeGLV.ReconAssignCd, dbo.SOM_BFA_ReconEmployeeGLV.ReconComment,dbo.SOM_BFA_ReconEmployeeGLV.RECON_Link, dbo.SOM_BFA_ReconEmployeeGLV.ReconUser, 
                         dbo.SOM_BFA_ReconEmployeeGLV.ReconDate, dbo.vw_COA_SOM_Funds.FundLevelBTitle, dbo.vw_COA_SOM_Funds.FundLevelDTitle, dbo.vw_COA_SOM_Funds.FundTitleCd, 
                         dbo.vw_COA_SOM_Functions.FunctionTitleCd, dbo.vw_COA_SOM_Departments.DeptTitleCd, dbo.vw_COA_SOM_Departments.DeptLevel1TitleCd, dbo.vw_COA_SOM_Departments.DeptLevel2TitleCd, 
                         dbo.vw_COA_SOM_Departments.DeptLevel3TitleCd, dbo.vw_COA_SOM_Departments.DeptLevel4TitleCd, dbo.vw_COA_SOM_Departments.DeptLevel5TitleCd, dbo.vw_COA_SOM_Departments.DeptLevel6TitleCd, 
                         dbo.vw_COA_SOM_Departments.DeptTreeTitle, dbo.vw_COA_SOM_Departments.DeptTreeTitleCdAbbrev, dbo.vw_COA_SOM_Projects.ProjectTitleCd, 
                         dbo.vw_COA_SOM_Projects.SOM_PROJ_MGR_NAME AS ProjectManagerName, dbo.vw_COA_SOM_Projects.PROJECT_TYPE AS ProjectType, 
                         dbo.vw_COA_SOM_Projects.AT__PROJECT_MANAGER AS ProjectManagerAT, dbo.vw_COA_SOM_Projects.AT__ZGL_AWD_PARENT_ID AS ProjectParent, 
                         dbo.vw_COA_SOM_Projects.AT__ZGL_AWD_PARENT_ID AS ProjectAwardParentCd, dbo.vw_COA_SOM_FlexCds.FlexTitleCd, dbo.vw_COA_SOM_BusinessUnits.BusinessUnitTitleCd, 'No Group' AS NoGroup, 
                         dbo.vw_COA_SOM_Functions.FunctionMissionTitle, dbo.vw_COA_SOM_Departments.DeptGroupSOM, dbo.vw_COA_SOM_Projects.SOM_PROJECT_USE_SHORT, 
                         dbo.vw_COA_SOM_Projects.ZSOM_PROJECT_USE_GRP_SHORT, dbo.vw_COA_SOM_Projects.ProjectUseGroup, dbo.vw_COA_SOM_Projects.ProjectUseGroupShort, dbo.vw_COA_SOM_Funds.FundLevelACd, 
                         dbo.vw_COA_SOM_Funds.FundLevelBCd, dbo.vw_COA_SOM_Funds.FundLevelCCd, dbo.vw_COA_SOM_Funds.FundLevelDCd
FROM            dbo.SOM_BFA_ReconEmployeeGLV LEFT OUTER JOIN
                         dbo.vw_SOM_BFA_ReconStatus ON dbo.SOM_BFA_ReconEmployeeGLV.ReconStatusCd = dbo.vw_SOM_BFA_ReconStatus.ReconStatusCd LEFT OUTER JOIN
                         dbo.vw_COA_SOM_BusinessUnits ON dbo.SOM_BFA_ReconEmployeeGLV.BusinessUnitCd = dbo.vw_COA_SOM_BusinessUnits.BusinessUnitCd LEFT OUTER JOIN
                         dbo.vw_COA_SOM_Departments ON dbo.SOM_BFA_ReconEmployeeGLV.DeptCd = dbo.vw_COA_SOM_Departments.DeptCd LEFT OUTER JOIN
                         dbo.vw_COA_SOM_FlexCds ON dbo.SOM_BFA_ReconEmployeeGLV.FlexCd = dbo.vw_COA_SOM_FlexCds.FlexCd LEFT OUTER JOIN
                         dbo.vw_COA_SOM_Functions ON dbo.SOM_BFA_ReconEmployeeGLV.FunctionCd = dbo.vw_COA_SOM_Functions.FunctionCd LEFT OUTER JOIN
                         dbo.vw_COA_SOM_Funds ON dbo.SOM_BFA_ReconEmployeeGLV.FundCd = dbo.vw_COA_SOM_Funds.FundCd LEFT OUTER JOIN
                         dbo.vw_COA_SOM_Projects ON dbo.SOM_BFA_ReconEmployeeGLV.ProjectCd = dbo.vw_COA_SOM_Projects.ProjectCd
GO


