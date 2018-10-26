<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Glverification_payroll_model extends CI_Model {

    var $table = 'SOM_BFA_ReconEmployeeGLV';

    var $table_expense_detail = "SOM_AA_EmployeeListRolling";
    var $column_expense_detail = array('uniqueid','PositionTitleCategory','Employee_Name','Employee_Id','RecType',
        'DeptCd','FundCd','ProjectCd','FunctionCd','FlexCd','PositionTitleCd','EmpChanged','M01','M02','M03');
    //e_Name
    var $order_expense_detail = "PositionTitleCategory, Employee_Name, Sort1, Sort2, PositionTitleCd, DeptCd, FundCd, ProjectCd, FunctionCd, FlexCd";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get datatable query
     * */
    private function _get_datatables_query($deptCd, $fiscalYear, $fiscalMonth, $businessUnitCd,$site,$filterName)
    {
        try {
            $where = $this->db->query(' select dbo.fn_SOM_BFA_GetWhereFromSavedFilter (?, ?, ?, ?, 0) as WhereStr ',array($this->session->userdata['userid'],$deptCd,$deptCd,$filterName))->result()[0]->WhereStr;
            $queryStr = "select vw_SOM_BFA_ReconEmployeeGLV_Details.uniqueid,PositionTitleCategory,Employee_Id,Employee_name,ReconComment,RECON_Link,
            DeptCd, FundCd,ProjectCd,FunctionCd,FlexCd,DeptSite,PlanTitleCdTitle,ReconUser,ReconDate,
            S01_Jul,S02_Aug,S03_Sep,S04_Oct,S05_Nov,S06_Dec,S07_Jan,S08_Feb,S09_Mar,S10_Apr,S11_May,S12_Jun,ReconStatusCd,lkp_userprofile.user_name,
            Comment_GLVType.id as CommentGLVTypeId
            from vw_SOM_BFA_ReconEmployeeGLV_Details 
            LEFT JOIN lkp_userprofile ON vw_SOM_BFA_ReconEmployeeGLV_Details.ReconUser = lkp_userprofile.user_id
            LEFT JOIN Comment_GLVType ON vw_SOM_BFA_ReconEmployeeGLV_Details.uniqueid = Comment_GLVType.UniqueId AND Comment_GLVType.CommentType = ? 
            where 
            (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
               OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND  
               ReconStatusCd in (0,1000,3000) AND FiscalYear = ?  AND FiscalPeriod = ?  AND  BusinessUnitCd =  ?  ";
            if($site != "%"){
                $queryStr= $queryStr." AND DeptSite = ?";
            } else{
                $queryStr= $queryStr." AND DeptSite like ?";
            }
            if($where){
                $queryStr= $queryStr. " AND ".$where;
            }
            $queryStr= $queryStr. " Order By uniqueid ";

            $query = $this->db->query($queryStr,array(PAYROLL_TYPE,$deptCd,$deptCd,$deptCd,$deptCd,$deptCd,$deptCd, $fiscalYear, $fiscalMonth, $businessUnitCd,$site));
            return $query;
           }
           catch(Exception $e){
            log_message('error',"_get_datatables_query: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get verify payroll data
     * */
    public function get_verify_payroll($deptCd, $fiscalYear, $fiscalMonth, $businessUnitCd,$site,$myFilter)
    {
        $siteStr = ($site == "(any)")?"%":$site;
        try {
            $query = $this->_get_datatables_query($deptCd, $fiscalYear, $fiscalMonth, $businessUnitCd,$siteStr,$myFilter);
            log_message('info',"get_verify_payroll SQL= " . $this->db->last_query());            

            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_verify_payroll: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Count all verify payroll
     * */
    public function count_all_verify_payroll($deptCd, $fiscalYear, $businessUnitCd)
    {
        try {
         $queryStr = "SELECT COUNT(*) AS 'numrows'  from SOM_BFA_ReconEmployeeGLV
         where 
         (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
         OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND 
         ReconStatusCd in (0,1000,3000) AND FiscalYear = ?  AND FiscalPeriod = ?  AND  BusinessUnitCd =  ?";
         $query = $this->db->query($queryStr,array($deptCd,$deptCd,$deptCd,$deptCd,$deptCd,$deptCd, $fiscalYear, $fiscalMonth, $businessUnitCd));

         if($query && $query->result()){
            return $query->result()[0]->numrows;
        }
        log_message('info',"count_all_verify_payroll SQL= " . $this->db->last_query());            
        return 0;
    }
    catch(Exception $e){
        log_message('error',"count_all_verify_payroll: ".$e->getMessage());
        return "";
    }
}

    /**
     * Get list recon status
     * */
    public function get_list_reconStatus()
    {
        try {
            $this->db->select('Description,ReconStatusCd');
            $this->db->from("SOM_BFA_ReconStatus");
            $this->db->where("ReconStatusCd <> 2000");

            $query = $this->db->get();
            log_message('info',"get_list_reconStatus SQL= " . $this->db->last_query());            

            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_reconStatus: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get list category sumary
     * */
    public function get_list_category_sumary($userId, $year)
    {
        try {
            $sql = " Select ISNULL(PositionTitleCategory, 'Total') AS PositionTitleCategory,
            SUM(FTEM01) AS FTEM01,SUM(FTEM02) AS FTEM02,SUM(FTEM03) AS FTEM03,SUM(FTEM04) AS FTEM04,
            SUM(FTEM05) AS FTEM05,SUM(FTEM06) AS FTEM06,SUM(FTEM07) AS FTEM07,SUM(FTEM08) AS FTEM08,
            SUM(FTEM09) AS FTEM09,SUM(FTEM10) AS FTEM10,SUM(FTEM11) AS FTEM11,SUM(FTEM12) AS FTEM12,
            SUM(SalM01) AS SalM01,SUM(SalM02) AS SalM02,SUM(SalM03) AS SalM03,SUM(SalM04) AS SalM04,
            SUM(SalM05) AS SalM05,SUM(SalM06) AS SalM06,SUM(SalM07) AS SalM07,SUM(SalM08) AS SalM08,
            SUM(SalM09) AS SalM09,SUM(SalM10) AS SalM10,SUM(SalM11) AS SalM11,SUM(SalM12) AS SalM12
            From vw_SOM_AA_EmployeeCategorySummary 
            where SessionUserid = ? AND FiscalYear = ?
            GROUP BY PositionTitleCategory
            WITH ROLLUP";


            $query = $this->db->query($sql,array($userId,$year));    
            log_message('info',"get_list_category_sumary SQL= " . $this->db->last_query());            

            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_category_sumary: ".$e->getMessage());
            return "";
        }
    }
    
    /**
     * Get all expense detail data
     * */
    public function get_all_expense_detail($userId, $start, $length)
    {
        try {
            $queryStr = "select uniqueid,PositionTitleCategory,Employee_Name,Employee_Id,RecType,
            DeptCd,FundCd,ProjectCd,FunctionCd,FlexCd,PositionTitleCd,EmpChanged,M01,M02,M03
            from SOM_AA_EmployeeListRolling             
            where SessionUserid = ? AND  ( (M01  != 0 OR M02 != 0 OR M03 != 0)  OR (RecType IS  NULL))
            AND Employee_Name NOT IN ( 
                select Employee_Name from SOM_AA_EmployeeListRolling 
                where SessionUserid = ? AND (( M01  != 0 OR M02 != 0 OR M03 != 0) OR RecType IS NULL) 
                group by  Employee_Name,PositionTitleCategory
                having count(Employee_Name) = 1
            )";
            if ($length != 0){
                $queryStr =  $queryStr ." ORDER BY PositionTitleCategory, Employee_Name, Sort1, Sort2, PositionTitleCd, DeptCd, FundCd, ProjectCd, FunctionCd, FlexCd OFFSET  ? ROWS FETCH NEXT ? ROWS ONLY";
                $query = $this->db->query($queryStr,array($userId,$userId,(int)$start,(int)$length));
            }else{
             $queryStr =  $queryStr ." ORDER BY PositionTitleCategory, Employee_Name, Sort1, Sort2, PositionTitleCd, DeptCd, FundCd, ProjectCd, FunctionCd, FlexCd ";
             $query = $this->db->query($queryStr,array($userId,$userId));
         }

         log_message('info',"get_all_expense_detail SQL= " . $this->db->last_query());            
         return $query->result();     
     }
     catch(Exception $e){
        log_message('error',"get_all_expense_detail: ".$e->getMessage());
        return "";
    }
}

    /**
     * Get all expense detail data to export
     * */
    public function get_all_expense_detail_ToExport($userId,$changedEmp )
    {
            try {
                $queryStr = "select uniqueid,PositionTitleCategory,Employee_Name,Employee_Id,RecType,
                DeptCd,FundCd,ProjectCd,FunctionCd,FlexCd,PositionTitleCd,EmpChanged,M01,M02,M03
                from SOM_AA_EmployeeListRolling             
                where SessionUserid = ? AND  ( (M01  != 0 OR M02 != 0 OR M03 != 0)  OR (RecType IS  NULL))
                AND Employee_Name NOT IN ( 
                    select Employee_Name from SOM_AA_EmployeeListRolling 
                    where SessionUserid = ? AND (( M01  != 0 OR M02 != 0 OR M03 != 0) OR RecType IS NULL) 
                    group by  Employee_Name,PositionTitleCategory
                    having count(Employee_Name) = 1
                )";
                if($changedEmp==true){
                    $queryStr = $queryStr." AND Employee_Name IN (
                select Employee_Name from SOM_AA_EmployeeListRolling 
                where SessionUserid = 'Ucsfmanager@Gmail.Com'  and EmpChanged = 'CHG' AND ( (M01  != 0 OR M02 != 0 OR M03 != 0)  OR (RecType IS  NULL))         
                ) ";                    
                }
                $queryStr =  $queryStr ." ORDER BY PositionTitleCategory, Employee_Name, Sort1, Sort2, PositionTitleCd, DeptCd, FundCd, ProjectCd, FunctionCd, FlexCd ";
                 $query = $this->db->query($queryStr,array($userId,$userId));
             

             log_message('info',"get_all_expense_detail_ToExport SQL= " . $this->db->last_query());            
             return $query->result();     
         }
         catch(Exception $e){
            log_message('error',"get_all_expense_detail_ToExport: ".$e->getMessage());
            return "";
        }
    }


    /**
     * Count all expense detail data
     * */
    public function count_expense_detail($userId)
    {
        try {
            $countAll = 0;
            $countIn = 0;
            $queryStrCountAll = "SELECT COUNT(*) AS 'numrows'  
            from SOM_AA_EmployeeListRolling             
            where SessionUserid = ? AND  ( (M01  != 0 OR M02 != 0 OR M03 != 0)  OR (RecType IS  NULL)) ";
            $queryCountAll = $this->db->query($queryStrCountAll,array($userId));

            if($queryCountAll && $queryCountAll->result()){
                $countAll= $queryCountAll->result()[0]->numrows;
            }

            $queryStrCountIn = "SELECT COUNT(*) AS 'numrows'  
            from SOM_AA_EmployeeListRolling             
            where SessionUserid = ? AND  ( (M01  != 0 OR M02 != 0 OR M03 != 0)  OR (RecType IS  NULL))
            AND Employee_Name  IN ( 
            select Employee_Name from SOM_AA_EmployeeListRolling 
            where SessionUserid = ? AND (( M01  != 0 OR M02 != 0 OR M03 != 0) OR RecType IS NULL) 
            group by  Employee_Name,PositionTitleCategory
            having count(Employee_Name) = 1 )";
            $queryCountIn = $this->db->query($queryStrCountIn,array($userId,$userId));

            if($queryCountIn && $queryCountIn->result()){
                $countIn= $queryCountIn->result()[0]->numrows;
            }

            log_message('info',"count_expense_detail SQL= " . $this->db->last_query());  
            return $countAll-$countIn;      
        }
        catch(Exception $e){
            log_message('error',"count_expense_detail: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get all expense detail data wwith employee name
     * */
    public function get_expense_detail_with_empName($userId, $emp_name, $start, $length)
    {
        try {
            $this->db->select($this->column_expense_detail);
            $this->db->from($this->table_expense_detail);
            $this->db->where("( M01  != 0 OR M02 != 0 OR M03 != 0)");
            $this->db->where("SessionUserid",$userId);
            $this->db->where("Employee_Name",$emp_name);
            if ($length != 0)
                $this->db->limit($length,$start);
            $this->db->order_by($this->order_expense_detail);

            $query = $this->db->get();
            log_message('info',"get_expense_detail_with_empName SQL= " . $this->db->last_query());      
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_expense_detail_with_empName: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get all expense detail data wwith employee name
     * */
    public function get_expense_detail_with_empName_ToExport($userId, $emp_name,$changedEmp)
    {
        try {
            $this->db->select($this->column_expense_detail);
            $this->db->from($this->table_expense_detail);
            $this->db->where("( M01  != 0 OR M02 != 0 OR M03 != 0)");
            $this->db->where("SessionUserid",$userId);
            $this->db->where("Employee_Name",$emp_name);
            if($changedEmp==true){
                $this->db->where(" Employee_Name IN (
                select Employee_Name from SOM_AA_EmployeeListRolling 
                where SessionUserid = 'Ucsfmanager@Gmail.Com'  and EmpChanged = 'CHG' AND ( (M01  != 0 OR M02 != 0 OR M03 != 0)  OR (RecType IS  NULL))         
                ) ");
            }
            $this->db->order_by($this->order_expense_detail);

            $query = $this->db->get();
            log_message('info',"get_expense_detail_with_empName_ToExport SQL= " . $this->db->last_query());      
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_expense_detail_with_empName_ToExport: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Count all expense detail data wwith employee name
     * */
    public function count_expense_detail_with_empName($userId, $emp_name)
    {
        try {
            $this->db->from($this->table_expense_detail);
            $this->db->where("( M01  != 0 OR M02 != 0 OR M03 != 0)");
            $this->db->where("SessionUserid",$userId);
            $this->db->where("Employee_Name",$emp_name);

            $data= $this->db->count_all_results();
            log_message('info',"get_expense_detail_with_empName SQL= " . $this->db->last_query());      
            return $data;
        }
        catch(Exception $e){
            log_message('error',"count_expense_detail_with_empName: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Update comment and status in recon Employee GLV
     * */
    public function update_comment_status_in_reconEmployeeGLV($data) 
    {
        try {
            if($this->db->update_batch($this->table, $data, 'uniqueid')){
               log_message('info',"update_comment_status_in_reconEmployeeGLV SQL= " . $this->db->last_query());  
               return true;
           }
           else
            return false;
        }
        catch(Exception $e){
            log_message('error',"update_comment_status_in_reconEmployeeGLV: ".$e->getMessage());
            return false;
        }
    }

     /**
     * Get comments
     * */
    public function getComments($comment_glvtype) 
    {
        try {
            $query = $this->db->query("select Comment.Id, Comment.Comment, Comment.Date,Comment.UserId, lkp_userprofile.nameFirst, lkp_userprofile.nameLast
            from Comment inner join lkp_userprofile on Comment.UserId = lkp_userprofile.user_id
            and  Comment.Comment_GlvType = ? 
            order by Comment.Date desc",array($comment_glvtype));
            if($query){
                $list = $query->result();
                log_message('info',"getComments SQL= " . $this->db->last_query());  
                $data = array();
                if($list){
                    foreach ($list as $item) {
                        $row = array();
                        $row[] = $item->Id;
                        $row[] = $item->Comment;
                        $row[] = $item->nameFirst . ' ' . $item->nameLast;
                        $row[] =  date("Y-m-d H:i:s", strtotime($item->Date));
                        $row[] = $item->UserId;
                        $data[] = $row;
                    }
                    return $data;
                }
                return null;
            }
           
            return null;
        }
        catch(Exception $e){
            log_message('error',"getComments: ".$e->getMessage());
            return null;
        }
    }

    
     /**
     * Update comment 
     * */
    public function updateComments($comment,$commentId,$date) 
    {
        try {
            $data = array(
                'Comment' => $comment,
                'Date'  => $date
            );
            $uniqueId=0;
            $commentType="";
            
            $this->db->trans_start();
             $result = $this->db->query("select CT.UniqueId, CT.CommentType from Comment_GlvType as CT
             left join Comment as C on CT.Id = C.Comment_GlvType where C.Id =? " ,array($commentId))->row(); 
            
            $uniqueId = $result->UniqueId;
            $commentType = $result->CommentType;

                     // Set Not verified status for record when add comment
            if($commentType== PAYROLL_TYPE){
                $this->db->set('ReconStatusCd', 0);
                $this->db->where('uniqueid', $uniqueId);
                $this->db->update('SOM_BFA_ReconEmployeeGLV');
            }else{
                $this->db->set('ReconStatusCd', 0);
                $this->db->where('uniqueid', $uniqueId);
                $this->db->update('COA_SOM_LedgerData');
            }

            $this->db->where('Id', $commentId);
            $this->db->update('Comment', $data);

            $this->db->trans_complete(); 

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error',"updateComments error");
                return false;
            } 
            else {
                $this->db->trans_commit();
                log_message('info',"updateComments SQL= " . $this->db->last_query());  
                return true;
            }
        }
        catch(Exception $e){
            log_message('error',"updateComments: ".$e->getMessage());
            return false;
        }
    }
     /**
     * Add additional comment 
     * */
    public function addAdditionalComments($comment,$userId,$date,$comment_glvType) 
    {
        try {
            $data = array(
                'Comment' => $comment,
                'Date'  => $date,
                'UserId' => $userId,
                'Comment_GlvType' =>$comment_glvType
            );
            $uniqueId=0;
            $commentType="";
            
            
            $this->db->trans_start();
            $result = $this->db->query("select UniqueId,CommentType from Comment_GlvType where Id = ? " ,array($comment_glvType))->row(); 
            
            $uniqueId = $result->UniqueId;
            $commentType = $result->CommentType;

                     // Set Not verified status for record when add comment
            if($commentType== PAYROLL_TYPE){
                $this->db->set('ReconStatusCd', 0);
                $this->db->where('uniqueid', $uniqueId);
                $this->db->update('SOM_BFA_ReconEmployeeGLV');
            }else{
                $this->db->set('ReconStatusCd', 0);
                $this->db->where('uniqueid', $uniqueId);
                $this->db->update('COA_SOM_LedgerData');
            }


            $this->db->insert('Comment', $data);

            $this->db->trans_complete(); 

            
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error',"addAdditionalComments error");
                return false;
            } 
            else {
                $this->db->trans_commit();
                log_message('info',"addAdditionalComments SQL= " . $this->db->last_query());  
                               
                   
                return true;
            }
        }
        catch(Exception $e){
            log_message('error',"addAdditionalComments: ".$e->getMessage());
            return false;
        }
    }

     /**
     * Add new comment
     * */
    public function addNewComment($uniqueId,$comment,$userId,$date,$commentType) 
    {
        try {
            $comment_GlvTypeId= "";
            $this->db->trans_start();
            $this->db->query('INSERT INTO Comment_GlvType(UniqueId,CommentType) VALUES( ? , ?);', array($uniqueId,$commentType));
           
            $comment_GlvTypeId = $this->db->insert_id();
            $this->db->query('insert into Comment(Comment,Comment_GlvType,UserId,Date)
            values( ? , ? , ? , ? );', array($comment,$comment_GlvTypeId,$userId,$date));      
          
               // Set Not verified status for record when add comment
                if($commentType== PAYROLL_TYPE){
                    $this->db->set('ReconStatusCd', 0);
                    $this->db->where('uniqueid', $uniqueId);
                    $this->db->update('SOM_BFA_ReconEmployeeGLV');
                }else{
                        $this->db->set('ReconStatusCd', 0);
                    $this->db->where('uniqueid', $uniqueId);
                    $this->db->update('COA_SOM_LedgerData');
                }
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                log_message('error',"addNewComment error");
                return null;
            }
            else
            {
                $this->db->trans_commit();
            }
            log_message('info',"addNewComment SQL= " . $this->db->last_query());  
            return $comment_GlvTypeId;
        }
        catch(Exception $e){
            log_message('error',"addNewComment: ".$e->getMessage());
            return null;
        }
    }


   
   
}