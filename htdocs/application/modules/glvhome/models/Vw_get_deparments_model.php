<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Get Department data model
 */
class Vw_get_deparments_model extends CI_Model {
    var $table = 'vw_Get_Deparments';
    var $column = array('DeptTreeTitleAbbrev','DeptCd'); // Set column field database for datatable searchable

    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
	 * Get list rollup by control point Cd
     * 
	 * @param	string  $deptCd     Id of department Cd
	 * @return	array
	 */
    public function get_list_rollup_by_controlPoint($deptCd)
    {
        try {
            $this->db->select($this->column);
            $this->db->from($this->table);
            $this->db->where('DeptLevel1Cd', $deptCd);
            $this->db->order_by('DeptTreeCd');

            $query = $this->db->get();
            log_message('info',"get_list_rollup_by_controlPoint SQL= " . $this->db->last_query());            
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_rollup_by_controlPoint: ".$e->getMessage());
            return "";
        }
    }

    /**
	 * Get list rollup by Department Cd
     * 
	 * @param	string  $deptCd     Id of department Cd
	 * @return	array
	 */
    public function get_list_rollup_by_deptId($deptId)
    {
        try {
            if (substr($deptId,0,1)=="2" || substr($deptId,0,1)=="H" || (int)$deptId>566000 || strlen($deptId)>6 || $deptId=='000000') return "";
        // Get DeptLevel1Cd of Department ID 
            $queryGetDeptLv1 = $this->db->select('DeptLevel1Cd')->from($this->table)->where("DeptCd",$deptId)->where("DeptLevel1Cd !='200000'")->get()->result();
            
            if(count($queryGetDeptLv1)>0)
            {
                $deptLv1Cd =$queryGetDeptLv1[0]->DeptLevel1Cd;
                $this->db->select($this->column);
                $this->db->from($this->table);
                $this->db->where("DeptLevel1Cd", $deptLv1Cd);
                $this->db->where("DeptCd != '999999'");
                $this->db->order_by('DeptTreeCd');

                $query = $this->db->get();
                log_message('info',"get_list_rollup_by_deptId SQL= " . $this->db->last_query());            
                return $query->result();
            }
            else {
                return "";
            }   
        }
        catch(Exception $e){
            log_message('error',"get_list_rollup_by_deptId: ".$e->getMessage());
            return "";
        }  
    }
}
?>