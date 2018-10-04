<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This model includes different functions for getting reports on GLVerification menu
 * */
class Glverification_report_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get DeptLevel1Cd base on its DeptCd
     * */
    function GetDeptLevel1Cd ($depId){
        try{
            $query = $this->db->query("SELECT DeptLevel1Cd FROM vw_COA_SOM_Departments WHERE DeptCd = ? " ,array($depId));
            log_message('info',"GetDeptLevel1Cd SQL= " . $this->db->last_query());
            $ret = $query->row();
            $depLevel1Cd = $ret->DeptLevel1Cd;
            return $depLevel1Cd;
        }
        catch(Exception $e){
            log_message('error','GetDeptLevel1Cd: '.$e->getMessage());
            return "";
        }
        
    }

    /**
     * Get Url Report 
     * in case depLevel1Cd = 10000
     * */
    function GetUrlReport_SOM($deptId,$site,$fy,$businessUnitCd,$depLevel1Cd){
        try{
            $query = $this->db->query("SELECT String FROM SOM_BFA_Variables WHERE VariableSet = ? and Variable = ? ", array('PlusPlan','SSRSFolder'));
            log_message('info',"GetUrlReport_SOM SQL= " . $this->db->last_query());
            $ret = $query->row();
            $url =  $ret->String;
            $url = $url."COA+-+Budget+Variance_Rpt&rs:Command=Render&rc:Parameters=Collapsed";
            if ( $site == "(any)" ){
                $url = $url . "&rpFiscalYear=" . $fy .  "&rpDeptLevel1Cd=" . $depLevel1Cd   . "&vDept=" .  $deptId .  "&rpShowMonths=1&rpBusinessUnitCd=" .  $businessUnitCd .  "&rpFundLevel:isnull=true";
            } else {
                $url = $url . "&rpFiscalYear=" . $fy .  "&rpDeptLevel1Cd=" . $depLevel1Cd   . "&vDept=" .  $deptId .  "&rpShowMonths=1&rpBusinessUnitCd=" .  $businessUnitCd .  "&rpFundLevel:isnull=true". "&rpDeptSite=" .  $site;
            }
            return $url;
        }
        catch(Exception $e){
            log_message('error','GetUrlReport_SOM: '.$e->getMessage());
            return "";
        }     
       
    }

     /**
     * Get Url Report 
     * in case depLevel1Cd != 10000
     * */
    function GetUrlReport_MyReport($businessUnitCd,$depLevel1Cd,$deptId,$reportDateString){
        //set default to level C for all departments
        $accLevel = "C";   
        try{
            $queryStr = "exec sp_SOM_GLV_MyReportsURL ? , ? , ? , ? ";
            $query = $this->db->query($queryStr, array($businessUnitCd, $deptId, $accLevel,$reportDateString ));
            log_message('info',"GetUrlReport_MyReport SQL= " . $this->db->last_query());
            $ret = $query->row();
            $url =  $ret->MyReportsURL;
            return $url;
        }
        catch(Exception $e){
            log_message('error','GetUrlReport_MyReport: '.$e->getMessage());
            return "";
        }
        
    }

    /**
     * Get monthly trend percent
     * $vFy: selected year
     * $vfp: selected month
     * $vDepCd: selected DepCd
     * */
    function GetMonthlyTrendPercent($vFy,$vfp,$vDeptCd){
        try{
            $vReconDeptCd = $this->db->query("select ReconDeptCd from vw_COA_SOM_Departments where DeptCd= ? ", array($vDeptCd))->row()->ReconDeptCd;        
            $queryStr = "SELECT SOM_BFA_ReconApproveTrend.FiscalYear, SOM_BFA_ReconApproveTrend.FiscalPeriod, 
            Sum(SOM_BFA_ReconApproveTrend.CheckedState)
            AS Verified, SUM(1) as Count from SOM_BFA_ReconApproveTrend
            where 
            ((SOM_BFA_ReconApproveTrend.ReconDeptCd)= ? ) AND ((SOM_BFA_ReconApproveTrend.DeptTreeCd) like ?  )
            AND ((SOM_BFA_ReconApproveTrend.DeptPostingLevel='Y')) 
            GROUP BY SOM_BFA_ReconApproveTrend.FiscalYear, SOM_BFA_ReconApproveTrend.FiscalPeriod 
            HAVING (((SOM_BFA_ReconApproveTrend.FiscalYear) = ? ) AND ((SOM_BFA_ReconApproveTrend.FiscalPeriod) = ? )) ";
            $query = $this->db->query( $queryStr,array($vReconDeptCd,"%".$vDeptCd."%",$vFy,$vfp));
            log_message('info',"GetMonthlyTrendPercent SQL= " . $this->db->last_query());
            
            $ret = $query->row();
            
            if ($ret != null){
                $percentage =$ret->Verified/ $ret->Count;
            } 
            else
            {
                $percentage = 0;
            }
            return $percentage;  
        }
        catch(Exception $e){
            log_message('error',"GetMonthlyTrendPercent: ".$e->getMessage());
            return 0;
        }            
        
    }
     /** Monthly Trend Change
     * Submit data when monthly trend dropdown change status and save button was clicked
     *  $fy: selected year
     *  $p: selected month
     *  $deptId: selected DepCd
     *  $flag = 0 if NotVerified and = 1 if Completed
     * */
     function MonthlyTrendChange($fy,$fp,$deptId, $flag){
        try {
            //check if The current department level is too high for this feature
            $checkQuery =$this->db->query("select ReconDeptCd from vw_COA_SOM_Departments where DeptCd= ? ",array( $deptId));
            if(!$checkQuery->result() || ($checkQuery->result() && ($checkQuery->result()[0]->ReconDeptCd == '------' || $checkQuery->result()[0]->ReconDeptCd == ''))){
             return false;
         } else{
            $query = $this->db->query("exec sp_GLV_Approve ? , ? , ? , ? , ? ,? ", array( 'Trend', $fy,$fp,$deptId, $flag,$this->session->userdata['userid'] ));
            log_message('info',"MonthlyTrendChange SQL= " . $this->db->last_query());
            if($query)
            {
                /* get monthlyTrend percentage */
                $percentage= $this->glverification_report->GetMonthlyTrendPercent($this->session->userdata('fy'),$this->session->userdata('fp'),$this->session->userdata('deptid'),$this->session->userdata('deptid'));
                $this->session->set_userdata('monthly_percentage',$percentage);
                return true;
            }else
            {
                return false;
            }
        }
        
    } catch (Exception $e) {
        log_message('error','MonthlyTrendChange: '.$e->getMessage());
        return false;
    }
}

}