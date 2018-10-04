
<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GLVHome Controller
 */
class Compliance extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_model','general');
        $this->load->model('Dashboard_compliance_model','dashboard_compliance');
        $this->load->helper('function');
        $this -> load -> library('form_validation');
    }

    public function index()
    {
        add_js(array('compliance.js'));
        if (isset($this->session->userdata['userid']))
        {
            ini_set( 'memory_limit', '200M' );
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);

            //list control point
            $defaultreportdate="";
            $controlPoints = $this->dashboard_compliance->get_list_control_point();
            if (isset($this->session->userdata['fp']) and isset($this->session->userdata['fy'])) {
                $fp =$this->session->userdata['fp'];
                $fy =$this->session->userdata['fy'];
                if ($fp < 7) {
                    $defaultreportdate=($this->general->getmonth_name(($fp+12)-6))." ".($fy-1);
                }else{
                    $defaultreportdate=($this->general->getmonth_name(($fp)-6))." ".$fy;
                }
            }else{
                $fp = $this->general->GetFPFYdefault("DefaultFPMax");
                $fy = $this->general->GetFPFYdefault("DefaultFY");
                if ($fp<7) {
                    $fp=$fp+6;
                    $fy=$fy-1;
                }else{
                    $fp=$fp-6;
                    $fy=$fy;
                }
                $defaultreportdate=$this->general->getmonth_name($fp)." ".$fy;
            }
            $this->load->helper('url');
            $data['bu'] = isset($this->session->userdata['bu'])?  $this->session->userdata['bu'] : "SFCMP";
            $data['listControlPoints'] = $controlPoints;
            $data['controlPointDefault'] = 999999;
            $data['defaultreportdate'] = $defaultreportdate;
            $data['title']="UCSF GL Compliance Dashboard";
            $data['template']='index';
            $this->load->view("shared/layout",$data);

        }else{
            redirect('/login');
        }
    }

    /** Get list compliance dashboard * */
    public function ajax_listcompliance_dashboard()
    {
        $uid = $this->session->userdata['userid'];
        $fp = $this->general->getmonth_reportdate($this->input->post('reportdate'));
        $fy = $this->general->getyear_reportdate($this->input->post('reportdate'));
        $deptid = $this->input->post('deptid');
        $bu = $this->input->post('busunit');
        $this->session->set_userdata('bu', $bu);
        log_message('info','User '.$this->session->userdata['userid'].' Load table compliance. Department id: '.$deptid);
        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
        }else{
            $fp = $fp - 6;
            $fy = $fy + 1;
        }
        $this->session->set_userdata('fp', $fp);
        $this->session->set_userdata('fy', $fy);

        $list = $this->dashboard_compliance->get_compliancedashboard($fy,$fp,$deptid,$bu,'%');
        $data = array();
        foreach ($list as $dashboard) {
            // check result not like  'Medical Center Main' and 'not coded'
            if ($dashboard->DeptTitle !='Medical Center Main' && !strpos($dashboard->DeptTitle, 'not coded')) {
                $row = array();
                $row[] = $dashboard->DeptTitle;
                $row[] = $dashboard->Complete;
                $row[] = $dashboard->NotVerified;
                $row[] = $dashboard->Pending;
                $row[] = $dashboard->Selected;
                $row[] = $dashboard->DeptCd;
                $data[] = $row;
            }
        }

        $output = array(
            //"draw" => $_POST['draw'],
            //"recordsTotal" => $this->glverification_transactions->count_all(),
            //"recordsFiltered" => $this->glverification_transactions->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    /** Get list compliance dashboard status report * */
    public function ajax_listcompliance_statusreport()
    {
        $uid = $this->session->userdata['userid'];
        $fp = $this->general->getmonth_reportdate($this->input->post('reportdate'));
        $fy = $this->general->getyear_reportdate($this->input->post('reportdate'));
        $deptid = $this->input->post('deptid');
        $bu = $this->input->post('busunit');

        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
        }else{
            $fp = $fp - 6;
            $fy = $fy + 1;
        }

        $list = $this->dashboard_compliance->get_compliancedashboard($fy,$fp,$deptid,$bu,'%');
        $data = array();
        foreach ($list as $dashboard) {
            // check result not like  'Medical Center Main' and 'not coded'
            if ($dashboard->DeptTitle !='Medical Center Main' && !strpos($dashboard->DeptTitle, 'not coded')) {
                $row = array();
                $row[] = $dashboard->DeptTitle;
                $row[] = $dashboard->Complete;
                $row[] = $dashboard->NotVerified;
                $row[] = $dashboard->Selected;
                $row[] = $dashboard->Pending;
                $row[] = $dashboard->DeptCd;
                $data[] = $row;
            }
        }

        $output = array(
            //"draw" => $_POST['draw'],
            //"recordsTotal" => $this->glverification_transactions->count_all(),
            //"recordsFiltered" => $this->glverification_transactions->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    /** Get list compliance dashboard detail report * */
    public function ajax_listcompliance_detailreport()
    {
        $uid = $this->session->userdata['userid'];
        $fp = $this->general->getmonth_reportdate($this->input->post('reportdate'));
        $fy = $this->general->getyear_reportdate($this->input->post('reportdate'));
        $deptid = $this->input->post('deptid');
        $bu = $this->input->post('busunit');

        if ($fp < 7) {
            $fp = $fp + 6;
            $fy = $fy;
        }else{
            $fp = $fp - 6;
            $fy = $fy + 1;
        }

        $list = $this->dashboard_compliance->get_compliance_detailreport($fy,$fp,$deptid,$bu,'%');

        $totalcomplete=0;
        $totalnotverified=0;
        $totalselected=0;
        if (count($list)>0){
            if ($deptid=='999999') {
                foreach ($list as $result) {
                        // check result not like  'Medical Center Main' and 'not coded'
                    if ($result['DeptTitle'] !='Medical Center Main'  && !strpos($result['DeptTitle'], 'not coded')) {
                        $row = array();
                        $row[] = $result['DeptTitle'];
                        $row[] = $result['Complete'];
                        $row[] = $result['NotVerified'];
                        $row[] = $result['Selected'];
                        $row[] = $result['Pending'];
                        $row[] = $result['DeptCd'];
                        $row[] = "level1";
                        $data[] = $row;

                        $totalcomplete=0;
                        $totalnotverified=0;
                        $totalselected=0;
                        $totalpending=0;
                        if (is_array($result['artwork'])) {
                            foreach ($result['artwork'] as $car) {
                                $row = array();
                                $row[] = $car['DeptTitle'];
                                $row[] = $car['Complete'];
                                $row[] = $car['NotVerified'];
                                $row[] = $car['Selected'];
                                $row[] = $car['Pending'];
                                $row[] = $car['DeptCd'];
                                $row[] = "level2";
                                $data[] = $row;

                                $totalcomplete = $totalcomplete + $car['Complete'];
                                $totalnotverified = $totalnotverified + $car['NotVerified'];
                                $totalselected = $totalselected + $car['Selected'];
                                $totalpending=$totalpending +$car['Pending'];
                            }
                        }
                        $row = array();
                        $row[] = $result['DeptTitle'];
                        $row[] = $totalcomplete;
                        $row[] = $totalnotverified;
                        $row[] = $totalselected;
                        $row[] = $totalpending;
                        $row[] = "";
                        $row[] = "Total";
                        $data[] = $row;
                    }
                }
            }else{
                $row = array();
                $row[] = $this->general->GetDepartmentsById($deptid);
                $row[] = "";
                $row[] = "";
                $row[] = "";
                $row[] = "";
                $row[] = $deptid;
                $row[] = "level1";
                $data[] = $row;

                $totalcomplete=0;
                $totalnotverified=0;
                $totalselected=0;
                $totalpending=0;

                foreach ($list as $result) {
                    $row = array();
                    $row[] = $result['DeptTitle'];
                    $row[] = $result['Complete'];
                    $row[] = $result['NotVerified'];
                    $row[] = $result['Selected'];
                    $row[] = $result['Pending'];
                    $row[] = $result['DeptCd'];
                    $row[] = "level2";
                    $data[] = $row;

                    $totalcomplete=$totalcomplete +$result['Complete'];
                    $totalnotverified=$totalnotverified +$result['NotVerified'];
                    $totalselected=$totalselected +$result['Selected'];
                    $totalpending=$totalpending +$result['Pending'];
                }
                $row = array();
                $row[] = $this->general->GetDepartmentsById($deptid);
                $row[] = $totalcomplete;
                $row[] = $totalnotverified;
                $row[] = $totalselected;
                $row[] = $totalpending;
                $row[] = "";
                $row[] = "Total";
                $data[] = $row;
            }

            $output = array(
                    //"draw" => $_POST['draw'],
                    //"recordsTotal" => $this->glverification_transactions->count_all(),
                    //"recordsFiltered" => $this->glverification_transactions->count_filtered(),
                "data" => $data
            );
            
        } else {
            
           $output = array(
                    //"draw" => $_POST['draw'],
                    //"recordsTotal" => $this->glverification_transactions->count_all(),
                    //"recordsFiltered" => $this->glverification_transactions->count_filtered(),
            "data" => ""
        );
           
       }
         //output to json format
       echo json_encode($output);
   }

}
