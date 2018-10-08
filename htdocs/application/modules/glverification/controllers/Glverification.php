
<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Glverification extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('General_model','general');
        $this->load->model('Glverification_model','glverification');
        $this->load->model('Glverification_transactions_model','glverification_transactions');
        $this->load->model('Glverification_payroll_model','glverification_payroll');
        $this->load->model('Glverification_report_model','glverification_report');
        $this->load->model('Glverifivation_filter_model','glverification_filter');
        $this->load->model('Glverification_document_model','glverification_document');
        $this->load->helper('function');
        $this ->load->library('form_validation');
    }

    public function index()
    {
        add_js(array('GLVerification_payroll.js'));
        add_js(array('GLVerification_filter.js'));
        add_js(array('GLVerification_MonthlyTrend.js'));
        add_js(array('GLVerification.js'));
        
        if (isset($this->session->userdata['userid']))
        {
            ini_set( 'memory_limit', '2400M' );
            ini_set('upload_max_filesize', '2400M');
            ini_set('post_max_size', '2400M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);

            $uid = $this->session->userdata['userid'];
            $deptid="";
            $bu =isset($this->session->userdata['bu']) ?  $this->session->userdata['bu'] : "SFCMP";
            $site = "%";
            $myfilters = "(default)";
            $this->session->set_userdata('bu', $bu);
            //convert back time
            $logfy= $logfp = "";
            //set default value for FY FP
            if (isset($this->session->userdata['fp']) and isset($this->session->userdata['fy'])) {
                $fp =$this->session->userdata['fp'];
                $fy =$this->session->userdata['fy'];
                if ($fp < 7) {
                    $reportdate=($this->general->getmonth_name(($fp+12)-6))." ".($fy-1);
                }else{
                    $reportdate=($this->general->getmonth_name(($fp)-6))." ".$fy;
                }
                if($fp>6){
                    $logfy = $fy;
                    $logfp = $fp-6;
                } else{
                    $logfy = $fy-1;
                    $logfp = $fp+6;  
                }
            }else{
                $fp = $this->general->GetFPFYdefault("DefaultFPMax");
                $fy = $this->general->GetFPFYdefault("DefaultFY");

                if ($fp<7) {
                    $fp=$fp+6;
                    $fy=$fy-1;
                    $this->session->set_userdata('fp', $fp-6);
                    $this->session->set_userdata('fy', $fy+1);
                }else{
                    $fp=$fp-6;
                    $fy=$fy;
                    $this->session->set_userdata('fp', $fp+6);
                    $this->session->set_userdata('fy', $fy);
                }
                $logfy = $fy;
                $logfp = $fp;
                $reportdate=$this->general->getmonth_name($fp)." ".$fy;
            }
            
            $urldeptid = $this->input->get("deptid");
            // check if $urldeptid existed
            if($urldeptid && !$this->general->CheckIfDeptCdExisted($urldeptid)){
                redirect('/glvhome');
            }
            $this->session->set_userdata('urlDeptId', $urldeptid);
            $defaultdeptid = $this->general->getdefaultdeptid($uid);
            
            
            if ($urldeptid==""){          
                if ($defaultdeptid !=""){
                     //set up trend check
                   $this->general->SetupTrendCheck($this->session->userdata('fy'),$this->session->userdata('fp'),$defaultdeptid);
                   
                   $listdeparment = $this->general->get_listdepartment($defaultdeptid,$this->session->userdata('fy'),$this->session->userdata('fp'));
               }else {
                   $listdeparment = $this->general->get_listdepartment("",$this->session->userdata('fy'),$this->session->userdata('fp'));
               }
               $deptid=$defaultdeptid;
           }
           else
           {
                 //set up trend check
            $this->general->SetupTrendCheck($this->session->userdata('fy'),$this->session->userdata('fp'),$urldeptid);
            
            if(isset($_GET['deptid']) AND trim($_GET['deptid']) != ''AND is_numeric($_GET['deptid'])) {
                $deptid=$urldeptid;
            }else{
                    //log event submit into log file
                log_message('info','User '.$this->session->userdata['userid'].' submitted the application for DeptCd: '.$urldeptid.
                    ' on report date: '. $logfp.'-'.$logfy. ' for business unit: '.$bu.' and site: '. ($site == "%" ? '(any)':$site ));
                redirect('/glvhome');
            }
            $listdeparment = $this->general->get_listdepartment($deptid,$this->session->userdata('fy'),$this->session->userdata('fp')); 
            
        }
            //log event submit into log file
        log_message('info','User '.$this->session->userdata['userid'].' submitted the application for DeptCd: '.$deptid. 
            ' on report date: '. $logfp.'-'.$logfy. ' for business unit: '.$bu.' and site: '. ($site == "%" ? '(any)':$site ));
        
        

        if(!isset($deptid) or trim($deptid) == '' or !is_numeric($deptid)) {              
            redirect('/glvhome');
        }

            //set session for deptid
        $this->session->set_userdata('deptid',$deptid);
            $this->session->set_userdata('bu',$bu);//set session for bu
            $this->session->set_userdata('site',$site);//set session for site
            $this->session->set_userdata('myfilter',$myfilters);//set session for my filter

            //auto submit data
            $this->glverification->FilterDataGLV_Submit($uid,$deptid,$deptid,$bu,$site,$uid,$myfilters,$this->session->userdata('fy'), $this->session->userdata('fp'),"1");

            //check acknowlege
            $approve_ack = $this->GetAcknowledgerApprove($deptid,$this->session->userdata('fy'),$this->session->userdata('fp'));

            
            $this->load->helper('url');
            $data['bu'] = $bu;
            $data['reportdate']=$reportdate;
            $data['listdeptid'] = $listdeparment;
            $data['defaultdeptid']=$deptid;
            $data['bu']=$bu;
            $data['site']=$site;
            $data['myfilters']=$myfilters;
            $data['approve_ack']=$approve_ack;           
            $data['title']="UCSF GL Verification System";
            $data['template']='index';
            //get monthlyTrend percentage
            $percentage= $this->glverification_report->GetMonthlyTrendPercent($this->session->userdata('fy'),$this->session->userdata('fp'),$this->session->userdata('deptid'));
            $this->session->set_userdata('monthly_percentage',$percentage);

            $this->load->view("shared/layout",$data);

        }else{
            redirect('/login');
        }
    }

    /**
     * Export transaction data to excel
     **/
    public function exportTransaction() {

        $userid=$this->session->userdata['userid'];
        $recongouptitle = $this->input->post('recongouptitle');
        $listHeader = $this->input->post('listHeader');
        
        // create file name
        $fileName = 'data-'.time().'.xlsx';
        // load excel library
        $this->load->library('excel');
        $transactionInfo = $this->glverification_transactions->get_ReviewVerifyTransactions_ToExport($userid,$recongouptitle);
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);  
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Group');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Not Verified');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Pending');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Complete');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Auto Complete');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Prior Not Verified');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Prior Pending');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Not Verified Count');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Pending Count');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Complete Count');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Auto Complete Count');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Prior Not Verified Count');   
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Prior Pending Count');
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', $listHeader[0]);
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', $listHeader[1]);
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', $listHeader[2]);
        $objPHPExcel->getActiveSheet()->SetCellValue('R1', $listHeader[3]);
        $objPHPExcel->getActiveSheet()->SetCellValue('S1', $listHeader[4]);
        $objPHPExcel->getActiveSheet()->SetCellValue('T1', $listHeader[5]);       
        $objPHPExcel->getActiveSheet()->SetCellValue('U1', $listHeader[6]);
        $objPHPExcel->getActiveSheet()->SetCellValue('V1', $listHeader[7]);
        $objPHPExcel->getActiveSheet()->SetCellValue('W1', $listHeader[8]);       
        $objPHPExcel->getActiveSheet()->SetCellValue('X1', $listHeader[9]);
        $objPHPExcel->getActiveSheet()->SetCellValue('Y1', $listHeader[10]);
        $objPHPExcel->getActiveSheet()->SetCellValue('Z1', $listHeader[11]);                    
        $objPHPExcel->getActiveSheet()->SetCellValue('AA1', 'Total');                    
        // set Row
        $rowCount = 2;
        foreach ($transactionInfo as $transactions) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $transactions->ReconGroupTitle);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $transactions->ReconItemTitle);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $transactions->NotVerified);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $transactions->Pending);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $transactions->Complete);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $transactions->AutoComplete);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $transactions->PriorNotVerified);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $transactions->PriorPending);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $transactions->NotVerifiedCount);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $transactions->PendingCount);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $transactions->CompleteCount);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $transactions->AutoCompleteCount);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $transactions->PriorNotVerifiedCount);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $transactions->PriorPendingCount);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $transactions->AmtM01x);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $transactions->AmtM02x);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $transactions->AmtM03x);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $transactions->AmtM04x);
            $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $transactions->AmtM05x);
            $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $transactions->AmtM06x);
            $objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $transactions->AmtM07x);
            $objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, $transactions->AmtM08x);
            $objPHPExcel->getActiveSheet()->SetCellValue('W' . $rowCount, $transactions->AmtM09x);
            $objPHPExcel->getActiveSheet()->SetCellValue('X' . $rowCount, $transactions->AmtM10x);
            $objPHPExcel->getActiveSheet()->SetCellValue('Y' . $rowCount, $transactions->AmtM11x);
            $objPHPExcel->getActiveSheet()->SetCellValue('Z' . $rowCount, $transactions->AmtM12x);
            $objPHPExcel->getActiveSheet()->SetCellValue('AA' . $rowCount, $transactions->AmtTotx);
            $rowCount++;
        } 

   
        
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);  
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');      
        ob_start();
        $objWriter->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );

        die(json_encode($response));
        
              
    }

    /**
     * Get list department base on deptId that user has choosen on GLHome tab
     **/
    public function GetListDepartment(){
        $defaultdeptid = $this->session->userdata('urlDeptId');
        if ($defaultdeptid !=""){
            $listdeparment = $this->general->get_listdepartment($defaultdeptid,$this->session->userdata('fy'),$this->session->userdata('fp'));
        }else {
            $deptid = $this->input->post("selectedDeptId");
            $listdeparment = $this->general->get_listdepartment( $deptid,$this->session->userdata('fy'),$this->session->userdata('fp'));
        }
        echo json_encode($listdeparment);
    }

     /**
     * Get list department base on deptId and report Date that user has choosen on GLHome tab
     **/
     public function GetListDepartmentBaseOnReportDate(){
        $fp = $this->input->post('month');
        $fy = $this->input->post('year');
        /* convert current date  */
        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
        }else{
            $fp = $fp - 6;
            $fy = $fy+1;
        }
        $urlDeptId = $this->session->userdata('urlDeptId');
        $defaultdeptid = $this->general->getdefaultdeptid($this->session->userdata['userid']);

        if ($urlDeptId !=""){
            $listdeparment = $this->general->get_listdepartment($urlDeptId,$fy,$fp);
        }else {
            if ($defaultdeptid !=""){
                $listdeparment = $this->general->get_listdepartment($defaultdeptid,$this->session->userdata('fy'),$this->session->userdata('fp'));
            }else {
                $listdeparment = $this->general->get_listdepartment("",$this->session->userdata('fy'),$this->session->userdata('fp'));
            }
        }
        echo json_encode($listdeparment);
    }

    /**
     * Get header filter for GLVdata
     **/
    public function headerfilterglvdata_submit()
    {
     
        $uid = $this->session->userdata['userid'];
        $fp = $this->general->getmonth_reportdate($this->input->post('reportdate'));
        $fy = $this->general->getyear_reportdate($this->input->post('reportdate'));
        $deptid = $this->input->post('deptid');
        $bu = $this->input->post('busunit');
        $this->session->set_userdata('bu', $bu);
        $userId = $this->session->userdata['userid'];

        $myfilter = $this->input->post('myfilter');

        $site = $this->glverification_filter->get_site_data_of_filter($userId,$deptid,$myfilter);        
         //log event submit into log file
        log_message('info','User '.$this->session->userdata['userid'].' submitted the application for DeptCd: '.$deptid. 
           ' on report date: '. $fp.'-'.$fy. ' for business unit: '.$bu.' and site: '. ($site == "%" ? '(any)':$site ) .' with filter '.$myfilter);

        $this->session->set_userdata('bu',$bu);  //set session for bus unit
        $this->session->set_userdata('deptid',$deptid);//set session for deptid
        $this->session->set_userdata('site',$site);//set session for site
        $this->session->set_userdata('myfilter',$myfilter);//set session for my filter

        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
            $this->session->set_userdata('fp', $fp);
            $this->session->set_userdata('fy', $fy);
            $reportdate=($this->general->getmonth_name(($fp)-6))." ".$fy;
        }else{
            $fp = $fp - 6;
            $fy = $fy+1;
            $this->session->set_userdata('fp', $fp);
            $this->session->set_userdata('fy', $fy);
            $reportdate=($this->general->getmonth_name(($fp+12)-6))." ".($fy-1);
        }

        //set up trend check
        $this->general->SetupTrendCheck($this->session->userdata('fy'),$this->session->userdata('fp'),$deptid);
        $siteStr = $site == "(any)"? "%": $site;
        $data['issuccess']= $this->glverification->FilterDataGLV_Submit($uid,$deptid,$deptid,$bu,$siteStr,$uid,$myfilter,$fy,$fp,"1");
        $data['approve'] = $this->GetAcknowledgerApprove($deptid,$fy,$fp);
        $data['reportdate']=$reportdate;

        echo json_encode($data);
    }

    /**
     * Check array contain value
     **/
    function myArrayContainsValue(array $myArray, $value) {
        foreach ($myArray as $element) {
            if ($element->DeptCd == $value ){
                return true;
            }
        }
        return false;
    }


    /**
     * Get status for acknowlegde
     **/
    function getStatusForAcknowlegde() {
        $fp = $this->general->getmonth_reportdate($this->input->post('reportdate'));
        $fy = $this->general->getyear_reportdate($this->input->post('reportdate'));
        $deptid = $this->input->post('deptid');

        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
            $this->session->set_userdata('fp', $fp);
            $this->session->set_userdata('fy', $fy);
        }else{
            $fp = $fp - 6;
            $fy = $fy+1;
            $this->session->set_userdata('fp', $fp);
            $this->session->set_userdata('fy', $fy);
        }
        $data['approve'] = $this->GetAcknowledgerApprove($deptid,$fy,$fp);
        echo json_encode($data);
    }

    /**
     * Get list transactions
     **/
    public function ajax_listtransactions()
    {
        $userid=$this->session->userdata['userid'];
        $recongouptitle = $this->input->post('recongouptitle');
        $start = $_POST["start"];
        $length = $_POST["length"];
        $list = $this->glverification_transactions->get_ReviewVerifyTransactions($userid,$recongouptitle, $start, $length);
        $data = array();
        foreach ($list as $transactions) {
            $row = array();
            $row[] = $transactions->ReconItemCd;
            $row[] = $transactions->ReconGroupTitle;
            $row[] = $transactions->ReconItemTitle;
            $row[] = $transactions->NotVerified;
            $row[] = $transactions->Pending;
            $row[] = $transactions->Complete;
            $row[] = $transactions->AutoComplete;
            $row[] = $transactions->PriorNotVerified;
            $row[] = $transactions->PriorPending;
            $row[] = $transactions->NotVerifiedCount;
            $row[] = $transactions->PendingCount;
            $row[] = $transactions->CompleteCount;
            $row[] = $transactions->AutoCompleteCount;
            $row[] = $transactions->PriorNotVerifiedCount;
            $row[] = $transactions->PriorPendingCount;
            $row[] = $transactions->AmtM01x;
            $row[] = $transactions->AmtM02x;
            $row[] = $transactions->AmtM03x;
            $row[] = $transactions->AmtM04x;
            $row[] = $transactions->AmtM05x;
            $row[] = $transactions->AmtM06x;
            $row[] = $transactions->AmtM07x;
            $row[] = $transactions->AmtM08x;
            $row[] = $transactions->AmtM09x;
            $row[] = $transactions->AmtM10x;
            $row[] = $transactions->AmtM11x;
            $row[] = $transactions->AmtM12x;
            $row[] = $transactions->AmtTotx;
            $data[] = $row;
        }
        $countFilter = $this->glverification_transactions->count_filtered($userid,$recongouptitle);
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" =>  $countFilter,// $this->glverification_transactions->count_all(),
            "recordsFiltered" =>  $countFilter,
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    /**
     * Get data verification for Dashboard
     **/
    public function getverification_dashboard(){
        $uidnumber = $this->session->userdata['userid'];

        $list = $this->general->get_Dashboard($uidnumber);
        $data = array();
        foreach ($list as $dashboard) {
            $row = array();
            $row[] = $dashboard->ReconGroupTitle;
            $row[] = $dashboard->TotalCompletedAmount;
            $row[] = $dashboard->TotalCompletedCount;
            $row[] = $dashboard->TotalSelectedAmount;
            $row[] = $dashboard->TotalSelectedCount;
            $row[] = $dashboard->TotalActivityAmount;
            $row[] = $dashboard->TotalActivityCount;
            $row[] = $dashboard->TotalNotVerifiedAmount;
            $row[] = $dashboard->TotalNotVerifiedCount;
            $row[] = $dashboard->TotalPendingAmount;
            $row[] = $dashboard->TotalPendingCount;
            $data[] = $row;
        }

        $output = array(
          
            "data" => $data,
        );
        
        //output to json format
        echo json_encode($output);
    }

    /**
     * Get Monthly Trend Percentage
     * */
    public function GetMonthlyTrendPercent(){
        //get monthlyTrend percentage
        $percentage= $this->glverification_report->GetMonthlyTrendPercent($this->session->userdata('fy'),$this->session->userdata('fp'),$this->session->userdata('deptid'));
        echo json_encode($percentage);
    }

    /**
     * Get Acknowleger Approve
     * */
    public function GetAcknowledgerApprove($deptid,$fy,$fp){
      
       $ack = $this->general->get_listdepartment($deptid,$fy,$fp);
       $listdeparment = $this->general->get_deptAllowed($this->session->userdata('userid'));
       $checkExistDept = strpos($listdeparment, $deptid) !== false ? true:false;
       if(!empty($ack)){
        $approve = array();
        foreach ($ack as $row)
        {
            if($this->session->userdata('authorized_role')=='Verifier' ){
               if($row->DeptCd==$deptid){
                if($row->DeptCd != $row->ReconDeptCd){
                    $approve='Disable';
                }else if($row->Checked==''){
                    $approve='Disable';
                }else{
                    $approve='EnableAcknowledged';
                }
                break;
            }
        }else if($this->session->userdata('authorized_role')=='Approver' && $checkExistDept==false){
            if($row->DeptCd==$deptid){
                if($row->DeptCd != $row->ReconDeptCd){
                    $approve='Disable';
                }else if($row->Checked==''){
                    $approve='Disable';
                }else{
                    $approve='EnableAcknowledged';
                }
                break;
            }
        }else {
            if($row->DeptCd==$deptid){
                if($row->DeptCd != $row->ReconDeptCd){
                    $approve='Disable';
                }else if($row->Checked==''){
                    $approve='EnableNotAcknowledged';
                }else{
                    $approve='EnableAcknowledged';
                }
                break; 
            }
        }
        
        
    }
}else{
    if($this->session->userdata('authorized_role')=='Verifier'){
        $approve='Disable';
    }else{
        $approve='EnableNotAcknowledged';
    }
    
}


return $approve;
}

     /**
     * Submit Acknowleger data
     * */
     public function Submit_AcknowledgedData()
     {
        log_message('info','User '.$this->session->userdata['userid'].' submitted Acknowledged Data');
        $fp = $this->general->getmonth_reportdate($this->input->post('reportdate'));
        $fy = $this->general->getyear_reportdate($this->input->post('reportdate'));
        $deptid = $this->input->post('deptid');
        $checkValue = $this->input->post('checked');

        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
        }else{
            $fp = $fp - 6;
            $fy = $fy + 1;
        }

        $data = $this->glverification->Submit_AcknowledgedData($deptid,$fy,$fp,$checkValue);
        echo json_encode($data);
    }

    /**
     * Get verification for detail items
     * */
    public function getverification_verifyglvitemdetail(){
        $deptid = $this->input->post('deptid');
        $bu = $this->input->post('busunit');
        $fy = $this->session->userdata['fy'];
        $fp = $this->session->userdata['fp'];
        $userId = $this->session->userdata['userid'];
        $reconitemcd = $this->input->post('reconitemcd');
        $reconstatuscd = $this->input->post('reconstatuscd');
        $recongrouptitle = $this->input->post('recongrouptitle');
        $priormonth = $this->input->post('priormonth');
        $filterName = $this->input->post('myfilter');
        $site = $this->glverification_filter->get_site_data_of_filter( $userId, $deptid,$filterName); 
        $start = $_POST["start"];
        $length = $_POST["length"];
        $total = 0;
        $siteStr = $site == "(any)"? "%":$site;
        $list = $this->glverification->get_VerifyGLVItemDetails($deptid,$bu,$fy,$fp,$reconitemcd,$reconstatuscd,$recongrouptitle,$priormonth,$start,$length,$filterName, $siteStr);
        $total = $this->glverification->count_GLVItemDetails($deptid,$bu,$fy,$fp,$reconitemcd,$reconstatuscd,$recongrouptitle,$priormonth,$filterName, $siteStr);

        $data = array();
        foreach ($list as $itemdetails) {
            $row = array();
            $row[] = $itemdetails->uniqueid;
            $row[] = $itemdetails->BusinessUnitCd;
            $row[] = $itemdetails->DeptCd;
            $row[] = $itemdetails->FundCd;
            $row[] = $itemdetails->ProjectCd;
            $row[] = $itemdetails->FunctionCd;
            $row[] = $itemdetails->AccountCd;
            $row[] = $itemdetails->ActivityCd;
            $row[] = $itemdetails->FlexCd;
            $row[] = $itemdetails->AccountMedCtrCd;
            $row[] = $itemdetails->Amount;
            $row[] = $itemdetails->ReconStatusCd;
            $row[] = $itemdetails->CommentGLVTypeId;
            $row[] = $itemdetails->JournalId;
            $row[] = $itemdetails->JournalPostDt;
            $row[] = $itemdetails->JournalLineRef;
            $row[] = $itemdetails->JournalLineDesc;
            $row[] = $itemdetails->JournalTitle;
            $row[] = $itemdetails->JournalSrcTitleCd;
            $row[] = $itemdetails->JournalOprDesc;
            $row[] = $itemdetails->ReconAssignDesc;
            $row[] = $itemdetails->ProjectUse;
            $row[] = $itemdetails->ProjectTitleCd;
            $row[] = $itemdetails->AccountTitleCd;
            $row[] = $itemdetails->user_name;
            $row[] = $itemdetails->ReconDate;
            $row[] = $itemdetails->ReconLink;
            $row[] = $itemdetails->InvoicePO;
            $row[] = $itemdetails->InvoiceId;
            $row[] = $itemdetails->InvoiceVoucherId;
            $row[] = $itemdetails->InvoiceDate;
            $row[] = $itemdetails->InvoiceReqDeptCd;
            $row[] = $itemdetails->InvoiceVendorName;
            $row[] = $itemdetails->InvoiceVendorCd;   
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" =>$total, //$this->getverification_dashboard->count_all(),
            "recordsFiltered" =>$total, //$this->getverification_dashboard->count_filtered(),
            "data" =>  $data,
        );
        //output to json format
        echo json_encode($output);
    }


     /**
     * Export GLV Item Details data to excel
     **/
    public function exportGLVItemDetails(){
        $deptid = $this->input->post('deptid');
        $bu = $this->input->post('busunit');
        $fy = $this->session->userdata['fy'];
        $fp = $this->session->userdata['fp'];
        $userId = $this->session->userdata['userid'];
        $reconitemcd = $this->input->post('reconitemcd');
        $reconstatuscd = $this->input->post('reconstatuscd');
        $recongrouptitle = $this->input->post('recongrouptitle');
        $priormonth = $this->input->post('priormonth');
        $filterName = $this->input->post('myfilter');
        $site = $this->glverification_filter->get_site_data_of_filter( $userId, $deptid,$filterName);        
        $siteStr = $site == "(any)"? "%":$site;
        $list = $this->glverification->get_VerifyGLVItemDetails_ToExport($deptid,$bu,$fy,$fp,$reconitemcd,$reconstatuscd,$recongrouptitle,$priormonth,$filterName, $siteStr);
       
        
        // load excel library
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);  

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'BU');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Dept ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Fund');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Project');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Funct');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Account');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Actvy');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Flex');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Amount');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Verification Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Verification Comments');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Jrnl ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Jrnl Post Dt');   
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Reference');
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Jrnl Line Desc');
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Jrnl Desc');
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Jrnl Opr Desc');
        $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'GLV Assign Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Account Title');
        $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Verifier');       
        $objPHPExcel->getActiveSheet()->SetCellValue('U1', 'Verification Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Attachments');
        $objPHPExcel->getActiveSheet()->SetCellValue('W1', 'PO');       
        $objPHPExcel->getActiveSheet()->SetCellValue('X1', 'Invoice');
        $objPHPExcel->getActiveSheet()->SetCellValue('Y1', 'Voucher');
        $objPHPExcel->getActiveSheet()->SetCellValue('Z1', 'Invoice Date');                    
        $objPHPExcel->getActiveSheet()->SetCellValue('AA1', 'Invoice Req Dept');
        $objPHPExcel->getActiveSheet()->SetCellValue('AB1','Vendor Name');                
        $objPHPExcel->getActiveSheet()->SetCellValue('AC1','Vendor No');                
        // set Row
        $rowCount = 2;
        foreach ($list as $item) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->BusinessUnitCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->DeptCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $item->FundCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $item->ProjectCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $item->FunctionCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $item->AccountCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $item->ActivityCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $item->FlexCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $item->Amount);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $item->ReconStatusCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, '');
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $item->JournalId);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $item->JournalPostDt);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $item->JournalLineRef);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $item->JournalLineDesc);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $item->JournalTitle);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $item->JournalOprDesc);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $item->ReconAssignDesc);
            $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $item->AccountTitleCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $item->user_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $item->ReconDate);
            $objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, '');
            $objPHPExcel->getActiveSheet()->SetCellValue('W' . $rowCount, $item->InvoicePO);
            $objPHPExcel->getActiveSheet()->SetCellValue('X' . $rowCount, $item->InvoiceId);
            $objPHPExcel->getActiveSheet()->SetCellValue('Y' . $rowCount, $item->InvoiceVoucherId);
            $objPHPExcel->getActiveSheet()->SetCellValue('Z' . $rowCount, $item->InvoiceDate);
            $objPHPExcel->getActiveSheet()->SetCellValue('AA' . $rowCount, $item->InvoiceReqDeptCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('AB' . $rowCount, $item->InvoiceVendorName);
            $objPHPExcel->getActiveSheet()->SetCellValue('AC' . $rowCount, $item->InvoiceVendorCd);
            $rowCount++;
        } 

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);        
        ob_start();
        $objWriter->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );

        die(json_encode($response));


       
    }
     /**
     * Get verification for detail items base on uniqueId
     * */
    public function getListDocuments(){
        $uniqueId = $this->test_input($_POST["uniqueid"]);
        $glvType = $this->test_input($_POST["glvType"]);
        $document_glvTypeId =  $this->test_input($_POST["document_glvtypeid"]);
        
        $list = $this->glverification_document->getListDocuments($uniqueId,$glvType,$document_glvTypeId);
        $data= array();
        if($list ){
            foreach ($list as $itemdetails) {
                $row = array();
                $row[] = $itemdetails->DocumentName;
                $row[] = $itemdetails->Id;
                $data[] = $row;
            }
            $output = array(
                "data" =>   $data,
            );
            
        } else{
            $output = array(
                "data" =>array(),
            );
        }
        echo json_encode($output);
    }

    /**
     * Update verification items
     * */
    public function update_verifyglvitems()
    {
        log_message('info','User '.$this->session->userdata['userid'].' update verify glvitems');
        $msg = "";
        $data = $_POST["data"];
        $currentServerDate = $this->general->getServerDate();
        $listRecords = array();
        foreach ($data as $item) {
            $array = array(
                "uniqueid" => $this->test_input($item["uniqueid"]),
                "ReconStatusCd" => $this->test_input($item["reconstatuscd"]),
              //  "RECON_Comment" => $this->test_input($item["reconcomment"]),
                "RECON_User" => $this->session->userdata['userid'],
                "RECON_Date" => $currentServerDate
            );
            $listRecords[] = $array;
        }
        $rs = $this->glverification->update_verifyGLVItemsDetails($listRecords);
        if ($rs)
            $msg = "success";
        else
            $msg = "error";
        $data = null;

        echo json_encode($msg);
    }

    /**
     * Format data
     * */
    public function test_input($data) 
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * Get verify data for payroll
     * */
    public function verify_payroll()
    {
        $deptCd = $fiscalYear = $fiscalMonth = $businessUnitCd = "";
        
        $deptCd = $this->test_input($_POST["deptId"]);
        $fy = $this->session->userdata['fy'];
        $fp = $this->session->userdata['fp'];
        $businessUnitCd = $this->test_input($_POST["businessUnit"]);
        $myFilter = $this->test_input($_POST["myfilter"]);      
        $userId = $this->session->userdata['userid'];        
        $site = $this->glverification_filter->get_site_data_of_filter( $userId, $deptCd,$myFilter); 
        $list = $this->glverification_payroll->get_verify_payroll($deptCd, $fy, $fp, $businessUnitCd,$site, $myFilter);
        
        $data = array();
        foreach ($list as $item) {
            $row = array();
            $row[] = $item->uniqueid;
            $row[] = $item->PositionTitleCategory;
            $row[] = $item->Employee_Id;
            $row[] = $item->Employee_name;
            $amountData =0;
            switch ($fp) {
                case '1':
                $amountData=$item->S01_Jul;
                break;
                case '2':
                $amountData=$item->S02_Aug;
                break;
                case '3':
                $amountData=$item->S03_Sep;
                break;
                case '4':
                $amountData=$item->S04_Oct;
                break;
                case '5':
                $amountData=$item->S05_Nov;
                break;
                case '6':
                $amountData=$item->S06_Dec;
                break;
                case '7':
                $amountData=$item->S07_Jan;
                break;
                case '8':
                $amountData=$item->S08_Feb;
                break;
                case '9':
                $amountData=$item->S09_Mar;
                break;
                case '10':
                $amountData=$item->S10_Apr;
                break;
                case '11':
                $amountData=$item->S11_May;
                break;
                case '12':
                $amountData=$item->S12_Jun;
                break;
                
                default:
                $amountData =0;
            }
            $row[] = $amountData;
            $row[] = $item->ReconStatusCd;
            $row[] = $item->CommentGLVTypeId;
            $row[] = $item->RECON_Link;
            $row[] = $item->DeptCd;
            $row[] = $item->FundCd;
            $row[] = $item->ProjectCd;
            $row[] = $item->FunctionCd;
            $row[] = $item->FlexCd;
            $row[] = $item->DeptSite;
            $row[] = $item->PlanTitleCdTitle;
            $row[] = $item->user_name;
            $row[] = $item->ReconDate;            
            $data[] = $row;
        }
        //$total = $this->glverification_payroll->count_all_verify_payroll($deptCd, $fy, $businessUnitCd);
        $output = array(
            "draw" => $_POST['draw'],
            //"recordsTotal" => $total,
            //"recordsFiltered" => $total,
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     * Get payroll FTE data
     * */
    public function payroll_fte()
    {
        $userId = $fiscalYear = "";
        
        $fy = $this->session->userdata['fy'];
        $userId = $this->session->userdata['userid'];

        $list = $this->glverification_payroll->get_list_category_sumary($userId, $fy);
        
        $data = array();
        foreach ($list as $item) {
            $row = array();
            $row[] = $item->PositionTitleCategory;
            $row[] = $item->FTEM01;
            $row[] = $item->FTEM02;
            $row[] = $item->FTEM03;
            $row[] = $item->FTEM04;
            $row[] = $item->FTEM05;
            $row[] = $item->FTEM06;
            $row[] = $item->FTEM07;
            $row[] = $item->FTEM08;
            $row[] = $item->FTEM09;
            $row[] = $item->FTEM10;
            $row[] = $item->FTEM11;
            $row[] = $item->FTEM12;
            $row[] = $item->SalM01;
            $row[] = $item->SalM02;
            $row[] = $item->SalM03;
            $row[] = $item->SalM04;
            $row[] = $item->SalM05;
            $row[] = $item->SalM06;
            $row[] = $item->SalM07;
            $row[] = $item->SalM08;
            $row[] = $item->SalM09;
            $row[] = $item->SalM10;
            $row[] = $item->SalM11;
            $row[] = $item->SalM12;
            $data[] = $row;
        }

        $output = array(
           // "draw" => $_POST['draw'],
            // "recordsTotal" => $this->glverification_payroll->count_all(),
            // "recordsFiltered" => $this->glverification_transactions->count_filtered($deptCd, $fiscalYear, $businessUnitCd),
            "data" => $data
        );

        echo json_encode($output);
    }

    /**
     * Get payroll expense data
     * */
    public function payroll_expense()
    {
        $userId = $emp_name = "";
        
        $userId = $this->session->userdata['userid'];
        $emp_name = $this->test_input($_POST["emp_name"]);
        $search_col = $this->test_input($_POST["search_col"]);
        $search_val  = $this->test_input($_POST["search_val"]);
        $column_expense_detail = array('uniqueid','PositionTitleCategory','Employee_Name','Employee_Id','RecType',
        'DeptCd','FundCd','ProjectCd','FunctionCd','FlexCd','PositionTitleCd','EmpChanged','M01','M02','M03');

        $start = $_POST["start"];
        $length = $_POST["length"];
        $order = $_POST["order"][0];
        $list = array();
        $total = 0;
        if ($emp_name != ""){
            $list = $this->glverification_payroll->get_expense_detail_with_empName($userId, $emp_name, $start, $length,$column_expense_detail[$order['column']],$order['dir'],$search_col,$search_val  );
            $total = $this->glverification_payroll->count_expense_detail_with_empName($userId, $emp_name,$search_col,$search_val  );
        } 
        else {
            $list = $this->glverification_payroll->get_all_expense_detail($userId, $start, $length,$column_expense_detail[$order['column']],$order['dir'],$search_col,$search_val  );  
            $total = $this->glverification_payroll->count_expense_detail($userId,$search_col,$search_val  );
        } 

        $data = array();
        foreach ($list as $item) {
            $row = array();
            $row[] = $item->uniqueid;
            $row[] = $item->PositionTitleCategory;
            $row[] = $item->Employee_Name;
            $row[] = $item->Employee_Id;
            $row[] = $item->RecType;
            $row[] = $item->DeptCd;
            $row[] = $item->FundCd;
            $row[] = $item->ProjectCd;
            $row[] = $item->FunctionCd;
            $row[] = $item->FlexCd;
            $row[] = $item->PositionTitleCd;
            $row[] = $item->EmpChanged;
            $row[] = $item->M01;
            $row[] = $item->M02;
            $row[] = $item->M03;
            // $row[] = $item->M04;
            // $row[] = $item->M05;
            // $row[] = $item->M06;
            // $row[] = $item->M07;
            // $row[] = $item->M08;
            // $row[] = $item->M09;
            // $row[] = $item->M10;
            // $row[] = $item->M11;
            // $row[] = $item->M12;
            $data[] = $row;
        }

        $output = array(
            "draw" => $search_col ." - ".$search_val,
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        );

        echo json_encode($output);
    }

     /**
     * Get payroll expense data to export
     * */
    public function export_payroll_expense()
    {
        $userId = $emp_name = "";
        
        $userId = $this->session->userdata['userid'];
        $emp_name = $this->test_input($_POST["emp_name"]);
        $changedEmp = ( $this->test_input($_POST["changedEmp"]) ) === 'true' ? true : false ;
        $listHeader = $this->input->post('listHeader');

        $list = array();
       
        if ($emp_name != ""){
            $list = $this->glverification_payroll->get_expense_detail_with_empName_ToExport($userId, $emp_name,$changedEmp );           
        } 
        else {
            $list = $this->glverification_payroll->get_all_expense_detail_ToExport($userId,$changedEmp);  
        } 

        // load excel library
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);  
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Category');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Emp Id');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Rec Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Dept ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Fund');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Project');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Funct');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Flex');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Pr Title');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Chg');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', $listHeader[0]);
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', $listHeader[1]);   
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', $listHeader[2]);                 
       // set Row
        $rowCount = 2;
        foreach ($list as $item) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->PositionTitleCategory);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->Employee_Name);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $item->Employee_Id);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $item->RecType);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $item->DeptCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $item->FundCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $item->ProjectCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $item->FunctionCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $item->FlexCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $item->PositionTitleCd);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $item->EmpChanged);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $item->M01);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $item->M02);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $item->M03);
            $rowCount++;
        } 

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);        
        ob_start();
        $objWriter->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );

        die(json_encode($response));
    }

    /**
     * Get list recon status data
     * */
    public function get_recon_status()
    {
        $output = $this->glverification_payroll->get_list_reconStatus();
        echo json_encode($output);
    }

    /**
     * Submit  GLV payroll data
     * */
    public function submit_data_glv_payroll() 
    {   
        log_message('info','User '.$this->session->userdata['userid'].' submmitted glv payroll data');
        $msg = "";
        $currentServerDate = $this->general->getServerDate();
        if(isset($_POST["data"])) {
            $data = $_POST["data"];
            $listRecords = array();
            foreach ($data as $item) {
                $array = array(
                    "uniqueid" => $this->test_input($item["Id"]),
                    "ReconStatusCd" => $this->test_input($item["Status"]),
                  //  "ReconComment" => $this->test_input($item["Comment"]),
                    "ReconUser" => $this->session->userdata['userid'],
                    "ReconDate" => $currentServerDate
                );
                $listRecords[] = $array;
            }

            $rs = $this->glverification_payroll->update_comment_status_in_reconEmployeeGLV($listRecords);
            if ($rs) 
                $msg = "Payroll has been updated successfully.";
            else 
                $msg = "Failed to update payroll.";
            $data = null;
        }
        else
            $msg = "No change has been made.";

        echo json_encode($msg);
    }

    /**
     * Get url report
     * Get url report for current verification system base on data that was submitted
     * */
    public function GetUrlReport()
    {
        $deptId = $fiscalYear = $businessUnitCd = "";
        $deptId = $this->test_input($_POST["deptId"]);
        $fy = $this->session->userdata['fy'];
        $fp=  $this->session->userdata['fp'];
        //convert back time
        $logfy= $logfp = "";
        if($fp>6){
            $logfy = $fy;
            $logfp = $fp-6;
        } else{
            $logfy = $fy-1;
            $logfp = $fp+6;  
        }
        log_message('info','User '.$this->session->userdata['userid'].' view monthly Trend Report for DetpCd: '. $deptId.', on report date: '.$logfp.'-'.$logfy);
        $reportDateString = $fp >= 7 ? $fy.'0701-'.($fy+1).'0630': ($fy-1).'0701-'.$fy.'0630' ;
        
        $site = $this->test_input($_POST['site']);

        $depLevel1Cd = $this->glverification_report->GetDeptLevel1Cd($deptId);
        $businessUnitCd = $this->test_input($_POST["businessUnit"]);
        /* depending on $depLevel1Cd we have different url reports  */
        // if ($depLevel1Cd != '100000'){
            
        //     $url = $this->glverification_report-> GetUrlReport_MyReport($businessUnitCd,$depLevel1Cd,$deptId,$reportDateString);
        // }else{
        //     $url = $this->glverification_report-> GetUrlReport_SOM($deptId,$site,$fy,$businessUnitCd,$depLevel1Cd);
        // }
        $url = $this->glverification_report-> GetUrlReport_MyReport($businessUnitCd,$depLevel1Cd,$deptId,$reportDateString);
        echo json_encode($url);

    }

    /** Monthly Trend Change
     * Submit data when monthly trend dropdown change status and save button was clicked
     * */
    function MonthlyTrendChange(){
        $fy = $this->session->userdata['fy'];
        $fp = $this->session->userdata['fp'];
        $deptId='';
        $deptId = $this->test_input($_POST["deptId"]);
        $flag=0;
        $trendStatus =$_POST["trendStatus"];
        //convert back time
        $logfy= $logfp = "";
        if($fp>6){
            $logfy = $fy;
            $logfp = $fp-6;
        } else{
            $logfy = $fy-1;
            $logfp = $fp+6;  
        }

        log_message('info','User '.$this->session->userdata['userid'].' save monthly status report to '.$trendStatus.' on report date '.$logfp.'-'.$logfy);
        /* depend on the status of monthly trend we will send diffrent value the the store sp_GLV_Approve */
        if ($trendStatus == "Not Verified"){
            $flag = 0;
        }else{
            $flag = 1;
        }
        $monthlyChange = $this->glverification_report->MonthlyTrendChange($fy,$fp,$deptId, $flag);
        echo json_encode($monthlyChange);
    }

    /*Filter*/
    public function get_default_deptId() 
    {
        $rs = $this->general->getdefaultdeptid($this->session->userdata['userid']);
        if($rs){
            echo json_encode($rs);
        } else{
            echo json_encode(null);
        }
       
    }

    /**
     * Get projectMgrCd data for dropdown in filter default of current user
     * */
    public function get_filter_data_ddl() 
    {
        $userId = $this->session->userdata['userid'];
        $deptId = $this->test_input($_POST["deptId"]);
        $filterId = $this->test_input($_POST["filterId"]);

        $list = array();
        $rs = $this->glverification_filter->get_data_for_ddl_filter($userId, $deptId, $filterId);
        if($rs){
            foreach ($rs as $item) {
                if ($item->ChartStrField != "DeptCdSaved") {
                    $pro_mgr_id = "";
                    $not_value = "false";
                    if ($item->ChartStrField == "ProjectManagerCd") {
                        $pro_mgr_id = $this->glverification_filter->get_projectMgrCd_by_projecMgr($item->ChartStrValue);
                        $pro_mgr_id = $pro_mgr_id[0]->ProjectManagerCd;
                    }
                    if ($item->Except == "-")
                        $not_value = "true";
                    
                    $arr = array(
                        "Type" => $item->ChartStrField,
                        "Value" => $item->ChartStrValue,
                        "not_value" => $not_value,
                        "ProMgrId" => $pro_mgr_id
                    );
                    $list[] = $arr;
                }
            }
            
            echo json_encode($list);
        } else{
            echo json_encode(null);
        }
       
    }

    /**
     * Get list filter name 
     * */
    public function get_list_filters() 
    {
        $userId = $this->session->userdata['userid'];
        $deptId = $this->test_input($_POST["deptId"]);

        $rs = $this->glverification_filter->get_list_filters($userId, $deptId);
        if($rs){
            echo json_encode($rs);
        }else{
            echo json_encode(null);
        }
    }

    /**
     * Get list projectCd by deptId
     * */
    public function get_filters_site() 
    {
        $userId = $this->session->userdata['userid'];
        $deptId = $this->test_input($_POST["deptId"]);
        $filterId = $this->test_input($_POST["filterId"]);

        $rs = $this->glverification_filter->get_site_data_of_filter($userId, $deptId, $filterId);
        if($rs){
            echo json_encode($rs);
        }else{
            echo json_encode(null);
        }
    }

    /**
     * Get list projectCd by deptId
     * */
    public function get_filters_projectCd() 
    {
        $deptId = $this->test_input($_POST["deptId"]);

        $rs = $this->glverification_filter->get_list_projectCd($deptId);
        if($rs){
            echo json_encode($rs);
        }else{
            echo json_encode(null);
        }
    }

    /**
     * Get list funcCd
     * */
    public function get_filters_funcCd() 
    {
        $rs = $this->glverification_filter->get_list_funcCd();
        if($rs){
            echo json_encode($rs);
        }else{
            echo json_encode(null);
        }
     
    }

    /**
     * Get list projectMgr by deptId
     * */
    public function get_filters_projectMgr() 
    {
        $deptId = $this->test_input($_POST["deptId"]);

        $rs = $this->glverification_filter->get_list_projectMgr($deptId);
        if($rs){
            echo json_encode($rs);
        }else{
            echo json_encode(null);
        }
       
    }

    /**
     * Get list ProjectUseTitle
     * */
    public function get_filters_projectUse() 
    {
        $rs = $this->glverification_filter->get_list_projectUse();
        if($rs){
            echo json_encode($rs);
        }else{
            echo json_encode(null);
        }
    }

    /**
     * Delete Filter
     * */
    public function delete_filters() 
    {
     
        $userId = $this->session->userdata['userid'];
        $deptId = $this->test_input($_POST["deptId"]);
        $filterId = $this->test_input($_POST["filterName"]);
        log_message('info','User '.$this->session->userdata['userid'].' delete filter '.$filterId);
        $rs = $this->glverification_filter->delete_filters_saved($userId,$deptId,$filterId);
        $mes = $rs ?  $filterId . " has been deleted." : "Failed to delete " . $filterId . ".";
        echo json_encode($mes);
    }

    /**
     * Save Filter data
     * */
    public function save_filters() 
    {
        
        $userId = $this->session->userdata['userid'];
        $deptId = $this->test_input($_POST["deptId"]);
        $filterId = $this->test_input($_POST["filterName"]);
        $type = $this->test_input($_POST["type"]);
        log_message('info','User '.$this->session->userdata['userid'].' save filters '.$filterId);
        $listRecords = array();
        $listRecords[] = array(
            "UserId" => $userId,
            "DeptCdSaved" => $deptId,
            "FilterName" => $filterId,
            "ChartStrField" => "DeptCdSaved",
            "ChartStrValue" => $deptId,
            "Except" => "+"
        );
        if ($type == "save_as") {
            $listRecords[] = array(
                "UserId" => $userId,
                "DeptCdSaved" => $deptId,
                "FilterName" => "(working)",
                "ChartStrField" => "DeptCdSaved",
                "ChartStrValue" => $deptId,
                "Except" => "+"
            );
        }
        if(isset($_POST["listItems"])) {
            $listItems = $_POST["listItems"];
            foreach ($listItems as $item) {
                $array = array(
                    "UserId" => $userId,
                    "DeptCdSaved" => $deptId,
                    "FilterName" => $filterId,
                    "ChartStrField" => $this->test_input($item["Type"]),
                    "ChartStrValue" => $this->test_input($item["Value"]),
                    "Except" => $this->test_input($item["Except"])
                );
                $listRecords[] = $array;
            }
        }
        $rs = true;
        if ($type == "save")
            $rs = $this->glverification_filter->save_filter($userId,$deptId,$filterId,$listRecords);
        else if($type == "save_as")
            $rs = $this->glverification_filter->save_as_filter($userId,$deptId,$filterId,$listRecords);
        
        $mes = $rs ? $filterId ." has been saved."  :  $filterId. " has not been saved." ;

        echo json_encode($mes);    
    }

    /**
     * Check duplicate filter name
     * */
    public function check_duplicate_filterName() 
    {
        $userId = $this->session->userdata['userid'];
        $deptId = $this->test_input($_POST["deptId"]);
        $filterId = $this->test_input($_POST["filterName"]);
        $rs = $this->glverification_filter->check_duplicate_filterName($userId,$deptId,$filterId);
        echo json_encode($rs);
    }
    
    /**
     * Get current depCd information include postinging level, checked status
     * */
    function GetDeptCdInformation(){
        $deptInfo = $this->general->GetDeptCdInformation($this->session->userdata('deptid'),$this->session->userdata('fy'), $this->session->userdata('fp'));
        echo json_encode($deptInfo);
    }

    /**
     * Upload documents
     * */
    public function uploadFiles()
    {
         //save file in GLVLink fields
        $uniqueid = $_POST['uniqueid'];
        $glvType =  $this->test_input($_POST['glvType']);
        //if $document_glvTypeId = '' then no existing document
        $document_glvTypeId = $this->test_input($_POST['document_glvtypeid']);

        $config["upload_path"] = UPLOAD_PATH.$glvType.'/'.$uniqueid.'/' ;
        if (!file_exists( $config["upload_path"])) {
            mkdir( $config["upload_path"], 0777, true);
        }
        $config["allowed_types"] = ALLOW_UPLOAD_FILETYPE;
        $config['max_filename'] = '255';
        $config['encrypt_name'] = FALSE;
        $config['max_size'] = '1024'; //1 MB
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $filesStr = "";
        $documentNameArr = array();
        $output="";
        //check if any document have the same name with existing documents
        if( $document_glvTypeId != ''){
            $checkExistDocument = $this->glverification_document->checkIfDocumentExist($uniqueid,$_FILES["files"]["name"],$document_glvTypeId,$glvType);
            if($checkExistDocument){
                echo json_encode(array(
                    "msg" => "File \"".(implode(", ",$checkExistDocument))."\" existed. Please choose another files.",
                    "document_glvtypeid" => $document_glvTypeId
                )); 
               exit(1);
            }
        }
       

        for($count = 0; $count<count($_FILES["files"]["name"]); $count++)
        {
            $_FILES["file"]["name"] = rawurlencode(str_replace(' ','_',$_FILES["files"]["name"][$count])) ;
            $_FILES["file"]["type"] = $_FILES["files"]["type"][$count];
            $_FILES["file"]["tmp_name"] = $_FILES["files"]["tmp_name"][$count];
            $_FILES["file"]["error"] = $_FILES["files"]["error"][$count];
            $_FILES["file"]["size"] = $_FILES["files"]["size"][$count];
            if (file_exists( $config["upload_path"]. $_FILES["file"]["name"])) {
                unlink($config["upload_path"]. $_FILES["file"]["name"]);
            }
            if($this->upload->do_upload('file'))
            {
                $data = $this->upload->data();
                $documentNameArr[] =  ($data['file_name']);
                $filesStr= $filesStr==""?  $filesStr.$data['file_name']:",".$filesStr.$data['file_name'];
                
            } else{
                $documentNameArr[] =   $_FILES["file"]["name"];
                echo json_encode(array(
                    "msg" => "Failed to upload file \"". rawurldecode(implode(", ",$documentNameArr))."\"",
                    "document_glvtypeid" => '',
                    "status" => false
                ));
                exit(1);
            }
        }
        $date = $this->general->getServerDate();
        $rs = '';
        if( $document_glvTypeId == ''){
            $rs = $this->glverification_document->addNewDocuments($uniqueid,$documentNameArr,$date,$glvType);
            if (!$rs) {
                echo json_encode(array(
                     "msg" => "Failed to upload file '". rawurldecode(implode(", ",$documentNameArr))."'",
                    "document_glvtypeid" => '',
                    "status" => false
                ));
           } else{
               echo json_encode(array(
                   "msg" =>"File \"". rawurldecode(implode(", ",$documentNameArr)). "\" has been uploaded successfully.",
                    "document_glvtypeid" => $rs,
                    "status" => true
               )); 
           }
        } else {
            $isAdd = $this->glverification_document->addAdditionalDocuments($documentNameArr,$date,$document_glvTypeId);
            if (!$isAdd) {
                echo json_encode(array(
                     "msg" => "Failed to upload file '". rawurldecode(implode(", ",$documentNameArr))."'",
                     "document_glvtypeid" => $document_glvTypeId,
                     "status" => false
                ));
           } else{
               echo json_encode(array(
                   "msg" =>"File \"" . rawurldecode(implode(", ",$documentNameArr)). "\" has been uploaded successfully.",
                    "document_glvtypeid" => $document_glvTypeId,
                    "status" => true
               )); 
           }
        }
    }

     /**
     * delete document
     * */
    public function deleteDocument(){
        $documentid = $this->test_input($_POST['documentid']);
        $document_glvtypeid = $this->test_input($_POST["document_glvtypeid"]);
        $documentname = $this->test_input($_POST["documentname"]);
        $glvType = $this->test_input($_POST['glvtype']);
        $uniqueId = $this->test_input($_POST["uniqueid"]);
        $path = UPLOAD_PATH. $glvType.'/'.$uniqueId.'/'.$documentname ;
        unlink( $path); 
        $res = $this->glverification_document->deleteDocument( $documentid, $document_glvtypeid,$glvType,$uniqueId );
        echo $res;
    }

    /**
     * get all comments 
     * */
    public function getComments(){
        $uniqueId = $this->test_input($_POST['uniqueId']);
        $comment_glvtype = $this->test_input($_POST["comment_glvtype"]);
        $commentType= $this->test_input($_POST['commentType']);
        $list = $this->glverification_payroll->getComments( $comment_glvtype, $uniqueId,$commentType);
        if($list) {
            $output = array(
                "data" => $list,
            );
            echo json_encode($output);
        } else {
            $output = array(
                "data" => array(),
            );
            echo json_encode( $output );
        }
    }
    /**
     * update  comments 
     * */
    public function updateComments(){
        $comment = $this->test_input($_POST['comment']);
        $commentId = $this->test_input($_POST["commentId"]);
        $date = $this->general->getServerDate();
        $result = $this->glverification_payroll->updateComments( $comment, $commentId,$date);
        echo json_encode($result);
    }
    
    /**
     * add additional  comments 
     * */
    public function addAdditionalComments(){
        $comment = $this->test_input($_POST['comment']);
        $userId = $this->session->userdata['userid'];
        $commentUserId =  $this->test_input($_POST['commentUserId']);
        $date = $this->general->getServerDate();
        $result = $this->glverification_payroll->addAdditionalComments( $comment, $userId,$date,$commentUserId);
        echo json_encode($result);
    }

    /**
     * add new  comments 
     * */
    public function addNewComment(){
        $uniqueId = $this->test_input($_POST['uniqueId']);
        $comment = $this->test_input($_POST['comment']);
        $userId = $this->session->userdata['userid'];
        $commentType = $this->test_input($_POST['commentType']);
        $date = $this->general->getServerDate();
        $result = $this->glverification_payroll->addNewComment( $uniqueId,$comment, $userId,$date,$commentType);
        echo json_encode($result);
    }



}
