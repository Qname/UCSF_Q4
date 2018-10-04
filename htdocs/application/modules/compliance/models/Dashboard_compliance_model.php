<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard compliance model
 */
class Dashboard_compliance_model extends CI_Model {
    var $table = 'vw_COA_SOM_Departments';
    // var $column = array('DeptCd','DeptTitle','DeptTitleCd','DeptLevel0Cd','DeptLevel1Cd','DeptLevel2Cd',
    // 'DeptLevel3Cd','DeptLevel4Cd','DeptLevel5Cd','DeptLevel6Cd','DeptLevel0Title','DeptLevel1Title',
    // 'DeptLevel2Title','DeptLevel3Title','DeptLevel4Title','DeptLevel5Title','DeptLevel6Title','DeptLevel0TitleCd',
    // 'DeptLevel1TitleCd','DeptLevel2TitleCd','DeptLevel3TitleCd','DeptLevel4TitleCd','DeptLevel5TitleCd',
    // 'DeptLevel6TitleCd','DeptTreeCd','DeptTreeTitle','DeptTreeTitleAbbrev','DeptSite','DeptTreeTitleCdAbbrev',
    // 'DeptGroupSOM','DeptPostingLevel','DeptPlanningLevel','DeptLevel','ReconDeptCd','ReconDeptTitle',
    // 'ReconDeptTreeCd','ZSOM_ReconDeptLevel');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /** Get list control point * */
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

    /** Get list compliance dashboard * */
    public function get_compliancedashboard($fy,$fp,$deptid,$bu,$site)
    {
        try {
            $query = $this->db->query("sp_SOM_GLV_CompSummary ?, ?, ?, ?, ?, 0, 0, 0", array($fy,$fp,$deptid,$bu,$site));
            $data = $query->result();
            log_message('info',"get_compliancedashboard SQL= " . $this->db->last_query());            
            return $data;
        }
        catch(Exception $e){
            log_message('error',"get_compliancedashboard: ".$e->getMessage());
            return "";
        }
    }

    /** Get list compliance dashboard detail report * */
    public function get_compliance_detailreport($fy,$fp,$deptid,$bu,$site)
    {
        try {
            if ($deptid=='999999') {
                $query = $this->db->query("sp_SOM_GLV_CompSummary ?, ?, ?, ?, ?, 0, 0, 0", array($fy,$fp,$deptid,$bu,$site));
                log_message('info',"get_compliance_detailreport SQL= " . $this->db->last_query());            
                $res = $query->result_array();
                if (count($res)>0 ) {
                  foreach ($res as $key => $row) {
                     //$res[$key]['artwork'] = $this->db->query("sp_SOM_GLV_CompSummary $fy, $fp, '$row[DeptCd]', '$bu', '$site', 0, 0, 0")->result_array();
                    $data = $this->db->query("sp_SOM_GLV_CompSummary ?, ?, '$row[DeptCd]', ?, ?, 0, 0, 0", array($fy,$fp,$bu,$site))->result_array();

                  $res[$key]['artwork'] = $data;
                    log_message('info',"get_compliance_detailreport SQL= " . $this->db->last_query());            
                }
            }
            
        }else{
            $query = $this->db->query("sp_SOM_GLV_CompSummary ?, ?, ?, ?, ?, 0, 0, 0", array($fy,$fp,$deptid,$bu,$site));
            log_message('info',"get_compliance_detailreport SQL= " . $this->db->last_query());            
            $res = $query->result_array();
        }
        return $res;
    }
    catch(Exception $e){
        log_message('error',"get_compliance_detailreport: ".$e->getMessage());
        return "";
    }
}

}
?>