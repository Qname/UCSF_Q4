<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('account_model','account');
        $this->load->model('General_model','general');
        $this->load->helper('function');
        $this -> load -> library('form_validation');
    }

    public function index()
    {
        $this->load->view('account/login');
    }
    // route /login
    public function login()
    {
        if (isset($this->session->userdata['userid']))
        {
            redirect('/glvhome');

        }else{
            // $uidnumber  = $_SERVER['uidNumber'];//"ucsfmanager@gmail.com";
            $uidnumber  = "ucsfmanager@gmail.com";//$_SERVER['uidNumber'];//"ucsfmanager@gmail.com";//
            //log login information
            $this->general->LogActivityInfo('login','login successfully','', $uidnumber );
            if( isset($_SERVER['sAMAccountName']) )
            {
                $uusername  = $_SERVER['sAMAccountName'];
            }else{
                $uusername="";
            }
            if( isset($_SERVER['surName']) )
            {
                $lastname  = $_SERVER['surName'];
            }else{
                $lastname="";
            }
            if( isset($_SERVER['givenName']) )
            {
                $firstname  = $_SERVER['givenName'];
            }else{$firstname="";}

            // $email = $_SERVER['email']; //"ucsfmanager@gmail.com"; 
            $email ="ucsfmanager@gmail.com";
            if( isset($_SERVER['ucsfEduWorkingDepartmentName']) )
            {
                $departmentname  = $_SERVER['ucsfEduWorkingDepartmentName'];
            }else{
                $departmentname="";
            }
            if($uidnumber !=""){
                if ($user = $this->account->getusers_by_uidnumber($uidnumber)) {
                    $sesiondata = array(
                        'userid' => $uidnumber,
                        'authorized_role'  =>$this->account->get_userrole($uidnumber),
                        'email'     => $email,
                        'logged_in' => TRUE
                    );
                    $this->session->set_userdata($sesiondata);
                    redirect('/glvhome');
                } else {
                    //add user
                    $adduserdata = array(
                        "user_id" => $uidnumber,
                        "user_name" => $uusername,
                        "nameLast" => $lastname,
                        "nameFirst" => $firstname,
                        "email"=>$email,
                        "departmentname"=>$departmentname,
                        "createdate" => date('Y-m-d H:i:s')
                    );

                    if ($this->account->add_user($adduserdata,"Verifier")){
                        $sesiondata = array(
                            'userid' => $uidnumber,
                            'authorized_role'  =>"Verifier",
                            'email'     => $email,
                            'logged_in' => TRUE
                        );
                        $this->session->set_userdata($sesiondata);
                    }
                    redirect('/glvhome');
                }

            }else{
                redirect('/securityalert');
            }
        }
    }

    // route /login    
    public function logout()
    { 
    	if($this->session->userdata['userid']){
            $this->general->LogActivityInfo('logout','logout successfully','', $this->session->userdata['userid'] );
        }
	$this->session->unset_userdata('userid');
	$this->session->unset_userdata('authorized_role');
	$this->session->unset_userdata('email');
	$this->session->unset_userdata('logged_in');
	$this->session->sess_destroy();     		   
        //redirect('/account/login');
		 if (isset($this->session->userdata['userid'])==false)
        {

            redirect('/Shibboleth.sso/Logout');    
        }		    
    }
}
