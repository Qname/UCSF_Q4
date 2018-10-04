<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Get SOM Department data model
 */
class Vw_coa_som_departments_model extends CI_Model {
    var $table = 'vw_COA_SOM_Departments';
    
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
	 * Get list control point
     * 
	 * @return	array
	 */
    public function get_list_control_point()
    {
        try {
            $this->db->select("CASE WHEN DeptCd = '------' THEN DeptTitle ELSE ((DeptCd + '-') +  REPLACE(SUBSTRING(DeptTitle, CHARINDEX('_', DeptTitle), LEN(DeptTitle)) , '_', '')) END AS DeptTitle, DeptCd");
            $this->db->from($this->table);
            $this->db->where('DeptLevel = 1');
            $this->db->not_like('DeptCd', '2', 'after');
            $this->db->not_like('DeptCd', '634000');
            $this->db->not_like('DeptCd', '------');
            $query = $this->db->get();
            log_message('info',"get_list_control_point SQL= " . $this->db->last_query());            
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_list_control_point: ".$e->getMessage());
            return "";
        }
    }
}
?>