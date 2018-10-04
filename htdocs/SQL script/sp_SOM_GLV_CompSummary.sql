USE [GLVData]
GO
/****** Object:  StoredProcedure [dbo].[sp_SOM_GLV_CompSummary]    Script Date: 12/20/2017 10:51:56 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


ALTER PROCEDURE [dbo].[sp_SOM_GLV_CompSummary]
@vFY smallint,
@vFP smallint,
@vDeptCdTop varchar(6),
@vBusinessUnitCd varchar(5),
@vDeptSite varchar(4)='%',
@vWithSource smallint=0,
@vWithReconGroup smallint=0,
@vWithReconItem smallint=0
AS
BEGIN
-- exec sp_SOM_GLV_CompSummary 2017, 4, '999999', 'SFCMP', '%', 0, 0, 0
SET NOCOUNT ON;

declare @vDeptLevel as varchar(6)
select @vDeptLevel = DeptLevel from vw_COA_SOM_Departments where DeptCd=@vDeptCdTop 
if @vDeptCdTop = '999999'
set @vDeptLevel=0 

create table #t (
Src varchar(3),
DeptCd varchar(6),
DeptTitle varchar(80),
ReconGroupTitle varchar(60),
ReconItemTitle varchar(60),
NotVerified Integer,
Pending Integer,
Complete Integer,
Selected Integer,
AutoComplete Integer,
Records Integer
)

-- Transactions portion
insert into #t (Src, DeptCd, DeptTitle, ReconGroupTitle, ReconItemTitle, NotVerified, Pending, Complete, Selected, AutoComplete, Records)
select
case when @vWithSource=0 then 'NA' else 'Trn' end as Src,
case
when @vDeptLevel=0 then DeptLevel1Cd
when @vDeptLevel=1 then DeptLevel2Cd
when @vDeptLevel=2 then DeptLevel3Cd
when @vDeptLevel=3 then DeptLevel4Cd
when @vDeptLevel=4 then DeptLevel5Cd
when @vDeptLevel=5 then DeptLevel6Cd
when @vDeptLevel=6 then DeptCd
end as DeptCd,
case
when @vDeptLevel=0 then DeptLevel1Title
when @vDeptLevel=1 then DeptLevel2Title
when @vDeptLevel=2 then DeptLevel3Title
when @vDeptLevel=3 then DeptLevel4Title
when @vDeptLevel=4 then DeptLevel5Title
when @vDeptLevel=5 then DeptLevel6Title
when @vDeptLevel=6 then DeptTitle
end as DeptTitle,
case when @vWithReconGroup=0 then 'NA' else ReconGroupTitle end as ReconGroupTitle,
case when @vWithReconItem=0 then 'NA' else ReconItemTitle end as ReconItemTitle,
sum(case when ReconStatusCd=0 then Records else 0 end) as NotVerifed,
sum(case when ReconStatusCd=1000 then Records else 0 end) as Pending,
sum(case when ReconStatusCd=3000 then Records else 0 end) as Complete,
sum(case when ReconStatusCd in (0, 1000, 3000) then Records else 0 end) as Selected,
sum(case when ReconStatusCd=2000 then Records else 0 end) as AutoComplete,
Sum(Records) as Records
from vw_GLV_Dashboard
where fiscalyear=@vFY and accountingperiod=@vFP and ReconItemCd>0
and BusinessUnitCd like @vBusinessUnitCd 
and DeptTreeCd like '%' + case when @vDeptCdTop='999999' then '' else @vDeptCdTop end + '%'
and DeptSite like '%' + @vDeptSite + '%'
group by
case
when @vDeptLevel=0 then DeptLevel1Cd
when @vDeptLevel=1 then DeptLevel2Cd
when @vDeptLevel=2 then DeptLevel3Cd
when @vDeptLevel=3 then DeptLevel4Cd
when @vDeptLevel=4 then DeptLevel5Cd
when @vDeptLevel=5 then DeptLevel6Cd
when @vDeptLevel=6 then DeptCd
end,
case
when @vDeptLevel=0 then DeptLevel1Title
when @vDeptLevel=1 then DeptLevel2Title
when @vDeptLevel=2 then DeptLevel3Title
when @vDeptLevel=3 then DeptLevel4Title
when @vDeptLevel=4 then DeptLevel5Title
when @vDeptLevel=5 then DeptLevel6Title
when @vDeptLevel=6 then DeptTitle
end,
case when @vWithReconGroup=0 then 'NA' else ReconGroupTitle end,
case when @vWithReconItem=0 then 'NA' else ReconItemTitle end
order by DeptTitle, ReconGroupTitle, ReconItemTitle

-- Employee portion
insert into #t (Src, DeptCd, DeptTitle, ReconGroupTitle, ReconItemTitle, NotVerified, Pending, Complete, Selected, AutoComplete, Records)
select
case when @vWithSource=0 then 'NA' else 'Emp' end as Src,
case
when @vDeptLevel=0 then A.DeptLevel1Cd
when @vDeptLevel=1 then A.DeptLevel2Cd
when @vDeptLevel=2 then A.DeptLevel3Cd
when @vDeptLevel=3 then A.DeptLevel4Cd
when @vDeptLevel=4 then A.DeptLevel5Cd
when @vDeptLevel=5 then A.DeptLevel6Cd
when @vDeptLevel=6 then A.DeptCd
end as DeptCd,
case
when @vDeptLevel=0 then DeptLevel1Title
when @vDeptLevel=1 then DeptLevel2Title
when @vDeptLevel=2 then DeptLevel3Title
when @vDeptLevel=3 then DeptLevel4Title
when @vDeptLevel=4 then DeptLevel5Title
when @vDeptLevel=5 then DeptLevel6Title
when @vDeptLevel=6 then A.DeptTitle
end as DeptTitle,
case when @vWithReconGroup=0 then 'NA' else '' end as ReconGroupTitle,
case when @vWithReconItem=0 then 'NA' else '' end as ReconItemTitle,
sum(case when ReconStatusCd=0 then 1 else 0 end) as NotVerified,
sum(case when ReconStatusCd=1000 then 1 else 0 end) as Pending,
sum(case when ReconStatusCd=3000 then 1 else 0 end) as Complete,
sum(case when ReconStatusCd in (0, 1000, 3000) then 1 else 0 end) as Selected,
sum(case when ReconStatusCd=2000 then 1 else 0 end) as AutoComplete,
count(*) as Records
from SOM_BFA_ReconEmployeeGLV A
inner join vw_COA_SOM_Departments B on A.DeptCd=B.DeptCd
where FiscalYear=@vFY and FiscalPeriod=@vFP and ReconStatusCd not in (2000)
and B.DeptTreeCd like '%' + case when @vDeptCdTop='999999' then '' else @vDeptCdTop end + '%'
group by
case
when @vDeptLevel=0 then A.DeptLevel1Cd
when @vDeptLevel=1 then A.DeptLevel2Cd
when @vDeptLevel=2 then A.DeptLevel3Cd
when @vDeptLevel=3 then A.DeptLevel4Cd
when @vDeptLevel=4 then A.DeptLevel5Cd
when @vDeptLevel=5 then A.DeptLevel6Cd
when @vDeptLevel=6 then A.DeptCd
end,
case
when @vDeptLevel=0 then DeptLevel1Title
when @vDeptLevel=1 then DeptLevel2Title
when @vDeptLevel=2 then DeptLevel3Title
when @vDeptLevel=3 then DeptLevel4Title
when @vDeptLevel=4 then DeptLevel5Title
when @vDeptLevel=5 then DeptLevel6Title
when @vDeptLevel=6 then A.DeptTitle
end

-- Results
select Src, DeptCd, DeptTitle, ReconGroupTitle, ReconItemTitle,
sum(NotVerified) as NotVerified,
sum(Pending) as Pending,
sum(Complete) as Complete,
sum(Selected) as Selected,
sum(AutoComplete) as AutoComplete,
sum(Records) as Records
from #t
group by Src, DeptCd, DeptTitle, ReconGroupTitle, ReconItemTitle

END


