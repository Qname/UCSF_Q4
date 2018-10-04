Alter view vw_SOM_AA_DASHBOARD AS

select SessionUserid, CASE GROUPING(ReconGroupTitle) WHEN 1 THEN 'edecec' ELSE 'ffffff' END AS ColorTile, 
		CASE GROUPING(ReconGroupTitle) WHEN 1 THEN 'Total' ELSE ReconGroupTitle END AS ReconGroupTitle,
		
		CASE
				WHEN SUM(ISNULL(StatusAmt0, 0)+ISNULL(StatusAmt1, 0)+ISNULL(StatusAmt3, 0)) is null or SUM(ISNULL(StatusAmt0, 0)+ISNULL(StatusAmt1, 0)+ISNULL(StatusAmt3, 0)) = '' THEN 0
				ELSE SUM(ISNULL(StatusAmt0, 0)+ISNULL(StatusAmt1, 0)+ISNULL(StatusAmt3, 0))
		END as TotalSelectedAmount,	
		CASE
				WHEN SUM(StatusCnt0+StatusCnt1+StatusCnt3) is null or SUM(StatusCnt0+StatusCnt1+StatusCnt3) = '' THEN 0
				ELSE SUM(StatusCnt0+StatusCnt1+StatusCnt3)
		END as TotalSelectedCount,

		SUM(AmtM01)as TotalActivityAmount,

		CASE
				WHEN SUM(StatusCnt0 +StatusCnt1 +StatusCnt2 +StatusCnt3 ) is null or SUM(StatusCnt0 +StatusCnt1 +StatusCnt2 +StatusCnt3 ) = '' THEN 0
				ELSE SUM(StatusCnt0 +StatusCnt1 +StatusCnt2 +StatusCnt3 )
		END as TotalActivityCount,	
		CASE
				WHEN SUM(StatusAmt0) is null or SUM(StatusAmt0) = '' THEN 0
				ELSE SUM(StatusAmt0)
		END as TotalNotVerifiedAmount,	
		CASE
				WHEN SUM(StatusCnt0) is null or SUM(StatusCnt0) = '' THEN 0
				ELSE SUM(StatusCnt0)
		END as TotalNotVerifiedCount,
		CASE
				WHEN SUM(StatusAmt1) is null or SUM(StatusAmt1) = '' THEN 0
				ELSE SUM(StatusAmt1)
		END as TotalPendingAmount,	
		CASE
				WHEN SUM(StatusCnt1) is null or SUM(StatusCnt1) = '' THEN 0
				ELSE SUM(StatusCnt1)
		END as TotalPendingCount,
		CASE
				WHEN SUM(StatusAmt3) is null or SUM(StatusAmt3) = '' THEN 0
				ELSE SUM(StatusAmt3)
		END as TotalCompletedAmount,	
		CASE
				WHEN SUM(StatusCnt3) is null or SUM(StatusCnt3) = '' THEN 0
				ELSE SUM(StatusCnt3)
		END as TotalCompletedCount		 
 from SOM_AA_DASHBOARD
 group by ROLLUP(ReconGroupTitle),SessionUserid
