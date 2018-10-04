<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Glverifivation_filter_model extends CI_Model {
    var $tbl_field_filters = "SOM_BFA_SavedChartFieldFilters";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get data for filter
     * */
    public function get_data_for_ddl_filter($userId, $deptId, $filterId) 
    {
        try {
            $this->db->select("ChartStrField,ChartStrValue,Except");
            $this->db->from($this->tbl_field_filters);
            $this->db->where("UserId", $userId );
            $this->db->where("DeptCdSaved", $deptId );
            $this->db->where("FilterName", $filterId );
            $this->db->order_by("FilterName");
            $query = $this->db->get();
            log_message('info',"get_data_for_ddl_filter SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_data_for_ddl_filter: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get site data of filter
     * */
    public function get_site_data_of_filter( $userId, $deptId, $filterId) 
    {
        try {
            $this->db->select("ChartStrField,ChartStrValue,Except");
            $this->db->from($this->tbl_field_filters);
            $this->db->where("UserId", $userId );
            $this->db->where("DeptCdSaved", $deptId );            
            $this->db->where("FilterName", $filterId );
            $this->db->where("ChartStrField", "Site");
            $query = $this->db->get();
            log_message('info',"get_site_data_of_filter SQL= " . $this->db->last_query()); 
            if ($query->num_rows() > 0){
                return $query->result()[0]->ChartStrValue;
            } else{
                return "(any)";
            }
            
        }
        catch(Exception $e){
            log_message('error',"get_site_data_of_filter: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get list filter name 
     * */
    public function get_list_filters($userId, $deptId) 
    {
        try {
            $sql = "select iif([integer]=1,'(no filter)','(default)') as FilterName0 "
            . "from zIntegers  where Integer in (2) "
            . "UNION ALL  SELECT SOM_BFA_SavedChartFieldFilters.FilterName "
            . "FROM SOM_BFA_SavedChartFieldFilters"
            . " WHERE (((SOM_BFA_SavedChartFieldFilters.UserId)=? ) "
            . "AND ((SOM_BFA_SavedChartFieldFilters.DeptCdSaved)=?) " 
            . "AND ((SOM_BFA_SavedChartFieldFilters.FilterName) not in ('(default)','(working)')) "
            . "AND ((SOM_BFA_SavedChartFieldFilters.ChartStrField)='DeptCdSaved')) "
            . "GROUP BY SOM_BFA_SavedChartFieldFilters.FilterName ORDER BY FilterName0";

            $query = $this->db->query($sql,array($userId,$deptId));       
            log_message('info',"get_list_filters SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_filters: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get list projectCd
     * */
    public function get_list_projectCd($deptId) 
    {
        try {
            $vreconDeptCd = $this->db->query("select ReconDeptCd from vw_COA_SOM_Departments where DeptCd= ?",array($deptId))->row()->ReconDeptCd;        
            $this->db->select("ProjectTitleCd,ProjectCd");
            $this->db->from("vw_SOM_BFA_ReconGroups");
            $this->db->where("ReconDeptCd", $vreconDeptCd );
            $this->db->group_by("ProjectCd, ProjectTitleCd, ProjectUseShort");
            $this->db->having("ProjectCd <> '-------'", null, false);
            $this->db->order_by("ProjectCd");

            $query = $this->db->get();
            log_message('info',"get_list_projectCd SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_projectCd: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get list funcCd
     * */
    public function get_list_funcCd() 
    {
        try {
            $this->db->select("FundTreeTitleShort,FundCd");
            $this->db->from("vw_COA_SOM_Funds_Tree");
            $this->db->group_by("FundCd, FundTreeTitleShort, FundTreeCd");
            $this->db->having("FundCd Is Not Null", null, false);
            $this->db->order_by("FundTreeCd");

            $query = $this->db->get();
            log_message('info',"get_list_funcCd SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_funcCd: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get list ProjectManager
     * */
    public function get_list_projectMgr($deptId) 
    {
        try {
            $vreconDeptCd = $this->db->query("select ReconDeptCd from vw_COA_SOM_Departments where DeptCd= ?",array($deptId))->row()->ReconDeptCd;                
            $this->db->select("ProjectManager,ProjectManagerCd");
            $this->db->from("vw_SOM_BFA_ReconGroups");
            $this->db->where("ReconDeptCd",  $vreconDeptCd );
            $this->db->where("ProjectManagerCd !=",'DEPT/DIV' );
            $this->db->group_by("ProjectManager,ProjectManagerCd");
            $this->db->order_by("ProjectManager");

            $query = $this->db->get();
            log_message('info',"get_list_projectMgr SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_projectMgr: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get Project Manager Cd by Project Manager
     * */
    public function get_projectMgrCd_by_projecMgr($projectMgr) 
    {
        try {
            $this->db->select("ProjectManagerCd");
            $this->db->from("vw_SOM_BFA_ReconGroups");
            $this->db->where("ProjectManager", $projectMgr);
            $this->db->group_by("ProjectManager,ProjectManagerCd");
            $this->db->order_by("ProjectManager");

            $query = $this->db->get();
            log_message('info',"get_projectMgrCd_by_projecMgr SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_projectMgrCd_by_projecMgr: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get list Project Use title
     * */
    public function get_list_projectUse() 
    {
        try {
            $this->db->select("ProjectUseTitle,ProjectUseShort");
            $this->db->from("vw_COA_SOM_ProjectUses");
            $this->db->group_by("ProjectUseShort, ProjectUseTitle");
            $this->db->order_by("ProjectUseTitle");

            $query = $this->db->get();
            log_message('info',"get_list_projectUse SQL= " . $this->db->last_query());  
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_projectUse: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Delete filter
     * */
    public function delete_filters_saved($userId,$deptId,$filterId) 
    {
        try {
            $array = array('UserId' => $userId, 'DeptCdSaved' => $deptId, 'FilterName' => $filterId);
            $this->db->where($array);
            if($this->db->delete($this->tbl_field_filters)){
               log_message('info',"delete_filters_saved SQL= " . $this->db->last_query());  

               return true;
           }
           else 
            return false;
    }
    catch(Exception $e){
        log_message('error',"delete_filters_saved: ".$e->getMessage());
        return false;
    }
}

    /**
     * Save filter
     * */
    public function save_filter($userId,$deptId,$filterId,$data) 
    {
        try {
            $this->delete_filters_saved($userId,$deptId,$filterId);
            if($this->db->insert_batch($this->tbl_field_filters, $data)){
               log_message('info',"save_filter SQL= " . $this->db->last_query());  

               return true;
           }
           else 
            return false;
    }
    catch(Exception $e){
        log_message('error',"save_filter: ".$e->getMessage());
        return false;
    }
}

    /**
     * Check duplicate filter name
     * */
    public function check_duplicate_filterName($userId,$deptId,$filterId) 
    {
        try {
            $array = array('UserId' => $userId, 'DeptCdSaved' => $deptId, 'FilterName' => $filterId);
            $this->db->where($array);
            $query = $this->db->get($this->tbl_field_filters);
            log_message('info',"check_duplicate_filterName SQL= " . $this->db->last_query());  

            if ($query->num_rows() > 0)
                return true;
            else
                return false;
        }
        catch(Exception $e){
            log_message('error',"check_duplicate_filterName: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Save new filter
     * */
    public function save_as_filter($userId,$deptId,$filterId,$data) 
    {
        try {
            if($this->db->insert_batch($this->tbl_field_filters, $data)){
               log_message('info',"save_as_filter SQL= " . $this->db->last_query());  
               
               return true;
           }
           else 
            return false;
    }
    catch(Exception $e){
        log_message('error',"save_as_filter: ".$e->getMessage());
        return false;
    }
}
}