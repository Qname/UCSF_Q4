<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Glverification_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Function filter data for GLV Verification    
     * */
    public function FilterDataGLV_Submit($sessionuserid,$deptcd,$depcdoverride,$bu,$site,$userid,$filtername,$fy,$fp,$withemp)
    {
        try {
            $query = $this->db->query("exec sp_SOM_GLV_Summary_AARolling ?, ?, ?, ?, ? ,? , ? , ?, ? , ?  ", array($sessionuserid,$deptcd,$depcdoverride,$bu,$site,$userid,$filtername,$fy,$fp,$withemp));
            log_message('info',"FilterDataGLV_Submit SQL= " . $this->db->last_query());

            if($query)
            {
                return true;
            }else
            return false;
        }
        catch(Exception $e){
            log_message('error',"FilterDataGLV_Submit: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Function get GLV item detail for click on item in Review and Verify Transactions tab
     * */
    public function get_VerifyGLVItemDetails($deptid,$bu,$fy,$fp,$reconitemcd,$reconstatuscd,$recongrouptitle,$priormonth,$start,$length,$filterName, $site,$columnName,$columnDir,$search_col,$search_val)
    {
        try {
            $where = $this->db->query(' select dbo.fn_SOM_BFA_GetWhereFromSavedFilter (?, ?, ?, ?, 0) as WhereStr ',array($this->session->userdata['userid'],$deptid,$deptid,$filterName))->result()[0]->WhereStr;
            log_message('info',"getquery filter SQL= " . $this->db->last_query());
            if ($reconitemcd == 0) {
                if ($priormonth == 1) {
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId  
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconGroupTitle = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod < ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ?";
                }else{
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId 
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconGroupTitle = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod = ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ?";
                }
                if ($site != "%"){
                    $queryStr=  $queryStr." AND DeptSite = ? AND  ".$where;
                } else{
                    $queryStr=  $queryStr." AND DeptSite like ? AND  ".$where;
                }

                if( strcmp($search_col,"CommentGLVTypeId")  == 0 ){
                    $queryStr=  $queryStr." AND Comment_GLVType.id > 0 " ;
                }else if ( strcmp($search_col,"ReconLink")  == 0 ){
                    $queryStr=  $queryStr." AND ( ReconLink is not NULL and ReconLink != '' ) " ;
                }else if ( strcmp($search_col,"user_name")  == 0){
                    if( strcmp($search_val,"")  == 0){
                        $queryStr=  $queryStr." AND (lkp_userprofile.user_name is NULL or lkp_userprofile.user_name = '' ) " ;  
                    }else{
                    $queryStr=  $queryStr." AND lkp_userprofile.user_name like '%".$search_val."%' " ;                        
                    }
                }else if ( strcmp($search_col,"ReconDate")  == 0 ||  strcmp($search_col,"InvoiceDate")  == 0 ||  strcmp($search_col,"JournalPostDt")  == 0){
                    if( strcmp($search_val,"")  == 0){
                         $queryStr=  $queryStr." AND ( CONVERT(varchar, ".$search_col.", 101) like '%%' or ".$search_col." is NULL ) " ;
                    }else{
                    $queryStr=  $queryStr." AND CONVERT(varchar, ".$search_col.", 101) like '%".$search_val."%' " ;
                    }                    
                }else{
                    $queryStr=  $queryStr." AND ".$search_col." like '%".$search_val."%' " ;                    
                }

                 

                    if ($length != 0){
                        $queryStr =  $queryStr ." ORDER BY ".$columnName." ".$columnDir." OFFSET  ? ROWS FETCH NEXT ? ROWS ONLY";
                        $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$recongrouptitle,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site,(int)$start,(int)$length));
                    } else{
                        $queryStr =  $queryStr ." ORDER BY ".$columnName." ".$columnDir." ";
                        $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$recongrouptitle,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site ));
                    }

                  
        
            }else{
                if ($priormonth == 1) {
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId 
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconItemCd = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod < ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
            
                }else{
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId 
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconItemCd = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod = ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
            
                }
                if ($site != "%"){
                    $queryStr=  $queryStr." AND DeptSite = ? AND  ".$where;
                } else{
                    $queryStr=  $queryStr." AND DeptSite like ? AND  ".$where;
                }

                 if( strcmp($search_col,"CommentGLVTypeId")  == 0 ){
                    $queryStr=  $queryStr." AND Comment_GLVType.id > 0 " ;
                }else if ( strcmp($search_col,"ReconLink")  == 0 ){
                    $queryStr=  $queryStr." AND ( ReconLink is not NULL and ReconLink != '' )    " ;
                }else if ( strcmp($search_col,"user_name")  == 0){
                    if( strcmp($search_val,"")  == 0){
                        $queryStr=  $queryStr." AND (lkp_userprofile.user_name is NULL or lkp_userprofile.user_name = '' ) " ;  
                    }else{
                    $queryStr=  $queryStr." AND lkp_userprofile.user_name like '%".$search_val."%' " ;                        
                    }
                }else if ( strcmp($search_col,"ReconDate")  == 0 ||  strcmp($search_col,"InvoiceDate")  == 0 ||  strcmp($search_col,"JournalPostDt")  == 0){
                    if( strcmp($search_val,"")  == 0){
                         $queryStr=  $queryStr." AND ( CONVERT(varchar, ".$search_col.", 101) like '%%' or ".$search_col." is NULL ) " ;
                    }else{
                    $queryStr=  $queryStr." AND CONVERT(varchar, ".$search_col.", 101) like '%".$search_val."%' " ;
                    }                    
                }else{
                    $queryStr=  $queryStr." AND ".$search_col." like '%".$search_val."%' " ;                    
                }
                
             

                     if ($length != 0){
                        $queryStr =  $queryStr ." ORDER BY ".$columnName." ".$columnDir." OFFSET  ? ROWS FETCH NEXT ? ROWS ONLY";
                        $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$reconitemcd,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site,
                        (int)$start,(int)$length));
                    } else{
                        $queryStr =  $queryStr ." ORDER BY ".$columnName." ".$columnDir." ";                        
                        $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$reconitemcd,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site ));
                    }

                
               
              
            //$rowcount = $query->num_rows();
            }
            $data = $query->result();
            log_message('info',"get_VerifyGLVItemDetails SQL= " . $this->db->last_query());

            return $data;
        }
        catch(Exception $e){
            log_message('error',"get_VerifyGLVItemDetails: ".$e->getMessage());
            return "";
        }
    }

     /**
     * Function get GLV item detail for click on item in Review and Verify Transactions tab to export
     * */
    public function get_VerifyGLVItemDetails_ToExport($deptid,$bu,$fy,$fp,$reconitemcd,$reconstatuscd,$recongrouptitle,$priormonth,$filterName, $site)
    {
        try {
            $where = $this->db->query(' select dbo.fn_SOM_BFA_GetWhereFromSavedFilter (?, ?, ?, ?, 0) as WhereStr ',array($this->session->userdata['userid'],$deptid,$deptid,$filterName))->result()[0]->WhereStr;
            log_message('info',"getquery filter SQL= " . $this->db->last_query());
            if ($reconitemcd == 0) {
                if ($priormonth == 1) {
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId  
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconGroupTitle = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod < ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ?";
                }else{
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId 
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconGroupTitle = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod = ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ?";
                }
                if ($site != "%"){
                    $queryStr=  $queryStr." AND DeptSite = ? AND  ".$where;
                } else{
                    $queryStr=  $queryStr." AND DeptSite like ? AND  ".$where;
                }

              
                    $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$recongrouptitle,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site ));
                
            }else{
                if ($priormonth == 1) {
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId 
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconItemCd = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod < ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
            
                }else{
                    $queryStr = "select lkp_userprofile.user_name,vw_COA_Report_Ledger_Details.*, Comment_GLVType.id as CommentGLVTypeId 
                    from vw_COA_Report_Ledger_Details 
                    LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ? 
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconItemCd = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod = ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
            
                }
                if ($site != "%"){
                    $queryStr=  $queryStr." AND DeptSite = ? AND  ".$where;
                } else{
                    $queryStr=  $queryStr." AND DeptSite like ? AND  ".$where;
                }
               
              
                    $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$reconitemcd,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site ));
                
              
            //$rowcount = $query->num_rows();
            }
            $data = $query->result();
            log_message('info',"get_VerifyGLVItemDetails_ToExport SQL= " . $this->db->last_query());

            return $data;
        }
        catch(Exception $e){
            log_message('error',"get_VerifyGLVItemDetails_ToExport: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Function count total item in GLV item detail
     * */
    public function count_GLVItemDetails($deptid,$bu,$fy,$fp,$reconitemcd,$reconstatuscd,$recongrouptitle,$priormonth,$filterName, $site,$search_col,$search_val)
    {
        try {
            $where = $this->db->query(' select dbo.fn_SOM_BFA_GetWhereFromSavedFilter (?, ?, ?, ?, 0) as WhereStr ',array($this->session->userdata['userid'],$deptid,$deptid,$filterName))->result()[0]->WhereStr;
            if ($reconitemcd == 0) {
                if ($priormonth == 1) {
                    $queryStr = "SELECT COUNT(*) AS 'numrows'  from vw_COA_Report_Ledger_Details
                     LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ?
                    where  systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconGroupTitle = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod < ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
                    
                }else{
                    $queryStr = "SELECT COUNT(*) AS 'numrows' from vw_COA_Report_Ledger_Details
                     LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ?
                     where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconGroupTitle = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod = ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
                }
                if ($site != "%"){
                    $queryStr=  $queryStr." AND DeptSite = ? AND  ".$where;
                } else{
                    $queryStr=  $queryStr." AND DeptSite like ? AND  ".$where;
                }

                 if( strcmp($search_col,"CommentGLVTypeId")  == 0 ){
                    $queryStr=  $queryStr." AND Comment_GLVType.id > 0 " ;
                }else if ( strcmp($search_col,"ReconLink")  == 0 ){
                    $queryStr=  $queryStr."  AND ( ReconLink is not NULL and ReconLink != '' )   " ;
                }else if ( strcmp($search_col,"user_name")  == 0){
                    if( strcmp($search_val,"")  == 0){
                        $queryStr=  $queryStr." AND (lkp_userprofile.user_name is NULL or lkp_userprofile.user_name = '' ) " ;  
                    }else{
                    $queryStr=  $queryStr." AND lkp_userprofile.user_name like '%".$search_val."%' " ;                        
                    }
                }else if ( strcmp($search_col,"ReconDate")  == 0 ||  strcmp($search_col,"InvoiceDate")  == 0 ||  strcmp($search_col,"JournalPostDt")  == 0){
                    if( strcmp($search_val,"")  == 0){
                         $queryStr=  $queryStr." AND ( CONVERT(varchar, ".$search_col.", 101) like '%%' or ".$search_col." is NULL ) " ;
                    }else{
                    $queryStr=  $queryStr." AND CONVERT(varchar, ".$search_col.", 101) like '%".$search_val."%' " ;
                    }                    
                }else{
                    $queryStr=  $queryStr." AND ".$search_col." like '%".$search_val."%' " ;                    
                }


                $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$recongrouptitle,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site));
            } else{
                if ($priormonth == 1) {
                    $queryStr = "SELECT COUNT(*) AS 'numrows'  from vw_COA_Report_Ledger_Details
                     LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ?
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconItemCd = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod < ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
                }else{
                    $queryStr = "SELECT COUNT(*) AS 'numrows' from vw_COA_Report_Ledger_Details
                     LEFT JOIN lkp_userprofile ON vw_COA_Report_Ledger_Details.ReconUser = lkp_userprofile.user_id
                    LEFT JOIN Comment_GLVType ON vw_COA_Report_Ledger_Details.uniqueid = Comment_GLVType.UniqueId 
                    AND Comment_GLVType.CommentType = ?
                    where systemledger='ACTUALS' And (AccountLevelACd='4000A'
                    Or AccountLevelACd='5000A' Or AccountLevelACd='5700A') And ReconItemCd = ? AND ReconStatusCd = ? AND FiscalYear = ?  AND FiscalPeriod = ?  AND (DeptLevel1Cd = ? OR DeptLevel2Cd = ?
                    OR DeptLevel3Cd = ? OR DeptLevel4Cd = ? OR DeptLevel5Cd = ? OR DeptLevel6Cd = ?) AND BusinessUnitCd =  ? ";
                }
                if ($site != "%"){
                    $queryStr=  $queryStr." AND DeptSite = ? AND  ".$where;
                } else{
                    $queryStr=  $queryStr." AND DeptSite like ? AND  ".$where;
                }

                if( strcmp($search_col,"CommentGLVTypeId")  == 0 ){
                    $queryStr=  $queryStr." AND Comment_GLVType.id > 0 " ;
                }else if ( strcmp($search_col,"ReconLink")  == 0 ){
                    $queryStr=  $queryStr."  AND ( ReconLink is not NULL and ReconLink != '' )   " ;
                }else if ( strcmp($search_col,"user_name")  == 0){
                    if( strcmp($search_val,"")  == 0){
                        $queryStr=  $queryStr." AND (lkp_userprofile.user_name is NULL or lkp_userprofile.user_name = '' ) " ;  
                    }else{
                    $queryStr=  $queryStr." AND lkp_userprofile.user_name like '%".$search_val."%' " ;                        
                    }
                }else if ( strcmp($search_col,"ReconDate")  == 0 ||  strcmp($search_col,"InvoiceDate")  == 0 ||  strcmp($search_col,"JournalPostDt")  == 0){
                    if( strcmp($search_val,"")  == 0){
                         $queryStr=  $queryStr." AND ( CONVERT(varchar, ".$search_col.", 101) like '%%' or ".$search_col." is NULL ) " ;
                    }else{
                    $queryStr=  $queryStr." AND CONVERT(varchar, ".$search_col.", 101) like '%".$search_val."%' " ;
                    }                    
                }else{
                    $queryStr=  $queryStr." AND ".$search_col." like '%".$search_val."%' " ;                    
                }

                $query =  $this->db->query(  $queryStr,array(TRANSACTION_TYPE,$reconitemcd,$reconstatuscd,$fy,$fp,$deptid,$deptid,$deptid,$deptid,$deptid,$deptid,$bu,$site));
            }
            log_message('info',"count_GLVItemDetails SQL= " . $this->db->last_query());
            if($query && $query->result()){
                return $query->result()[0]->numrows;
            }
            return "";
        }
        catch(Exception $e){
            log_message('error',"count_GLVItemDetails: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Function update record on modal GLV item detail
     * */
    public function update_verifyGLVItemsDetails($data)
    {
        try {
            if($this->db->update_batch("COA_SOM_LedgerData", $data, 'uniqueid')){
               log_message('info',"update_verifyGLVItemsDetails SQL= " . $this->db->last_query());            
               return true;
           }        
           else
            return false;
    }
    catch(Exception $e){
        log_message('error',"update_verifyGLVItemsDetails: ".$e->getMessage());
        return false;
    }
}

    /**
     * Function get acknowlege approve on tab dashboard
     * */
    public function get_acknowlegeapprove($deptid,$fy,$fp)
    {
        try {
            $query = $this->db->query("select * from vw_DeptID where DeptCd= ? and FY= ? and FP =?",array($deptid,$fy,$fp));
            $data = $query->result();
            log_message('info',"get_acknowlegeapprove SQL= " . $this->db->last_query());            

            return $data;
        }
        catch(Exception $e){
            log_message('error',"get_acknowlegeapprove: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Function submit acknowlege data on tabl dashbaord
     * */
    public function Submit_AcknowledgedData($deptid,$fy,$fp,$checked)
    {
        try {
            $query = $this->db->query("sp_GLV_Approve ? , ? , ? , ? , ? , ? ", array('Dept', $fy, $fp, $deptid, $checked , $this->session->userdata['userid']));
            log_message('info',"Submit_AcknowledgedData SQL= " . $this->db->last_query());            
            
            $result = array();
            if($query->num_rows()==0){
                $result['Code'] = "success";
            }else{
                $result['Code'] = "error";
            }
            return $result;
        }
        catch(Exception $e){
            log_message('error',"Submit_AcknowledgedData: ".$e->getMessage());
            return "";
        }
    }


}