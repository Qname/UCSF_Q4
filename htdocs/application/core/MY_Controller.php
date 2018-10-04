<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH . "third_party/MX/Controller.php";

/**
 * Description of my_controller
 *
 * @author http://roytuts.com
 */
class MY_Controller extends MX_Controller {

    function __construct() {
        parent::__construct();
        if (version_compare(CI_VERSION, '2.1.0', '<')) {
            $this->load->library('security');
        }
    }

    //set the class variable.
    var $template  = array();
    var $data      = array();
    //Load layout
    public function layout() {
        // making temlate and send data to view.
        $this->template['header']   = $this->load->view('layout/header', $this->data, true);
        $this->template['left']   = $this->load->view('layout/left', $this->data, true);
        $this->template['middle'] = $this->load->view($this->middle, $this->data, true);
        $this->template['footer'] = $this->load->view('layout/footer', $this->data, true);
        $this->load->view('layout/index', $this->template);
    }

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */