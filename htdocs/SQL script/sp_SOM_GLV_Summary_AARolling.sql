USE [GLVData]
GO
/****** Object:  StoredProcedure [dbo].[sp_SOM_GLV_Summary_AARolling]    Script Date: 10/19/2018 2:20:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER PROCEDURE [dbo].[sp_SOM_GLV_Summary_AARolling] 
	@vSessionUserid varchar(30), 
	@vDeptCdGLV varchar(6), 
	@vDeptCdOverride varchar(6), 
	@vBusinessUnitCd varchar(5), 
	@vDeptSite varchar(4), 
	@vUserId varchar(30),  
	@vFilterName varchar(50), 
	@vFY smallint, 
	@vFP smallint, 
	@vWithEmp smallint=1 
AS
BEGIN
	-- exec sp_SOM_GLV_Summary_AARolling_TEST 'mkincaid', '147100', '147100', 'SFCMP', '%', 'mkincaid', '(default)', 2018, 5, 1  

	SET NOCOUNT ON;

	declare @vSessionDate as datetime 
	declare @vSQL as nvarchar(2048)
	declare @vWhere as varchar(1024) 

	declare @vSysFYFP as varchar(7), @vSysFY as smallint, @vSysFP as smallint, @vEmpFP as smallint  
	select @vSysFYFP=[String] from som_bfa_variables where variable='ActiveFYFP' 
	select @vSysFY=[Integer] from som_bfa_variables where variable='ActiveFY' 
	select @vSysFP=[Integer] from som_bfa_variables where variable='ActiveFP' 
	if @vFY=@vSysFY 
		set @vEmpFP = @vSysFP 
	else 
		set @vEmpFP = 12

	---------------------------------------------------
	-- Clear out prior session records 
	---------------------------------------------------

	select @vSessionDate=getdate() 
	delete from SOM_AA_Dashboard where SessionUserid=@vSessionUserid 
	delete from SOM_AA_TransactionSummary where SessionUserid=@vSessionUserid 
	delete from SOM_AA_EmployeeListRolling where SessionUserid=@vSessionUserid 
	if @vDeptSite='*' 
		select @vDeptSite='%' 

	---------------------------------------------------
	-- Build GL transaction data summary 
	---------------------------------------------------

	create table #t (
		FiscalYear smallint,
		FiscalPeriod smallint,
		ReconGroupTitle varchar(20),
		ReconItemCd smallint,
		ReconItemTitle varchar(50),
		ReconAssignCd smallint,
		ReconStatusCd smallint,
		ReconStatusTitle varchar(50),
		Amt money, 
		Cnt Integer
		)

	create table #t1 (
		FiscalYear smallint,
		ReconGroupTitle varchar(20),
		ReconItemTitle varchar(50),
		ReconItemCd smallint,
		StatusAmt0 money,     -- Current assigned status of 0000 (NotVerified)
		StatusCnt0 Integer,   
		StatusAmt1 money,     -- Current assigned status of 1000 (PendingComplete)
		StatusCnt1 Integer,   
		StatusAmt2 money,     -- Current assigned status of 2000 (AutoComplete) 
		StatusCnt2 Integer,   
		StatusAmt3 money,     -- Current assigned status of 3000 (Complete) 
		StatusCnt3 Integer,   
		PriorStatusAmt0 money,    -- Prior period : Current assigned status of 0000 (NotVerified)
		PriorStatusCnt0 Integer,  
		PriorStatusAmt1 money,    -- Prior period : Current assigned status of 1000 (PendingComplete)
		PriorStatusCnt1 Integer,  
		AmtM01 money, 
		AmtM02 money, 
		AmtM03 money, 
		AmtM04 money, 
		AmtM05 money, 
		AmtM06 money, 
		AmtM07 money, 
		AmtM08 money, 
		AmtM09 money, 
		AmtM10 money, 
		AmtM11 money, 
		AmtM12 money, 
		IncM01 integer, 
		IncM02 integer, 
		IncM03 integer, 
		IncM04 integer, 
		IncM05 integer, 
		IncM06 integer, 
		IncM07 integer, 
		IncM08 integer, 
		IncM09 integer, 
		IncM10 integer, 
		IncM11 integer, 
		IncM12 integer 
		)

	-- Build dynamic SQL 
	set @vSQL = 'INSERT INTO #t ' + char(10) 
	set @vSQL = @vSQL + 'SELECT 0 as FiscalYear, FiscalPeriod, ReconGroupTitle, ReconItemCd, ReconItemTitle, ReconAssignCd, ReconStatusCd, ReconStatusTitle, sum(Amount) as Amt, sum(1) as Cnt ' + char(10) 
	set @vSQL = @vSQL + '   FROM vw_COA_Report_Ledger_Details ' + char(10) 
	set @vSQL = @vSQL + '   WHERE (FiscalYear=([vFY]-1) AND FiscalPeriod Between [vFP]+1 and 12 ' + char(10) 
	set @vSQL = @vSQL + '       OR FiscalYear=[vFY] AND FiscalPeriod Between 1 And [vFP]) ' + char(10) 
	set @vSQL = @vSQL + '   	AND BusinessUnitCd = ''[vBusinessUnitCd]'' ' + char(10) 
	if @vDeptSite <> '%' 
		begin 
		set @vSQL = @vSQL + '   	AND DeptSite = ''[vDeptSite]'' ' + char(10) 
		end 
	set @vSQL = @vSQL + '   	AND AccountLevelACd In (''4000A'',''5000A'',''5700A'') ' + char(10) 
	set @vSQL = @vSQL + '   	AND [vWhere] ' + char(10) 
	set @vSQL = @vSQL + '   GROUP BY FiscalYear, FiscalPeriod, ReconGroupTitle, ReconItemTitle, ReconItemCd, ReconAssignCd, ReconStatusTitle, ReconStatusCd ' + char(10) 

	select @vWhere = dbo.fn_SOM_BFA_GetWhereFromSavedFilter (@vUserId, @vDeptCdGLV, @vDeptCdOverride, @vFilterName, 0) 

	set @vSQL = replace(@vSQL, '[vFY]', @vFY)
	set @vSQL = replace(@vSQL, '[vFP]', @vFP)
	set @vSQL = replace(@vSQL, '[vBusinessUnitCd]', @vBusinessUnitCd)
	set @vSQL = replace(@vSQL, '[vDeptSite]', @vDeptSite)
	set @vSQL = replace(@vSQL, '[vWhere]', @vWhere)

	--return @vSQL 

	-- Execute the SQL statement creating a temp table #t 
	exec sp_executesql @vSQL; 

	-- This pivots the data by month 
	-- Rolling 12 Months and reversed order.  Exactly 12 months are returned.  
	-- M01 is the current period, M02 is the prior period, M03 is the prior-prior, ... M12 is 11 months ago 
	-- Current period in this sense is the FY and FP passed into the stored procedure 
	insert into #t1 
		select FiscalYear, ReconGroupTitle, ReconItemTitle, ReconItemCd, 
			sum(case when ReconStatusCd=0 and FiscalPeriod=@vFP then Amt else 0 end), 
			sum(case when ReconStatusCd=0 and FiscalPeriod=@vFP then Cnt else 0 end), 
			sum(case when ReconStatusCd=1000 and FiscalPeriod=@vFP then Amt else 0 end), 
			sum(case when ReconStatusCd=1000 and FiscalPeriod=@vFP then Cnt else 0 end), 
			sum(case when ReconStatusCd=2000 and FiscalPeriod=@vFP then Amt else 0 end), 
			sum(case when ReconStatusCd=2000 and FiscalPeriod=@vFP then Cnt else 0 end), 
			sum(case when ReconStatusCd=3000 and FiscalPeriod=@vFP then Amt else 0 end), 
			sum(case when ReconStatusCd=3000 and FiscalPeriod=@vFP then Cnt else 0 end), 
			sum(case when ReconStatusCd=0 and FiscalPeriod<@vFP then Amt else 0 end), 
			sum(case when ReconStatusCd=0 and FiscalPeriod<@vFP then Cnt else 0 end), 
			sum(case when ReconStatusCd=1000 and FiscalPeriod<@vFP then Amt else 0 end), 
			sum(case when ReconStatusCd=1000 and FiscalPeriod<@vFP then Cnt else 0 end), 
			sum(case when FiscalPeriod=((@vFP + 11) % 12) + 1 then Amt else 0 end),  -- current month
			sum(case when FiscalPeriod=((@vFP + 10) % 12) + 1 then Amt else 0 end),  -- 1 month ago
			sum(case when FiscalPeriod=((@vFP + 9) % 12) + 1 then Amt else 0 end),  -- 2 months ago
			sum(case when FiscalPeriod=((@vFP + 8) % 12) + 1 then Amt else 0 end),  -- 3 months ago
			sum(case when FiscalPeriod=((@vFP + 7) % 12) + 1 then Amt else 0 end),  -- 4 months ago
			sum(case when FiscalPeriod=((@vFP + 6) % 12) + 1 then Amt else 0 end),  -- 5 months ago
			sum(case when FiscalPeriod=((@vFP + 5) % 12) + 1 then Amt else 0 end),  -- 6 months ago
			sum(case when FiscalPeriod=((@vFP + 4) % 12) + 1 then Amt else 0 end),  -- 7 months ago
			sum(case when FiscalPeriod=((@vFP + 3) % 12) + 1 then Amt else 0 end),  -- 8 months ago
			sum(case when FiscalPeriod=((@vFP + 2) % 12) + 1 then Amt else 0 end),  -- 9 months ago
			sum(case when FiscalPeriod=((@vFP + 1) % 12) + 1 then Amt else 0 end),  -- 10 months ago
			sum(case when FiscalPeriod=((@vFP + 0) % 12) + 1 then Amt else 0 end),  -- 11 months ago 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 11) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 10) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 9) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 8) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 7) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 6) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 5) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 4) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 3) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 2) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 1) % 12) + 1 then Cnt else 0 end), 
			sum(case when ReconStatusCd<2000 and FiscalPeriod=((@vFP + 0) % 12) + 1 then Cnt else 0 end) 
			from #t 
		group by FiscalYear, ReconGroupTitle, ReconItemTitle, ReconItemCd 

	-- Save transaction summary data to SessionUserid tables 

	-- Dashboard table 
	INSERT INTO SOM_AA_Dashboard ( 
		SessionUserid, SessionDate, 
		FiscalYear, ReconGroupTitle, 
		StatusAmt0, StatusCnt0, StatusAmt1, StatusCnt1, StatusAmt2, StatusCnt2, StatusAmt3, StatusCnt3, 
		PriorStatusAmt0, PriorStatusCnt0, PriorStatusAmt1, PriorStatusCnt1, 
		AmtM01, AmtM02, AmtM03, AmtM04, AmtM05, AmtM06, AmtM07, AmtM08, AmtM09, AmtM10, AmtM11, AmtM12, 
		IncM01, IncM02, IncM03, IncM04, IncM05, IncM06, IncM07, IncM08, IncM09, IncM10, IncM11, IncM12 
		)
		SELECT @vSessionUserid, @vSessionDate, 
			T1.FiscalYear, T1.ReconGroupTitle, 
			sum(T1.StatusAmt0), sum(T1.StatusCnt0), sum(T1.StatusAmt1), sum(T1.StatusCnt1), 
			sum(T1.StatusAmt2), sum(T1.StatusCnt2), sum(T1.StatusAmt3), sum(T1.StatusCnt3), 
			sum(T1.PriorStatusAmt0), sum(T1.PriorStatusCnt0), sum(T1.PriorStatusAmt1), sum(T1.PriorStatusCnt1), 
			sum(T1.AmtM01), sum(T1.AmtM02), sum(T1.AmtM03), sum(T1.AmtM04), sum(T1.AmtM05), sum(T1.AmtM06), 
			sum(T1.AmtM07), sum(T1.AmtM08), sum(T1.AmtM09), sum(T1.AmtM10), sum(T1.AmtM11), sum(T1.AmtM12), 
			sum(T1.IncM01), sum(T1.IncM02), sum(T1.IncM03), sum(T1.IncM04), sum(T1.IncM05), sum(T1.IncM06), 
			sum(T1.IncM07), sum(T1.IncM08), sum(T1.IncM09), sum(T1.IncM10), sum(T1.IncM11), sum(T1.IncM12) 
			FROM #t1 as T1
			group by T1.FiscalYear, T1.ReconGroupTitle;

	-- Display details
	INSERT INTO SOM_AA_TransactionSummary ( 
		SessionUserid, SessionDate, 
		ReconGroupTitle, Sort1, Sort2, ReconItemTitle, ReconItemCd, 
		NotVerified, Pending, AutoComplete, Complete, PriorNotVerified, PriorNotVerifiedCount, PriorPending, PriorPendingCount, 
		AmtM01x, AmtM02x, AmtM03x, AmtM04x, AmtM05x, AmtM06x, AmtM07x, AmtM08x, AmtM09x, AmtM10x, AmtM11x, AmtM12x, AmtTotx, 
		NotVerifiedCount, PendingCount, AutoCompleteCount, CompleteCount 
		)
		SELECT @vSessionUserid, @vSessionDate, 
			T1.ReconGroupTitle, T1.ReconGroupTitle AS Sort1, 1 AS Sort2, T1.ReconItemTitle, T1.ReconItemCd, 
			T1.StatusAmt0, T1.StatusAmt1, T1.StatusAmt2, T1.StatusAmt3, T1.PriorStatusAmt0, T1.PriorStatusCnt0, T1.PriorStatusAmt1, T1.PriorStatusCnt1, 
			T1.AmtM01 AS AmtM01x, T1.AmtM02 AS AmtM02x, T1.AmtM03 AS AmtM03x, T1.AmtM04 AS AmtM04x, T1.AmtM05 AS AmtM05x, T1.AmtM06 AS AmtM06x, 
			T1.AmtM07 AS AmtM07x, T1.AmtM08 AS AmtM08x, T1.AmtM09 AS AmtM09x, T1.AmtM10 AS AmtM10x, T1.AmtM11 AS AmtM11x, T1.AmtM12 AS AmtM12x, 
			[AmtM01]+[AmtM02]+[AmtM03]+[AmtM04]+[AmtM05]+[AmtM06]+[AmtM07]+[AmtM08]+[AmtM09]+[AmtM10]+[AmtM11]+[AmtM12] AS AmtTotx, 
			T1.StatusCnt0, T1.StatusCnt1, T1.StatusCnt2, T1.StatusCnt3
			FROM #t1 as T1; 

	-- Display subtotal 
	INSERT INTO SOM_AA_TransactionSummary ( 
		SessionUserid, SessionDate, 
		ReconGroupTitle, Sort1, Sort2, ReconItemTitle, ReconItemCd, 
		NotVerified, Pending, AutoComplete, Complete, PriorNotVerified, PriorPending, 
		AmtM01x, AmtM02x, AmtM03x, AmtM04x, AmtM05x, AmtM06x, AmtM07x, AmtM08x, AmtM09x, AmtM10x, AmtM11x, AmtM12x, AmtTotx, 
		NotVerifiedCount, PendingCount, AutoCompleteCount, CompleteCount,PriorNotVerifiedCount,PriorPendingCount )
		SELECT @vSessionUserid, @vSessionDate, 
			T1.ReconGroupTitle, T1.ReconGroupTitle AS Sort1, 2 AS Sort2, 'Total' AS ReconItemTitle, 0 AS ReconItemCd, 
			Sum(T1.StatusAmt0) AS SumOfStatusAmt0, Sum(T1.StatusAmt1) AS SumOfStatusAmt1, Sum(T1.StatusAmt2) AS SumOfStatusAmt21, Sum(T1.StatusAmt3) AS SumOfStatusAmt3, 
			Sum(T1.PriorStatusAmt0) AS SumOfPriorStatusAmt0, Sum(T1.PriorStatusAmt1) AS SumOfPriorStatusAmt1, 
			Sum(T1.AmtM01) AS AmtM01x, Sum(T1.AmtM02) AS AmtM02x, Sum(T1.AmtM03) AS AmtM03x, Sum(T1.AmtM04) AS AmtM04x, Sum(T1.AmtM05) AS AmtM05x, Sum(T1.AmtM06) AS AmtM06x, 
			Sum(T1.AmtM07) AS AmtM07x, Sum(T1.AmtM08) AS AmtM08x, Sum(T1.AmtM09) AS AmtM09x, Sum(T1.AmtM10) AS AmtM10x, Sum(T1.AmtM11) AS AmtM11x, Sum(T1.AmtM12) AS AmtM12x, 
			Sum(([AmtM01]+[AmtM02]+[AmtM03]+[AmtM04]+[AmtM05]+[AmtM06]+[AmtM07]+[AmtM08]+[AmtM09]+[AmtM10]+[AmtM11]+[AmtM12])) AS AmtTotx, 
			Sum(T1.StatusCnt0) AS SumOfStatusCnt0, Sum(T1.StatusCnt1) AS SumOfStatusCnt1, Sum(T1.StatusCnt2) AS SumOfStatusCnt2, Sum(T1.StatusCnt3) AS SumOfStatusCnt3,
			 Sum(T1.PriorStatusCnt0) AS SumOfStatusCnt4, Sum(T1.PriorStatusCnt1) AS SumOfStatusCnt5
			FROM #t1 as T1 
			GROUP BY T1.ReconGroupTitle 

	-- Display empty lines after subtotals 
	INSERT INTO SOM_AA_TransactionSummary ( 
		SessionUserid, SessionDate, 
		ReconGroupTitle, Sort1, Sort2, ReconItemTitle, ReconItemCd, 
		NotVerified, Pending, Complete, AutoComplete, PriorNotVerified, PriorPending, 
		AmtM01x, AmtM02x, AmtM03x, AmtM04x, AmtM05x, AmtM06x, AmtM07x, AmtM08x, AmtM09x, AmtM10x, AmtM11x, AmtM12x, AmtTotx )
		SELECT @vSessionUserid, @vSessionDate, 
			Null AS ReconGroupTitle, T1.ReconGroupTitle AS Sort1, 3 AS Sort2, Null AS ReconItemTitle, Null AS ReconItemCd, 
			Null AS StatusAmt0, Null AS StatusAmt1, Null AS StatusAmt2, Null AS StatusAmt3, Null AS PriorStatusAmt0, Null AS PriorStatusAmt1, 
			Null AS AmtM01x, Null AS AmtM02x, Null AS AmtM03x, Null AS AmtM04x, Null AS AmtM05x, Null AS AmtM06x, 
			Null AS AmtM07x, Null AS AmtM08x, Null AS AmtM09x, Null AS AmtM10x, Null AS AmtM11x, Null AS AmtM12x, Null AS AmtTotx
			FROM #t1 as T1 
			GROUP BY T1.ReconGroupTitle; 

	-- Display grand total 
	INSERT INTO SOM_AA_TransactionSummary ( 
		SessionUserid, SessionDate, 
		ReconGroupTitle, Sort1, Sort2, ReconItemTitle, ReconItemCd, 
		NotVerified, Pending, AutoComplete, Complete, PriorNotVerified, PriorPending, 
		AmtM01x, AmtM02x, AmtM03x, AmtM04x, AmtM05x, AmtM06x, AmtM07x, AmtM08x, AmtM09x, AmtM10x, AmtM11x, AmtM12x, AmtTotx, 
		NotVerifiedCount, PendingCount, AutoCompleteCount, CompleteCount )
		SELECT @vSessionUserid, @vSessionDate, 
			'All Types' AS ReconGroupTitle, 'zzz' AS Sort1, 4 AS Sort2, 'Total' AS ReconItemTitle, 0 AS ReconItemCd, 
			Sum(T1.StatusAmt0) AS SumOfStatusAmt0, Sum(T1.StatusAmt1) AS SumOfStatusAmt1, Sum(T1.StatusAmt2) AS SumOfStatusAmt21, Sum(T1.StatusAmt3) AS SumOfStatusAmt3, 
			Sum(T1.PriorStatusAmt0) AS SumOfPriorStatusAmt0, Sum(T1.PriorStatusAmt1) AS SumOfPriorStatusAmt1, 
			Sum(T1.AmtM01) AS AmtM01x, Sum(T1.AmtM02) AS AmtM02x, Sum(T1.AmtM03) AS AmtM03x, Sum(T1.AmtM04) AS AmtM04x, Sum(T1.AmtM05) AS AmtM05x, Sum(T1.AmtM06) AS AmtM06x, 
			Sum(T1.AmtM07) AS AmtM07x, Sum(T1.AmtM08) AS AmtM08x, Sum(T1.AmtM09) AS AmtM09x, Sum(T1.AmtM10) AS AmtM10x, Sum(T1.AmtM11) AS AmtM11x, Sum(T1.AmtM12) AS AmtM12x, 
			Sum(([AmtM01]+[AmtM02]+[AmtM03]+[AmtM04]+[AmtM05]+[AmtM06]+[AmtM07]+[AmtM08]+[AmtM09]+[AmtM10]+[AmtM11]+[AmtM12])) AS AmtTotx, Sum(T1.StatusCnt0) AS SumOfStatusCnt0, Sum(T1.StatusCnt1) AS SumOfStatusCnt1, Sum(T1.StatusCnt2) AS SumOfStatusCnt2, Sum(T1.StatusCnt3) AS SumOfStatusCnt3
			FROM #t1 as T1;

	---------------------------------------------------
	-- Get Employee data 
	---------------------------------------------------
	if @vWithEmp<>0 
		begin 
		declare @vSQL1 as nvarchar(4000) 
		declare @vSQL2 as nvarchar(4000) 
		declare @vSQLp1 as nvarchar(1000) 

 
		if @vFP=1 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY-1 then [P07_Jan] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P06_Dec] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P05_Nov] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P04_Oct] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P03_Sep] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P02_Aug] else 0 end) as M12, '  
			end 
		if @vFP=2 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M01,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P07_Jan] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P06_Dec] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P05_Nov] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P04_Oct] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P03_Sep] else 0 end) as M12, '
			end 
		if @vFP=3 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M02,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P07_Jan] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P06_Dec] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P05_Nov] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P04_Oct] else 0 end) as M12, '
			end 
		if @vFP=4 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M03,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P07_Jan] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P06_Dec] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P05_Nov] else 0 end) as M12, '
			end 
		if @vFP=5 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M04,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P07_Jan] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P06_Dec] else 0 end) as M12, '
			end 
		if @vFP=6 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M05,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P07_Jan] else 0 end) as M12, '
			end 
		if @vFP=7 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P07_Jan] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M06,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P08_Feb] else 0 end) as M12, '
			end 
		if @vFP=8 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P08_Feb] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P07_Jan] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M07,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P09_Mar] else 0 end) as M12, '
			end 
		if @vFP=9 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P09_Mar] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P08_Feb] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P07_Jan] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M08,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P10_Apr] else 0 end) as M12, '
			end 
		if @vFP=10 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P10_Apr] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P09_Mar] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P08_Feb] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P07_Jan] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M09,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P11_May] else 0 end) as M12, '
			end 
		if @vFP=11 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P11_May] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P10_Apr] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P09_Mar] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P08_Feb] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P07_Jan] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M10,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M11, 
				sum(case when FiscalYear=@vFY-1 then [P12_Jun] else 0 end) as M12, '
			end 
		if @vFP=12 
			begin  
			set @vSQLp1='
				sum(case when FiscalYear=@vFY   then [P12_Jun] else 0 end) as M01, 
				sum(case when FiscalYear=@vFY   then [P11_May] else 0 end) as M02, 
				sum(case when FiscalYear=@vFY   then [P10_Apr] else 0 end) as M03, 
				sum(case when FiscalYear=@vFY   then [P09_Mar] else 0 end) as M04, 
				sum(case when FiscalYear=@vFY   then [P08_Feb] else 0 end) as M05, 
				sum(case when FiscalYear=@vFY   then [P07_Jan] else 0 end) as M06, 
				sum(case when FiscalYear=@vFY   then [P06_Dec] else 0 end) as M07, 
				sum(case when FiscalYear=@vFY   then [P05_Nov] else 0 end) as M08, 
				sum(case when FiscalYear=@vFY   then [P04_Oct] else 0 end) as M09, 
				sum(case when FiscalYear=@vFY   then [P03_Sep] else 0 end) as M10, 
				sum(case when FiscalYear=@vFY   then [P02_Aug] else 0 end) as M11,   
				sum(case when FiscalYear=@vFY   then [P01_Jul] else 0 end) as M12, '
			end 

		set @vSQL1 = ' 
		insert into SOM_AA_EmployeeListRolling  
			(SessionUserid, SessionDate, 
			NotVerified, RecType, Sort1, Sort2, FiscalYear, FiscalPeriod, 
			Employee_Name, Employee_Id, EmpChanged, ReconAssignCd, ReconStatusCd, 
			PositionTitleCategory, PositionTitleCd, PositionTitleCdTitle, 
			DeptCd, ProjectManagerCd, ProjectManagerTitleCd, ProjectUseShort, ProjectCd, FundCd, FunctionCd, FlexCd, DeptTitle, ProjectTitle, FundTitle, FunctionTitle, 
			M01, M02, M03, M04, M05, M06, M07, M08, M09, M10, M11, M12, tot) 
			SELECT @vSessionUserid, @vSessionDate, 
				0 as NotVerified, @vRecType as RecType, 
				@vSort1 as Sort1, 1 as Sort2, @vFY, @vFP, Employee_Name, Employee_Id, 
				null as EmpChanged, ReconAssignCd, 
				case when FiscalYear=@vFY then ReconStatusCd else 2000 end as ReconStatusCd, 
				PositionTitleCategory, PositionTitleCd, PositionTitleCdTitle, 
				DeptCd, ProjectManagerCd, left(ProjectManagerTitleCd,50), ProjectUseShort, ProjectCd, FundCd, FunctionCd, FlexCd, DeptTitle, ProjectTitle, FundTitle, FunctionTitle, ' 
			+ @vSQLp1 
			+ ' 0 as tot 
				from vw_SOM_BFA_ReconEmployeeGLV_Details as X 
				where ((X.FiscalYear=@vFY and X.FiscalPeriod=@vEmpFP) or (X.FiscalYear=@vFY-1 and X.FiscalPeriod=12)) 
				and X.DeptSite like @vDeptSite and X.DeptTreeCd like ''%'' + @vDeptCdOverride + ''%'' 
				and '+dbo.fn_SOM_BFA_GetWhereFromSavedFilter (@vUserId, @vDeptCdGLV, @vDeptCdOverride, @vFilterName, 0) +' 
				and @vWhere 
				group by X.RecType, 
				Employee_Name, Employee_Id, 
				IIf([ReconAssignCd]=10 or [ReconAssignCd]=30, ''CHG'', null), 
				ReconAssignCd, 
				case when FiscalYear=@vFY then ReconStatusCd else 2000 end, 
				PositionTitleCategory, PositionTitleCd, PositionTitleCdTitle, 
				DeptCd, ProjectManagerCd, left(ProjectManagerTitleCd,50), ProjectUseShort, ProjectCd, FundCd, 
				FunctionCd, FlexCd, DeptTitle, ProjectTitle, FundTitle, FunctionTitle 
				'
		
		set @vSQL1 = replace(@vSQL1, '@vSessionUserId', '''' + @vSessionUserId + '''') 
		set @vSQL1 = replace(@vSQL1, '@vSessionDate', '''' + cast(@vSessionDate as varchar(28)) + '''') 
		set @vSQL1 = replace(@vSQL1, '@vDeptCdOverride', '''' + @vDeptCdOverride + '''') 
		set @vSQL1 = replace(@vSQL1, '@vDeptSite', '''' + @vDeptSite + '''') 
		set @vSQL1 = replace(@vSQL1, '@vFY', cast(@vFY as varchar(4))) 
		set @vSQL1 = replace(@vSQL1, '@vFP', cast(@vFP as varchar(2))) 
		set @vSQL1 = replace(@vSQL1, '@vEmpFP', cast(@vEmpFP as varchar(2))) 

		-- FTE 
		set @vSQL2 = replace(@vSQL1, '@vRecType', '''FTE''') 
		set @vSQL2 = replace(@vSQL2, '@vWhere', ' X.RecType=''XY'' ') 
		set @vSQL2 = replace(@vSQL2, '@vSort1', '1') 
		exec sp_executesql @vSQL2; 

		-- RegPay / xxxPay 
		set @vSQL2 = replace(@vSQL1, ' [P', ' [S') 
		set @vSQL2 = replace(@vSQL2, '@vRecType', 'case when X.RecType=''XY'' then ''RegPay'' else ''AddPay'' end ') 
		set @vSQL2 = replace(@vSQL2, '@vWhere', ' X.RecType not in (''VLA'') ') 
		set @vSQL2 = replace(@vSQL2, '@vSort1', 'case when X.RecType=''XY'' then 2 else 3 end ') 
		exec sp_executesql @vSQL2; 

		-- Remove all zero only records 
		delete from SOM_AA_EmployeeListRolling 
			where SessionUserid=@vSessionUserid 
				and (abs(M01)+abs(M02)+abs(M03)+abs(M04)+abs(M05)+abs(M06)+abs(M07)+abs(M08)+abs(M09)+abs(M10)+abs(M11)+abs(M12))=0

		-- Remove if missing names (remove later) 
		delete from SOM_AA_EmployeeListRolling 
			where employee_name is null 

		-- Mark changed records 
		update SOM_AA_EmployeeListRolling 
			set EmpChanged='Chg' 
			where SessionUserid=@vSessionUserid 
			and RecType in ('FTE', 'RegPay') and M01<>M02 

		-- Totals 
		insert into SOM_AA_EmployeeListRolling 
			(SessionUserid, SessionDate, NotVerified, RecType, Sort1, Sort2, FiscalYear, FiscalPeriod, 
			Employee_Name, Employee_Id, EmpChanged, ReconAssignCd, ReconStatusCd, 
			PositionTitleCategory, PositionTitleCd, PositionTitleCdTitle, 
			DeptCd, ProjectManagerCd, ProjectManagerTitleCd, ProjectUseShort, ProjectCd, FundCd, FunctionCd, FlexCd, DeptTitle, ProjectTitle, FundTitle, FunctionTitle, 
			M01, M02, M03, M04, M05, M06, M07, M08, M09, M10, M11, M12) 
			SELECT @vSessionUserid, @vSessionDate, 
				0 as NotVerified, RecType, 
				case when RecType='FTE' then 1 when RecType='RegPay' then 2 when RecType='OthPay' then 3 else 4 end, 
				2 as Sort2, FiscalYear, FiscalPeriod, Employee_Name, 'Total' as Employee_Id, 
				null as EmpChanged, null as ReconAssignCd, null as ReconStatusCd, 
				PositionTitleCategory, null as PositionTitleCd, null as PositionTitleCdTitle, 
				null as DeptCd, null as ProjectManagerCd, null as ProjectManagerTitleCd, null as ProjectUseShort, 
				null as ProjectCd, null as FundCd, null as FunctionCd, null as FlexCd, null as DeptTitle, null as ProjectTitle, null as FundTitle, null as FunctionTitle, 
				sum(M01), sum(M02), sum(M03), sum(M04), sum(M05), sum(M06), 
				sum(M07), sum(M08), sum(M09), sum(M10), sum(M11), sum(M12) 
				from SOM_AA_EmployeeListRolling as X 
				where SessionUserid=@vSessionUserid 
				and Sort2=1 
				group by RecType, FiscalYear, FiscalPeriod, Employee_name, Employee_Id, PositionTitleCategory, 
					case when RecType='FTE' then 1 when RecType='RegPay' then 2 when RecType='AddPay' then 3 else 4 end 


		-- zSpace 
		insert into SOM_AA_EmployeeListRolling 
			(SessionUserid, SessionDate, NotVerified, RecType, Sort1, Sort2, FiscalYear, FiscalPeriod, 
			Employee_Name, Employee_Id, EmpChanged, ReconAssignCd, ReconStatusCd, 
			PositionTitleCategory, PositionTitleCd, PositionTitleCdTitle, 
			DeptCd, ProjectManagerCd, ProjectManagerTitleCd, ProjectUseShort, ProjectCd, FundCd, FunctionCd, FlexCd, DeptTitle, ProjectTitle, FundTitle, FunctionTitle, 
			M01, M02, M03, M04, M05, M06, M07, M08, M09, M10, M11, M12) 
			SELECT @vSessionUserid, @vSessionDate, 
				0 as NotVerified, null as RecType, 
				5 as Sort1, 1 as Sort2, FiscalYear, FiscalPeriod, Employee_Name, 'zSpace' as Employee_Id, 
				null as EmpChanged, null as ReconAssignCd, null as ReconStatusCd, 
				PositionTitleCategory, null as PositionTitleCd, null as PositionTitleCdTitle, 
				null as DeptCd, null as ProjectManagerCd, null as ProjectManagerTitleCd, null as ProjectUseShort, 
				null as ProjectCd, null as FundCd, null as FunctionCd, null as FlexCd, null as DeptTitle, null as ProjectTitle, null as FundTitle, null as FunctionTitle, 
				null as S01_Jul, null as S02_Aug, null as S03_Sep, 
				null as S04_Oct, null as S05_Nov, null as S06_Dec, 
				null as S07_Jan, null as S08_Feb, null as S09_Mar, 
				null as S10_Apr, null as S11_May, null as S12_Jun 
				from SOM_AA_EmployeeListRolling as X 
				where SessionUserid=@vSessionUserid 
				and Sort2=1 

				group by FiscalYear, FiscalPeriod, PositionTitleCategory, Employee_name, Employee_Id 
				

		-- Add payroll line to dashboard if missing 
		if not exists(select * from SOM_AA_Dashboard where SessionUserid=@vSessionUserid and ReconGroupTitle='Payroll') 
			insert into SOM_AA_Dashboard 
				(SessionUserid, SessionDate, FiscalYear, ReconGroupTitle)
				values(@vSessionUserid, @vSessionDate, @vFY, 'Payroll') 

		-- Get not verified payroll amounts and counts 
		declare @vAmt as money, @vCnt as money
		select @vCnt=count(*), @vAmt=sum(M01) 
			from SOM_AA_EmployeeListRolling 
			where SessionUserid=@vSessionUserid 
				and ReconStatusCd in (0) and RecType='RegPay'

		-- Add amounts not verified to payroll dashboard line 
		if ( @vCnt>0 and @vAmt is not null )
			begin
		update SOM_AA_Dashboard 
			set StatusAmt0=StatusAmt0+@vAmt, StatusCnt0=StatusCnt0+@vCnt 
			where SessionUserid=@vSessionUserid and ReconGroupTitle='Payroll' 
			end
		
		-- Get pending payroll amounts and counts 
		declare @vAmt1 as money, @vCnt1 as money
		select @vCnt1=count(*), @vAmt1=sum(M01) 
			from SOM_AA_EmployeeListRolling 
			where SessionUserid=@vSessionUserid 
				and ReconStatusCd in (1000) and RecType='RegPay'

		-- Add amounts pending to payroll dashboard line 
		if ( @vCnt1 >0 and @vAmt1 IS NOT NULL )
			begin 
			update SOM_AA_Dashboard 
				set StatusAmt1=StatusAmt1+@vAmt1, StatusCnt1=StatusCnt1+@vCnt1 
				where SessionUserid=@vSessionUserid and ReconGroupTitle='Payroll' 
			end 

		-- Get completed payroll amounts and counts 
		declare @vAmt3 as money, @vCnt3 as money
		select @vCnt3=count(*), @vAmt3=sum(M01) 
			from SOM_AA_EmployeeListRolling 
			where SessionUserid=@vSessionUserid 
				and ReconStatusCd in (3000) and RecType='RegPay'

		-- Add amounts completed to payroll dashboard line 
		if ( @vCnt3 >0 and @vAmt3 IS NOT NULL )
			begin 
			update SOM_AA_Dashboard 
				set StatusAmt3=StatusAmt3+@vAmt3, StatusCnt3=StatusCnt3+@vCnt3 
				where SessionUserid=@vSessionUserid and ReconGroupTitle='Payroll' 
			end 

		end 

		
		 

end
