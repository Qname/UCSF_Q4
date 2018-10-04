<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GLVHome Class
 */
class Glvhome extends MY_Controller {

    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vw_coa_som_departments_model','coa_departments');
        $this->load->model('Som_bfa_userpreferences_model','preferences');
        $this->load->model('Vw_get_deparments_model','depart');
        $this->load->helper('function'); 
        ini_set( 'memory_limit', '2400M' );
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288');
    }

    /**
	 * Index Page for this controller.
	 *
     * @return	void
	 */
    public function index()
    {
        add_js(array('GLVHome.js'));
        if (isset($this->session->userdata['userid']))
        {
            //list control point
            $controlPoints = $this->coa_departments->get_list_control_point();
            $controlPointDefault = 0;

            //get control-point AND roll-up is saved of user
            $deptId = $controlPoints[0]->DeptCd;
            $bfaPrefetences = $this->preferences->get_prefernce_by_userId($this->session->userdata['userid']);
            
            if (sizeof($bfaPrefetences) > 0) 
            {
                foreach ($bfaPrefetences as $item) 
                {
                    if ($item->Preference == "Default Deptid") 
                    {
                        $deptId = $item->String;
                    }
                    if ($item->Preference == "Default ControlPoint") 
                    {
                        $controlPointDefault = $item->String;
                    }
                }
            }
            else 
            {
                $controlPointDefault = $deptId;
            }
            
            //list roll-up
            $rollUps = $this->depart->get_list_rollup_by_controlPoint($controlPointDefault);

            //save to tempe -> parse view
            $this->load->helper('url');
            $temp['listControlPoints'] = $controlPoints;
            $temp['controlPointDefault'] = $controlPointDefault;
            $temp['listRollUps'] = $rollUps;
            $temp['rollUpDefault'] = $deptId;
            $temp['title']="UCSF";
            $temp['template']='index';
            $this->load->view("shared/layout",$temp);
        }
        else
        {
            redirect('/login');
        }
    }

    /**
	 * Format for input data
	 *
     * @param	string  $data
     * @return	string
	 */
    public function test_input($data) 
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
	 * Get roll up data
	 *
     * @return	json
	 */
    public function get_rollUps() 
    {
        $data = $this->depart->get_list_rollup_by_controlPoint($this->test_input($_POST["deptCd"]));
        echo json_encode($data);
    }

    /**
	 * Get roll up data by id of department Cd
	 *
     * @return	json
	 */
    public function get_rollUp_with_deptId() 
    {
        $deptId = $_POST["deptCd"];
        $data = "";
        if ($deptId != "") 
        {
            $data = $this->depart->get_list_rollup_by_deptId($this->test_input($_POST["deptCd"]));
        }
        echo json_encode($data);
    }

    /**
	 * Insert user preference
	 *
     * @param	string  $userId
     * @param	string  $preference
     * @param	string  $deptId
     * @return	bool
	 */
    function insert_data_user_preferences($userId, $preference, $deptId) 
    {
        $data = array(
            'UserId'=> $userId,
            'Preference' => $preference,
            'String' =>  $deptId
        );
        return $this->preferences->insert_userPreferences($data);
    }

    /**
	 * Update user preference
	 *
     * @param	string  $userId
     * @param	string  $preference
     * @param	string  $deptId
     * @return	bool
	 */
    function update_data_user_preferences($userId, $preference, $deptId) 
    {
        $data = array(
            'String' =>  $deptId
        );
        $where = array(
            'UserId' =>  $userId,
            'Preference' => $preference
        );
        return $this->preferences->update_userPrefences($data,$where);
    }

    /**
	 * Save user preference as default
	 *
     * @return	json
	 */
    public function save_as_default() 
    {
        $controlPoint = $rollUp = $deptId = "";
        $controlPoint = $this->test_input($_POST["controlPoint"]);
        $rollUp = $this->test_input($_POST["rollUp"]);
        
        $PreferenceDeptId = "Default Deptid";
        $preferenceCP = "Default ControlPoint";
        $userId = $this->session->userdata['userid'];
        $rsDept = $rsCP = false;
        log_message('info','User '.$this->session->userdata['userid'].' save new default. Control point: '.$controlPoint.'. Roll up: '. $rollUp);
        $deptIdDb = $this->preferences->get_prefernce_by_userId_and_pre($userId, $PreferenceDeptId);
        $controlPointDb = $this->preferences->get_prefernce_by_userId_and_pre($userId, $preferenceCP);

        if (count($deptIdDb) > 0) 
            $rsDept = $this->update_data_user_preferences($userId, $PreferenceDeptId, $rollUp);
        else 
            $rsDept = $this->insert_data_user_preferences($userId, $PreferenceDeptId, $rollUp);

        if (count($controlPointDb) > 0) 
            $rsCP = $this->update_data_user_preferences($userId, $preferenceCP, $controlPoint);
        else 
            $rsCP = $this->insert_data_user_preferences($userId, $preferenceCP, $controlPoint);
        
        if ($rsCP && $rsDept) 
            echo json_encode(true);
        else 
            echo json_encode(false);
    }

    /**
	 * Set data for redirect verification
	 *
     * @return	json
	 */
    public function redirect_verification() {
        $controlPoint = $rollUp = $deptId = "";
        $controlPoint = $this->test_input($_POST["controlPoint"]);
        $rollUp = $this->test_input($_POST["rollUp"]);
        $deptId = $this->test_input($_POST["deptId"]);
        $data['deptId']=$deptId;

        echo json_encode($data);
    }
}
