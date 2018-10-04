USE [GLVData]
GO
/****** Object:  StoredProcedure [dbo].[sp_SOM_GLV_MyReportsURL]    Script Date: 12/6/2017 3:16:16 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


-- 

ALTER PROCEDURE [dbo].[sp_SOM_GLV_MyReportsURL]
	@vBusinessUnitCd varchar(5),
	@vDeptCdGLV varchar(6),
	@vAcctLevel varchar(1), 
	@vYearRange varchar(20) = '20150701-20160630'  
AS
BEGIN 
	-- Sample: exec sp_SOM_GLV_MyReportsURL 'SFCMP', '133100', 'C', '20150701-20160630'  
	declare @vURL varchar(2048) 
	declare @vStr varchar(2048) 
	declare @vMun varchar(255)  -- MUN stands for 'Member unique number' (
	declare @vMunDisp varchar(100) 
	declare @vMunLevel smallint 
	declare @vshortYearRange varchar(20)
	set  @vshortYearRange = SUBSTRING(@vYearRange,1,4)+'-'+SUBSTRING(@vYearRange,12,2)
	select @vMun = '[MgmtSummary].[Department].[Department].[Department Level ' + cast((DeptLevel + 1) as varchar(1))+ ']->:[PC].[@MEMBER].[' + @vDeptCdGLV + ']', 
		@vMunDisp = @vDeptCdGLV + ' - ' + upper(DeptTitle), 
		@vMunLevel=DeptLevel 
		from vw_COA_SOM_Departments where DeptCd=@vDeptCdGLV 
	set @vURL = 'https://mrpt.ucsf.edu/ibmcognos/cgi-bin/cognos.cgi?b_action=cognosViewer' 
    set @vURL = @vURL + '&ui.action=run'
    set @vURL = @vURL + '&run.prompt=false'
    set @vStr = ''
	set @vMunDisp = Replace(@vMunDisp, '&', '&amp;');
    set @vStr = @vStr + '&p_p_DepartmentID=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''' + @vMUN + ''' displayValue='''+ @vMunDisp +'''/></selectChoices>')
    set @vStr = @vStr + '&p_p_DepartmentNode=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''' + @vAcctLevel + '''/></selectChoices>')
    set @vStr = @vStr + '&p_p_ProjectID=' + dbo.fn_PctEncode('<selectChoices></selectChoices>')
    set @vStr = @vStr + '&p_p_Fund=' + dbo.fn_PctEncode('<selectChoices></selectChoices>')
    set @vStr = @vStr + '&p_myTextYearPrompt=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''' + @vYearRange + ''' displayValue='''+@vshortYearRange+'''/></selectChoices>')
    set @vStr = @vStr + '&p_p_AccountDefaultLvl=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''' + rTrim(Ascii(upper(@vAcctLevel)) - 66) + ''' displayValue=''Level ' + cast(@vAcctLevel as varchar(1)) + '''/></selectChoices>')
    set @vStr = @vStr + '&p_p_BU=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''[MgmtSummary].[Business Unit].[Business Unit].[Business Unit Level]-&gt;:[PC].[@MEMBER].[' + @vBusinessUnitCd + ']''/></selectChoices>')
    set @vStr = @vStr + '&run.outputFormat='
    set @vStr = @vStr + '&p_p_Report=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''1'' displayValue=''Plan, Actuals and Forecast''/></selectChoices>')
    set @vStr = @vStr + '&p_p_InExPeriods=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''Ex'' displayValue=''Exclude Open Periods''/></selectChoices>')
    set @vStr = @vStr + '&p_p_FundNode=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''1''/></selectChoices>')
    set @vStr = @vStr + '&p_p_AccountNode=' + dbo.fn_PctEncode('<selectChoices><selectOption useValue=''' + @vAcctLevel + '''/></selectChoices>')
    set @vStr = @vStr + '&ui.object=' + dbo.fn_PctEncode('/content/folder[@name=''F3Reporting'']/folder[@name=''Operational Reports'']/report[@name=''Monthly Report'']')
    set @vStr = @vStr + '&ui.name=' + dbo.fn_PctEncode('Monthly Report')
	set @vStr = @vStr + '&run.verticalElements=1000'
	set @vStr = @vStr + '&p_p_CodeDes=<selectChoices><selectOption useValue=''D'' displayValue=''Display Descriptions Only''/></selectChoices>'
    set @vStr = Replace(@vStr, 'useValue=', 'useValue%3D')
    set @vStr = Replace(@vStr, 'displayValue=', 'displayValue%3D')
	set @vURL = @vURL + @vStr
	select @vURL as MyReportsURL 
END 

