USE [GLVData]
GO
/****** Object:  UserDefinedFunction [dbo].[fn_SOM_BFA_GetWhereFromSavedFilter]    Script Date: 1/25/2018 2:25:00 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER FUNCTION [dbo].[fn_SOM_BFA_GetWhereFromSavedFilter]
(
	@vUserId varchar(30), 
	@vDeptCdSaved varchar(6), 
	@vDeptCdOverride varchar(6), 
	@vFilterName varchar(50), 
	@vReturnType smallint=0 
)
RETURNS varchar(1000) 
AS
BEGIN

	declare @vSQLDeptCdSaved as varchar(255) 
	declare @vDeptCdSavedLevel as smallint 
	declare @vDeptCdOverrideLevel as smallint 
	--
	declare @vSQLDept as varchar(255) 
	declare @vSQLDeptSite as varchar(255) 
	declare @vSQLFund as varchar(255) 
	declare @vSQLProjectManagerCd as varchar(255) 
	declare @vSQLProjectUseShort as varchar(255) 
	declare @vSQLProjectCd as varchar(255) 
	declare @vSQLFunctionCd as varchar(255) 
	declare @vSQLFlexCd as varchar(255) 
	--
	declare @vSQLDeptX as varchar(255) 
	declare @vSQLDeptSiteX as varchar(255) 
	declare @vSQLFundX as varchar(255) 
	declare @vSQLProjectManagerCdX as varchar(255) 
	declare @vSQLProjectUseShortX as varchar(255) 
	declare @vSQLProjectCdX as varchar(255) 
	declare @vSQLFunctionCdX as varchar(255) 
	declare @vSQLFlexCdX as varchar(255) 
	-- 
	declare @vSQL as varchar(4096) 
	--
	declare @vChartStrField as varchar(50), @vChartStrValue as varchar(50), @vExcept as varchar(1), @vDeptLevel as smallint, @vFundLevel as smallint
	--
	declare @vListDeptCd1 as varchar(512)='', @vListDeptCd2 as varchar(512)='', @vListDeptCd3 as varchar(512)='', @vListDeptCd4 as varchar(512)='', @vListDeptCd5 as varchar(512)='', @vListDeptCd6 as varchar(512)='' 
	declare @vListDeptSite as varchar(512)=''
	declare @vListFundCd1 as varchar(512)='', @vListFundCd2 as varchar(512)='', @vListFundCd3 as varchar(512)='', @vListFundCd4 as varchar(512)='', @vListFundCd as varchar(512)=''
	declare @vListProjectManagerCd as varchar(512)='' 
	declare @vListProjectUseShort as varchar(512)='', @vListProjectCd as varchar(512)='', @vListFunctionCd as varchar(512)='', @vListFlexCd as varchar(512)='' 
	--
	declare @vListXDeptCd1 as varchar(512)='', @vListXDeptCd2 as varchar(512)='', @vListXDeptCd3 as varchar(512)='', @vListXDeptCd4 as varchar(512)='', @vListXDeptCd5 as varchar(512)='', @vListXDeptCd6 as varchar(512)='' 
	declare @vListXDeptSite as varchar(512)='' 
	declare @vListXFundCd1 as varchar(512)='', @vListXFundCd2 as varchar(512)='', @vListXFundCd3 as varchar(512)='', @vListXFundCd4 as varchar(512)='', @vListXFundCd as varchar(512)=''
	declare @vListXProjectManagerCd as varchar(512)='' 
	declare @vListXProjectUseShort as varchar(512)='', @vListXProjectCd as varchar(512)='', @vListXFunctionCd as varchar(512)='', @vListXFlexCd as varchar(512)='' 

	select @vDeptCdSavedLevel=DeptLevel from vw_COA_SOM_Departments where DeptCd=@vDeptCdSaved
	if @vDeptCdOverride is not null
		select @vDeptCdOverrideLevel=DeptLevel from vw_COA_SOM_Departments where DeptCd=@vDeptCdOverride 

	Declare vCur CURSOR fast_forward read_only LOCAL FOR
		Select ChartStrField, ChartStrValue, [Except], DeptLevel, FundLevel  
			from [vw_SOM_BFA_SavedChartFieldFilters] 
			where UserId=@vUserId and FilterName=@vFilterName and DeptCdSaved=@vDeptCdSaved and ChartStrField<>'DeptCdSaved' 
			and ChartStrField<>case when @vDeptCdOverride is null then 'xxxxx' else 'DeptCd' end 
			order by ChartStrField, DeptLevel, FundLevel 
	
	Open vCur
	Fetch Next From vCur into @vChartStrField, @vChartStrValue, @vExcept, @vDeptLevel, @vFundLevel

	While @@FETCH_STATUS=0
		begin
		if @vChartStrField='DeptCd' and @vExcept='+' 
			begin
			if @vDeptLevel=1 
				set @vListDeptCd1 = @vListDeptCd1 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=2 
				set @vListDeptCd2 = @vListDeptCd2 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=3 
				set @vListDeptCd3 = @vListDeptCd3 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=4 
				set @vListDeptCd4 = @vListDeptCd4 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=5 
				set @vListDeptCd5 = @vListDeptCd5 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=6 
				set @vListDeptCd6 = @vListDeptCd6 + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'DeptSite' and @vExcept='+' 
			begin
			set @vListDeptSite = @vListDeptSite + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'FundCd' and @vExcept='+' 
			begin
			if @vFundLevel=1
				set @vListFundCd1 = @vListFundCd1 + '~''' + @vChartStrValue + ''''
			if @vFundLevel=2
				set @vListFundCd2 = @vListFundCd2 + '~''' + @vChartStrValue + ''''
			if @vFundLevel=3
				set @vListFundCd3 = @vListFundCd3 + '~''' + @vChartStrValue + ''''
			if @vFundLevel=4
				set @vListFundCd4 = @vListFundCd4 + '~''' + @vChartStrValue + ''''
			if @vFundLevel>4
				set @vListFundCd = @vListFundCd + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'ProjectManagerCd' and @vExcept='+' 
			begin
			set @vListProjectManagerCd = @vListProjectManagerCd + '~''' + case when @vChartStrValue like '%(%' then left(right(@vChartStrValue,10),9) else @vChartStrValue end + ''''
			end

		else if @vChartStrField = 'ProjectUseShort' and @vExcept='+' 
			begin
			set @vListProjectUseShort = @vListProjectUseShort + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'ProjectCd' and @vExcept='+' 
			begin
			set @vListProjectCd = @vListProjectCd + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'FunctionCd' and @vExcept='+' 
			begin
			set @vListFunctionCd = @vListFunctionCd + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'FlexCd' and @vExcept='+' and @vExcept='+' 
			begin
			set @vListFlexCd = @vListFlexCd + '~''' + @vChartStrValue + ''''
			end
		
		-- Excepts
		if @vChartStrField='DeptCd' and @vExcept='-'
			begin
			if @vDeptLevel=1 
				set @vListXDeptCd1 = @vListXDeptCd1 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=2 
				set @vListXDeptCd2 = @vListXDeptCd2 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=3 
				set @vListXDeptCd3 = @vListXDeptCd3 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=4 
				set @vListXDeptCd4 = @vListXDeptCd4 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=5 
				set @vListXDeptCd5 = @vListXDeptCd5 + '~''' + @vChartStrValue + ''''
			if @vDeptLevel=6 
				set @vListXDeptCd6 = @vListXDeptCd6 + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'DeptSite' and @vExcept='-'
			begin
			set @vListXDeptSite = @vListXDeptSite + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'FundCd' and @vExcept='-'
			begin
			if @vFundLevel=1
				set @vListXFundCd1 = @vListXFundCd1 + '~''' + @vChartStrValue + ''''
			if @vFundLevel=2
				set @vListXFundCd2 = @vListXFundCd2 + '~''' + @vChartStrValue + ''''
			if @vFundLevel=3
				set @vListXFundCd3 = @vListXFundCd3 + '~''' + @vChartStrValue + ''''
			if @vFundLevel=4
				set @vListXFundCd4 = @vListXFundCd4 + '~''' + @vChartStrValue + ''''
			if @vFundLevel>4
				set @vListXFundCd = @vListXFundCd + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'ProjectManagerCd' and @vExcept='-'
			begin
			set @vListXProjectManagerCd = @vListXProjectManagerCd + '~''' + case when @vChartStrValue like '%(%' then left(right(@vChartStrValue,10),9) else @vChartStrValue end + ''''
			end

		else if @vChartStrField = 'ProjectUseShort' and @vExcept='-'
			begin
			set @vListXProjectUseShort = @vListXProjectUseShort + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'ProjectCd' and @vExcept='-'
			begin
			set @vListXProjectCd = @vListXProjectCd + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'FunctionCd' and @vExcept='-'
			begin
			set @vListXFunctionCd = @vListXFunctionCd + '~''' + @vChartStrValue + ''''
			end

		else if @vChartStrField = 'FlexCd' and @vExcept='-'and @vExcept='-'
			begin
			set @vListXFlexCd = @vListXFlexCd + '~''' + @vChartStrValue + ''''
			end

		Fetch Next From vCur into @vChartStrField, @vChartStrValue, @vExcept, @vDeptLevel, @vFundLevel
		end

	Close vCur
	deallocate vCur

	-- 
	if @vReturnType<>0 
		begin 
		set @vSQL = ''
		if @vDeptCdOverride is null 
			begin 
			if @vListDeptCd1<>'' set @vSQL = @vSQL + '~DeptLevel1Cd' + @vListDeptCd1 
			if @vListDeptCd2<>'' set @vSQL = @vSQL + '~DeptLevel2Cd' + @vListDeptCd2 
			if @vListDeptCd3<>'' set @vSQL = @vSQL + '~DeptLevel3Cd' + @vListDeptCd3 
			if @vListDeptCd4<>'' set @vSQL = @vSQL + '~DeptLevel4Cd' + @vListDeptCd4 
			if @vListDeptCd5<>'' set @vSQL = @vSQL + '~DeptLevel5Cd' + @vListDeptCd5 
			if @vListDeptCd6<>'' set @vSQL = @vSQL + '~DeptLevel6Cd' + @vListDeptCd6 
			end
		else
			begin
			if @vDeptCdOverride<>'' set @vSQL = @vSQL + '~DeptLevel' + cast(@vDeptCdOverrideLevel as varchar(1)) + 'Cd~' + @vDeptCdOverride 
			end
		if @vListDeptSite<>'' set @vSQL = @vSQL + '~DeptSite' + @vListDeptSite 
		if @vListFundCd1<>'' set @vSQL = @vSQL + '~FundLevelACd' + @vListFundCd1 
		if @vListFundCd2<>'' set @vSQL = @vSQL + '~FundLevelBCd' + @vListFundCd2 
		if @vListFundCd3<>'' set @vSQL = @vSQL + '~FundLevelCCd' + @vListFundCd3 
		if @vListFundCd4<>'' set @vSQL = @vSQL + '~FundLevelDCd' + @vListFundCd4 
		if @vListFundCd<>'' set @vSQL = @vSQL + '~FundCd' + @vListFundCd 
		if @vListProjectManagerCd<>'' set @vSQL = @vSQL + '~ProjectManagerCd' + @vListProjectManagerCd 
		if @vListProjectUseShort<>'' set @vSQL = @vSQL + '~ProjectUseShort' + @vListProjectUseShort 
		if @vListProjectCd<>'' set @vSQL = @vSQL + '~ProjectCd' + @vListProjectCd 
		if @vListFunctionCd<>'' set @vSQL = @vSQL + '~FunctionCd' + @vListFunctionCd 
		if @vListFlexCd<>'' set @vSQL = @vSQL + '~FlexCd' + @vListFlexCd 
		return replace(@vSQL , '''', '') 
		end 

	-- -- Now build SQL Parts 

	-- DeptCdSaved 
	if @vDeptCdOverride is null 
		set @vSQLDeptCdSaved = '(' 
			+ 'DeptLevel' + cast(@vDeptCdSavedLevel as varchar(1)) + 'Cd=''' + @vDeptCdSaved + '''' 
			+ ')'
	else
		set @vSQLDeptCdSaved = '(' 
			+ 'DeptLevel' + cast(@vDeptCdOverrideLevel as varchar(1)) + 'Cd=''' + @vDeptCdOverride + '''' 
			+ ')' 

	-- DeptCds 
	set @vSQLDept = '(' 
		+ case when @vListDeptCd1='' then '' 	
			when len(@vListDeptCd1)-len(replace(@vListDeptCd1,'~',''))=1 then ' or DeptLevel1Cd=' + replace(substring(@vListDeptCd1,2,512), '~', ',')  
			else ' or DeptLevel1Cd in (' + replace(substring(@vListDeptCd1,2,512), '~', ',') + ')' 
			end 
		+ case when @vListDeptCd2='' then '' 	
			when len(@vListDeptCd2)-len(replace(@vListDeptCd2,'~',''))=1 then ' or DeptLevel2Cd=' + replace(substring(@vListDeptCd2,2,512), '~', ',')  
			else ' or DeptLevel2Cd in (' + replace(substring(@vListDeptCd2,2,512), '~', ',') + ')' 
			end 
		+ case when @vListDeptCd3='' then '' 	
			when len(@vListDeptCd3)-len(replace(@vListDeptCd3,'~',''))=1 then ' or DeptLevel3Cd=' + replace(substring(@vListDeptCd3,2,512), '~', ',')  
			else ' or DeptLevel3Cd in (' + replace(substring(@vListDeptCd3,2,512), '~', ',') + ')' 
			end 
		+ case when @vListDeptCd4='' then '' 	
			when len(@vListDeptCd4)-len(replace(@vListDeptCd4,'~',''))=1 then ' or DeptLevel4Cd=' + replace(substring(@vListDeptCd4,2,512), '~', ',')  
			else ' or DeptLevel4Cd in (' + replace(substring(@vListDeptCd4,2,512), '~', ',') + ')' 
			end 
		+ case when @vListDeptCd5='' then '' 	
			when len(@vListDeptCd5)-len(replace(@vListDeptCd5,'~',''))=1 then ' or DeptLevel5Cd=' + replace(substring(@vListDeptCd5,2,512), '~', ',')  
			else ' or DeptLevel5Cd in (' + replace(substring(@vListDeptCd5,2,512), '~', ',') + ')' 
			end 
		+ case when @vListDeptCd6='' then '' 	
			when len(@vListDeptCd6)-len(replace(@vListDeptCd6,'~',''))=1 then ' or DeptLevel6Cd=' + replace(substring(@vListDeptCd6,2,512), '~', ',')  
			else ' or DeptLevel6Cd in (' + replace(substring(@vListDeptCd6,2,512), '~', ',') + ')' 
			end 
		+ ')' 
	set @vSQLDept = replace(@vSQLDept, '( or Dept', '(Dept')

	set @vSQLDeptX = '(' 
		+ case when @vListXDeptCd1='' then '' 	
			when len(@vListXDeptCd1)-len(replace(@vListXDeptCd1,'~',''))=1 then ' or DeptLevel1Cd=' + replace(substring(@vListXDeptCd1,2,512), '~', ',')  
			else ' or DeptLevel1Cd in (' + replace(substring(@vListXDeptCd1,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXDeptCd2='' then '' 	
			when len(@vListXDeptCd2)-len(replace(@vListXDeptCd2,'~',''))=1 then ' or DeptLevel2Cd=' + replace(substring(@vListXDeptCd2,2,512), '~', ',')  
			else ' or DeptLevel2Cd in (' + replace(substring(@vListXDeptCd2,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXDeptCd3='' then '' 	
			when len(@vListXDeptCd3)-len(replace(@vListXDeptCd3,'~',''))=1 then ' or DeptLevel3Cd=' + replace(substring(@vListXDeptCd3,2,512), '~', ',')  
			else ' or DeptLevel3Cd in (' + replace(substring(@vListXDeptCd3,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXDeptCd4='' then '' 	
			when len(@vListXDeptCd4)-len(replace(@vListXDeptCd4,'~',''))=1 then ' or DeptLevel4Cd=' + replace(substring(@vListXDeptCd4,2,512), '~', ',')  
			else ' or DeptLevel4Cd in (' + replace(substring(@vListXDeptCd4,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXDeptCd5='' then '' 	
			when len(@vListXDeptCd5)-len(replace(@vListXDeptCd5,'~',''))=1 then ' or DeptLevel5Cd=' + replace(substring(@vListXDeptCd5,2,512), '~', ',')  
			else ' or DeptLevel5Cd in (' + replace(substring(@vListXDeptCd5,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXDeptCd6='' then '' 	
			when len(@vListXDeptCd6)-len(replace(@vListXDeptCd6,'~',''))=1 then ' or DeptLevel6Cd=' + replace(substring(@vListXDeptCd6,2,512), '~', ',')  
			else ' or DeptLevel6Cd in (' + replace(substring(@vListXDeptCd6,2,512), '~', ',') + ')' 
			end 
		+ ')' 
	set @vSQLDeptX = replace(@vSQLDeptX, '( or Dept', '(Dept')

	-- DeptSite
	set @vSQLDeptSite = '(' + 
		+ case when @vListDeptSite='' then '' 	
			when len(@vListDeptSite)-len(replace(@vListDeptSite,'~',''))=1 then ' and DeptSite=' + replace(substring(@vListDeptSite,2,512), '~', ',')  
			else ' and DeptSite in (' + replace(substring(@vListDeptSite,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQLDeptSiteX = '(' + 
		+ case when @vListXDeptSite='' then '' 	
			when len(@vListXDeptSite)-len(replace(@vListXDeptSite,'~',''))=1 then ' and DeptSite=' + replace(substring(@vListXDeptSite,2,512), '~', ',')  
			else ' and DeptSite in (' + replace(substring(@vListXDeptSite,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	-- Fund
	set @vSQLFund = '(' + 
		+ case when @vListFundCd1='' then '' 	
			when len(@vListFundCd1)-len(replace(@vListFundCd1,'~',''))=1 then ' or FundLevelACd=' + replace(substring(@vListFundCd1,2,512), '~', ',')  
			else ' or FundLevelACd in (' + replace(substring(@vListFundCd1,2,512), '~', ',') + ')' 
			end 
		+ case when @vListFundCd2='' then '' 	
			when len(@vListFundCd2)-len(replace(@vListFundCd2,'~',''))=1 then ' or FundLevelBCd=' + replace(substring(@vListFundCd2,2,512), '~', ',')  
			else ' or FundLevelBCd in (' + replace(substring(@vListFundCd2,2,512), '~', ',') + ')' 
			end 
		+ case when @vListFundCd3='' then '' 	
			when len(@vListFundCd3)-len(replace(@vListFundCd3,'~',''))=1 then ' or FundLevelCCd=' + replace(substring(@vListFundCd3,2,512), '~', ',')  
			else ' or FundLevelCCd in (' + replace(substring(@vListFundCd3,2,512), '~', ',') + ')' 
			end 
		+ case when @vListFundCd4='' then '' 	
			when len(@vListFundCd4)-len(replace(@vListFundCd4,'~',''))=1 then ' or FundLevelDCd=' + replace(substring(@vListFundCd4,2,512), '~', ',')  
			else ' or FundLevelDCd in (' + replace(substring(@vListFundCd4,2,512), '~', ',') + ')' 
			end 
		+ case when @vListFundCd='' then '' 	
			when len(@vListFundCd)-len(replace(@vListFundCd,'~',''))=1 then ' or FundCd=' + replace(substring(@vListFundCd,2,512), '~', ',')  
			else ' or FundCd in (' + replace(substring(@vListFundCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 
	set @vSQLFund = replace(@vSQLFund, '( or Fund', '(Fund')

	set @vSQLFundX = '(' + 
		+ case when @vListXFundCd1='' then '' 	
			when len(@vListXFundCd1)-len(replace(@vListXFundCd1,'~',''))=1 then ' or FundLevelACd=' + replace(substring(@vListXFundCd1,2,512), '~', ',')  
			else ' or FundLevelACd in (' + replace(substring(@vListXFundCd1,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXFundCd2='' then '' 	
			when len(@vListXFundCd2)-len(replace(@vListXFundCd2,'~',''))=1 then ' or FundLevelBCd=' + replace(substring(@vListXFundCd2,2,512), '~', ',')  
			else ' or FundLevelBCd in (' + replace(substring(@vListXFundCd2,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXFundCd3='' then '' 	
			when len(@vListXFundCd3)-len(replace(@vListXFundCd3,'~',''))=1 then ' or FundLevelCCd=' + replace(substring(@vListXFundCd3,2,512), '~', ',')  
			else ' or FundLevelCCd in (' + replace(substring(@vListXFundCd3,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXFundCd4='' then '' 	
			when len(@vListXFundCd4)-len(replace(@vListXFundCd4,'~',''))=1 then ' or FundLevelDCd=' + replace(substring(@vListXFundCd4,2,512), '~', ',')  
			else ' or FundLevelDCd in (' + replace(substring(@vListXFundCd4,2,512), '~', ',') + ')' 
			end 
		+ case when @vListXFundCd='' then '' 	
			when len(@vListXFundCd)-len(replace(@vListXFundCd,'~',''))=1 then ' or FundCd=' + replace(substring(@vListXFundCd,2,512), '~', ',')  
			else ' or FundCd in (' + replace(substring(@vListXFundCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 
	set @vSQLFundX = replace(@vSQLFundX, '( or Fund', '(Fund')
	-- ProjectManagerCd 
	set @vSQLProjectManagerCd = '(' 
		+ case when @vListProjectManagerCd='' then '' 	
			when len(@vListProjectManagerCd)-len(replace(@vListProjectManagerCd,'~',''))=1 then ' and ProjectManagerCd=' + replace(substring(@vListProjectManagerCd,2,512), '~', ',')  
			else ' and ProjectManagerCd in (' + replace(substring(@vListProjectManagerCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQLProjectManagerCdX = '(' 
		+ case when @vListXProjectManagerCd='' then '' 	
			when len(@vListXProjectManagerCd)-len(replace(@vListXProjectManagerCd,'~',''))=1 then ' and ProjectManagerCd=' + replace(substring(@vListXProjectManagerCd,2,512), '~', ',')  
			else ' and ProjectManagerCd in (' + replace(substring(@vListXProjectManagerCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 
	
	-- ProjectUseShort
	set @vSQLProjectUseShort = '(' 
		+ case when @vListProjectUseShort='' then '' 	
			when len(@vListProjectUseShort)-len(replace(@vListProjectUseShort,'~',''))=1 then ' and ProjectUseShort=' + replace(substring(@vListProjectUseShort,2,512), '~', ',')  
			else ' and ProjectUseShort in (' + replace(substring(@vListProjectUseShort,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQLProjectUseShortX = '(' 
		+ case when @vListXProjectUseShort='' then '' 	
			when len(@vListXProjectUseShort)-len(replace(@vListXProjectUseShort,'~',''))=1 then ' and ProjectUseShort=' + replace(substring(@vListXProjectUseShort,2,512), '~', ',')  
			else ' and ProjectUseShort in (' + replace(substring(@vListXProjectUseShort,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	-- ProjectCd 
	set @vSQLProjectCd = '(' 
		+ case when @vListProjectCd='' then '' 	
			when len(@vListProjectCd)-len(replace(@vListProjectCd,'~',''))=1 then ' and ProjectCd=' + replace(substring(@vListProjectCd,2,512), '~', ',')  
			else ' and ProjectCd in (' + replace(substring(@vListProjectCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQLProjectCdX = '(' 
		+ case when @vListXProjectCd='' then '' 	
			when len(@vListXProjectCd)-len(replace(@vListXProjectCd,'~',''))=1 then ' and ProjectCd=' + replace(substring(@vListXProjectCd,2,512), '~', ',')  
			else ' and ProjectCd in (' + replace(substring(@vListXProjectCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	-- FunctionCd
	set @vSQLFunctionCd = '(' 
		+ case when @vListFunctionCd='' then '' 	
			when len(@vListFunctionCd)-len(replace(@vListFunctionCd,'~',''))=1 then ' and FunctionCd=' + replace(substring(@vListFunctionCd,2,512), '~', ',')  
			else ' and FunctionCd in (' + replace(substring(@vListFunctionCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQLFunctionCdX = '(' 
		+ case when @vListXFunctionCd='' then '' 	
			when len(@vListXFunctionCd)-len(replace(@vListXFunctionCd,'~',''))=1 then ' and FunctionCd=' + replace(substring(@vListXFunctionCd,2,512), '~', ',')  
			else ' and FunctionCd in (' + replace(substring(@vListXFunctionCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	-- FlexCd
	set @vSQLFlexCd = '(' 
		+ case when @vListFlexCd='' then '' 	
			when len(@vListFlexCd)-len(replace(@vListFlexCd,'~',''))=1 then ' and FlexCd=' + replace(substring(@vListFlexCd,2,512), '~', ',')  
			else ' and FlexCd in (' + replace(substring(@vListFlexCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQLFlexCdX = '(' 
		+ case when @vListXFlexCd='' then '' 	
			when len(@vListXFlexCd)-len(replace(@vListXFlexCd,'~',''))=1 then ' and FlexCd=' + replace(substring(@vListXFlexCd,2,512), '~', ',')  
			else ' and FlexCd in (' + replace(substring(@vListXFlexCd,2,512), '~', ',') + ')' 
			end 
		+ ')' 

	set @vSQL = '1=1 and ' 
		+ '' + iif(@vSQLDept='()', @vSQLDeptCdSaved, @vSQLDept) + ' and not ' + @vSQLDeptX + ''
		+ ' and ' + @vSQLDeptSite + ' and not ' + @vSQLDeptSiteX + ''
		+ ' and ' + @vSQLFund + ' and not ' + @vSQLFundX + '' 
		+ ' and ' + @vSQLProjectManagerCd + ' and not ' + @vSQLProjectManagerCdX + ''
		+ ' and ' + @vSQLProjectUseShort + ' and not ' + @vSQLProjectUseShortX + ''
		+ ' and ' + @vSQLProjectCd + ' and not ' + @vSQLProjectCdX + '' 
		+ ' and ' + @vSQLFunctionCd + ' and not ' + @vSQLFunctionCdX + '' 
		+ ' and ' + @vSQLFlexCd + ' and not ' + @vSQLFlexCdX + ''

	set @vSQL = replace(@vSQL, ' and () and not ()', '')
	set @vSQL = replace(@vSQL, ' and () and not (', ' and not (')
	set @vSQL = replace(@vSQL, ' and not ()', '')
	set @vSQL = replace(@vSQL, '( and ', '(')
	set @vSQL = replace(@vSQL, '1=1 and ', '')
	
	set @vSQL = replace(@vSQL, 'FundLevelACd', 'ltrim(rtrim(FundLevelACd))')
	set @vSQL = replace(@vSQL, 'FundLevelBCd', 'ltrim(rtrim(FundLevelBCd))')
	set @vSQL = replace(@vSQL, 'FundLevelCCd', 'ltrim(rtrim(FundLevelCCd))')
	set @vSQL = replace(@vSQL, 'FundLevelDCd', 'ltrim(rtrim(FundLevelDCd))')
	return @vSQL 
END





