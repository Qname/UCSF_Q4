<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getmonth_name($mon)
    {
        $grade=0;
        switch ($mon) {
            case 1:
            $grade = Jan;
            break;
            case 2:
            $grade = Feb;
            break;
            case 3:
            $grade = Mar;
            break;
            case 4:
            $grade = Apr;
            break;
            case 5:
            $grade = May;
            break;
            case 6:
            $grade = Jun;
            break;
            case 7:
            $grade = Jul;
            break;
            case 8:
            $grade = Aug;
            break;
            case 9:
            $grade = Sep;
            break;
            case 10:
            $grade = Oct;
            break;
            case 11:
            $grade = Nov;
            break;
            case 12:
            $grade = Dec;
            break;
        }
        return $grade;
    }

    public function getmonth_reportdate($reportdate)
    {
        $mon  = substr($reportdate,0,3);
        $grade=0;
        switch ($mon) {
            case Jan:
            $grade = 1;
            break;
            case Feb:
            $grade = 2;
            break;
            case Mar:
            $grade = 3;
            break;
            case Apr:
            $grade = 4;
            break;
            case May:
            $grade = 5;
            break;
            case Jun:
            $grade = 6;
            break;
            case Jul:
            $grade = 7;
            break;
            case Aug:
            $grade = 8;
            break;
            case Sep:
            $grade = 9;
            break;
            case Oct:
            $grade = 10;
            break;
            case Nov:
            $grade = 11;
            break;
            case Dec:
            $grade = 12;
            break;
        }
        return $grade;
    }

    public function getyear_reportdate($reportdate)
    {
        return substr($reportdate,4);
    }
    
    public function getdefaultdeptid($uidnumber)
    {
        $query = $this->db->query("SELECT string FROM SOM_BFA_UserPreferences WHERE UserId= ?  AND Preference='Default Deptid'",array($uidnumber));
        log_message('info',"getdefaultdeptid SQL= " . $this->db->last_query()); 
        
        if($query->num_rows() > 0){
            $ret = $query->row();
            return $ret->string;
        } else {         
            return "";
        }  

    }
    /**
     * Get Compliance Dashboard – For user Displays a management overview of GL Verification completion status by Control Point or by Level 2 Dept ID
     * input parameter: userid
     * */
    public function get_Dashboard($userid)
    {
       $query = $this->db->query("SELECT * FROM vw_SOM_AA_Dashboard WHERE SessionUserid= ?  ORDER BY case when ReconGroupTitle = 'Total' then 1 else 0 end, ReconGroupTitle ASC",array($userid));
       log_message('info',"get_Dashboard SQL= " . $this->db->last_query()); 
       $data = $query->result();
       return $data;
   }

   public function get_listdepartment($deptid,$fy,$fp)
   {
    $query = null;
    if ($deptid!=''){
        $queryStr = "SELECT DISTINCT vw_COA_SOM_Departments.DeptCd, vw_COA_SOM_Departments.DeptTreeTitleAbbrev, vw_COA_SOM_Departments.DeptSite, 
        IIf([vw_COA_SOM_Departments].[DeptPostingLevel]='Y','Posting','') AS PostingLevel, 
        vw_COA_SOM_Departments.DeptLevel, vw_COA_SOM_Departments.DeptLevel1Cd,
        vw_COA_SOM_Departments.ReconDeptCd,
        IIf([vw_COA_SOM_Departments].[DeptCd]=[vw_COA_SOM_Departments].[ReconDeptCd],IIf([CheckedState]=0,'','Dept Approved'),
        IIf([SOM_BFA_ReconApproveTrend].[CheckedState] IS NULL Or [SOM_BFA_ReconApproveTrend].[CheckedState]=0,'','Monthly Trend Verified')) AS Checked, 
        SOM_BFA_ReconApproveTrend.CheckedUserId, SOM_BFA_ReconApproveTrend.CheckedDate, vw_COA_SOM_Departments.DeptTreeCd,lkp_userprofile.user_name as UserName FROM vw_COA_SOM_Departments 
        LEFT JOIN SOM_BFA_ReconApproveTrend ON vw_COA_SOM_Departments.DeptCd = SOM_BFA_ReconApproveTrend.DeptCd
        LEFT JOIN lkp_userprofile on  SOM_BFA_ReconApproveTrend.CheckedUserId = lkp_userprofile.user_id
        WHERE vw_COA_SOM_Departments.DeptTreeCd like ?
        AND (SOM_BFA_ReconApproveTrend.FiscalYear Is Null Or SOM_BFA_ReconApproveTrend.FiscalYear = ? )
        AND  (SOM_BFA_ReconApproveTrend.FiscalPeriod Is Null Or SOM_BFA_ReconApproveTrend.FiscalPeriod = ? )
        ORDER BY vw_COA_SOM_Departments.DeptTreeCd";
         $query = $this->db->query( $queryStr, array( "%".$deptid."%",$fy,$fp));
    }else{
        $queryStr = "SELECT DISTINCT vw_COA_SOM_Departments.DeptCd, vw_COA_SOM_Departments.DeptTreeTitleAbbrev, vw_COA_SOM_Departments.DeptSite, 
        IIf([vw_COA_SOM_Departments].[DeptPostingLevel]='Y','Posting','') AS PostingLevel, 
        vw_COA_SOM_Departments.DeptLevel, vw_COA_SOM_Departments.DeptLevel1Cd,
        vw_COA_SOM_Departments.ReconDeptCd,
        IIf([vw_COA_SOM_Departments].[DeptCd]=[vw_COA_SOM_Departments].[ReconDeptCd],IIf([CheckedState]=0,'','Dept Approved'),
        IIf([SOM_BFA_ReconApproveTrend].[CheckedState] IS NULL Or [SOM_BFA_ReconApproveTrend].[CheckedState]=0,'','Monthly Trend Verified')) AS Checked, 
        SOM_BFA_ReconApproveTrend.CheckedUserId, SOM_BFA_ReconApproveTrend.CheckedDate, vw_COA_SOM_Departments.DeptTreeCd, lkp_userprofile.user_name as UserName 
        FROM vw_COA_SOM_Departments LEFT JOIN SOM_BFA_ReconApproveTrend ON vw_COA_SOM_Departments.DeptCd = SOM_BFA_ReconApproveTrend.DeptCd
        LEFT JOIN lkp_userprofile on  SOM_BFA_ReconApproveTrend.CheckedUserId = lkp_userprofile.user_id
        WHERE (SOM_BFA_ReconApproveTrend.FiscalYear Is Null Or SOM_BFA_ReconApproveTrend.FiscalYear = ?)
        AND  (SOM_BFA_ReconApproveTrend.FiscalPeriod Is Null Or SOM_BFA_ReconApproveTrend.FiscalPeriod = ?)
        ORDER BY vw_COA_SOM_Departments.DeptTreeCd";
        $query = $this->db->query( $queryStr, array($fy,$fp));
    }
    
    $data = $query->result();
    log_message('info',"get_listdepartment SQL= " . $this->db->last_query()); 
    return $data;
}

    /**
     * Get DeptCd Information
     * Get status of monthly trend verficiation to display it in DepCd dropdown on Filter Menu on tab GLVerfication
     * */
    public function GetDeptCdInformation($detpCd,$fy,$fp){
        $queryStr = "SELECT DISTINCT vw_COA_SOM_Departments.DeptCd, vw_COA_SOM_Departments.DeptTreeTitleAbbrev, vw_COA_SOM_Departments.DeptSite, IIf([vw_COA_SOM_Departments].[DeptPostingLevel]='Y','Posting','') AS PostingLevel, 
        vw_COA_SOM_Departments.DeptLevel, vw_COA_SOM_Departments.DeptLevel1Cd,vw_COA_SOM_Departments.ReconDeptCd, IIf([vw_COA_SOM_Departments].[DeptCd]=[vw_COA_SOM_Departments].[ReconDeptCd],IIf([CheckedState]=0,'','Dept Approved'),
        IIf([SOM_BFA_ReconApproveTrend].[CheckedState] IS NULL Or [SOM_BFA_ReconApproveTrend].[CheckedState]=0,'','Monthly Trend Verified')) AS Checked, 
        SOM_BFA_ReconApproveTrend.CheckedUserId, SOM_BFA_ReconApproveTrend.CheckedDate, vw_COA_SOM_Departments.DeptTreeCd
        FROM vw_COA_SOM_Departments LEFT JOIN SOM_BFA_ReconApproveTrend ON vw_COA_SOM_Departments.DeptCd
        = SOM_BFA_ReconApproveTrend.DeptCd WHERE vw_COA_SOM_Departments.DeptCd = ? AND (SOM_BFA_ReconApproveTrend.FiscalYear Is Null Or SOM_BFA_ReconApproveTrend.FiscalYear = ? )
        AND  (SOM_BFA_ReconApproveTrend.FiscalPeriod Is Null Or SOM_BFA_ReconApproveTrend.FiscalPeriod = ?)
        ORDER BY vw_COA_SOM_Departments.DeptTreeCd";
        $query = $this->db->query( $queryStr, array($detpCd,$fy,$fp));
        $data = $query->result();
        log_message('info',"GetDeptCdInformation SQL= " . $this->db->last_query()); 
        return $data;
    }
    public function CheckIfDeptCdExisted($deptCd){
        $query = $this->db->query("SELECT DISTINCT vw_COA_SOM_Departments.DeptCd from vw_COA_SOM_Departments where DeptCd = ? and ((DeptCd NOT LIKE '2%') AND (DeptCd NOT LIKE 'H%') AND (DeptCd <= 566000) AND (DeptCd != '000000'))", array($deptCd));
        log_message('info',"CheckIfDeptCdExisted SQL= " . $this->db->last_query()); 
        $data = $query->result();
        if($data){
            return true;
        }
        return false;
    }

    /**
     * Get config default fy, fp from SOM_BFA_Variables 
     * $variableriable: DefaultFPMax or DefaultFY     
     * */
    public function GetFPFYdefault($variable)
    {
        $query = $this->db->query("SELECT Integer FROM SOM_BFA_Variables WHERE variable= ?",array($variable));
        log_message('info',"GetFPFYdefault SQL= " . $this->db->last_query()); 
        $ret = $query->row();
        return $ret->Integer;
    }

    /**
     * Get list Departments 
     * $deptcd
     * */
    public function GetDepartmentsById($deptcd)
    {
        $query = $this->db->query("SELECT DeptTitle FROM vw_COA_SOM_Departments WHERE DeptCd= ?",array($deptcd));
        log_message('info',"GetDepartmentsById SQL= " . $this->db->last_query()); 
        $ret = $query->row();
        return $ret->DeptTitle;
    }


    /**
     * Get department allowed of user by id of user
     *
     * @param   string  $email    of user
     * @return  array
     */
    public function get_deptAllowed($email)
    {
        try {
            $query = $this->db->query("SELECT allowDeptId FROM lkp_userprofile  WHERE user_id= ?",array($email));
            log_message('info',"get_deptAllowed SQL= " . $this->db->last_query()); 
            $ret = $query->row();
            return $ret->allowDeptId;
        }
        catch(Exception $e){
            log_message('error',"get_deptAllowed " . $e->getMessage());
            return "";
        }
        
    }
        /**
     * Get current server date    
     * */
        public function getServerDate(){
            $query = $this->db->query('select GETDATE() as CurrentDate')->result();       
            log_message('info',"getServerDate SQL= " . $this->db->last_query()); 
            return $query[0]->CurrentDate;
        }
    //set up trend check 
        function SetupTrendCheck($fy,$fp,$deptId){
         $this->db->query("exec sp_GLV_Approve ? , ? , ? , ? , ? , ?", array( 'Trend', $fy,$fp,$deptId, -1,$this->session->userdata['userid'] ));
         log_message('info',"SetupTrendCheck SQL= " . $this->db->last_query()); 
     }  
     
    //log information
     function LogActivityInfo($logSubject,$description,$additionalInfo,$userId){
        try{
            $serverDate = $this->getServerDate();
            $this->db->query("insert into ActivityLog(LogSubject, Description, CreatedDate, AdditionalInfo, UserId) values ( ? , ? , ?  , ? , ? )",array($logSubject,$description, $serverDate,$additionalInfo, $userId));
            log_message('info',"LogActivityInfo SQL= " . $this->db->last_query()); 
        }
        catch(Exception $e){
            log_message('error','error in saving log: '. $e->getMessage());
        }
        
    }

 
    
}