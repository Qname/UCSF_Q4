
<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GLVsetting Class
 *
 * @package     Manage User
 */
class GLVsetting extends MY_Controller {
    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct() {
        parent::__construct();
        $this->load->model('GLVsetting_model','GLVsetting');
        $this->load->helper('function');
        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }

    /**
	 * Index Page for this controller.
	 *
     * @return	void
	 */
    public function index() {
        // If user not login then redirect to login page
        if (isset($this->session->userdata['logged_in']))
        {
            // If user have not permission then redirect to home page
            if ($this->session->userdata['authorized_role']=="Sysadmin")
            {
                $this->load->helper('url');
                $upload_setting = $this->GLVsetting->get_upload_setting();
                $temp['upload_setting'] = $upload_setting;
                $temp['title']="UCSF GLV Settings";
                $temp['template']='index';
                $this->load->view("shared/layout",$temp);
            }
            else
            {
                redirect('/glvhome');
            }
        }
        else
        {
            redirect('/login');
        }
    }

    /**
	 * Save user
	 *
	 * @return	void
	 */
    public function save() {
        $size = $this->input->post('valueSize');
        echo $this->GLVsetting->update_size_upload($size);
    }


    /**
     *Get Size Upload
     *
     * @return  void
     */
    public function getSizeUpload() {
        $upload_setting= $this->GLVsetting->get_upload_setting();
        echo $upload_setting['ValueSize'];
    }



    
}
