
<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Usersmanagement Class
 *
 * @package     Manage User
 */
class Usersmanagement extends MY_Controller {
    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct() {
        parent::__construct();
        $this->load->model('usersmanagement_model','usersmanagement');
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
                add_js(array('usersmanagement.js'));
                $this->load->helper('url');
                $list_users = $this->usersmanagement->get_list_users();
                $temp['users'] = $list_users;
                $temp['title']="UCSF GL System Administrator";
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
	 * List all user of the system
     * 
     * @return	void
	 */
    public function list_users() {
        $this->load->helper('url');
        $users = $this->usersmanagement->get_list_users();
        $temp['users'] = $users;
        $this->load->view("list_users",$temp);
    }

    /**
	 * Add new user
     * 
     * @return	void
	 */
    public function add() {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $data['roles'] = $this->usersmanagement->get_list_roles();
        $this->load->view('add_user',$data);
    }

    /**
	 * Update user
	 *
	 * @param	string  $user_id    Id of user
	 * @return	void
	 */

    public function edit($user_id) {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        if ($this->form_validation->is_natural_no_zero($user_id)===FALSE) 
        {
          redirect('usersmanagement');
      }
      $data['user_item'] = $this->usersmanagement->get_user_byid($user_id);
      $data['roles'] = $this->usersmanagement->get_list_roles();
      $this->load->view('edit_user',$data);
  }

    /**
	 * Check if userid exist
	 *
	 * @param	string  $user_id    Id of user
	 * @return	bool
	 */
    public function check_user_id($user_id) {        
        if ($this->input->post('id'))
            $id = $this->input->post('id');
        else
            $id = '';
        $result = $this->usersmanagement->check_unique_user_id($id, $user_id);
        if ($result == 0)
            $response = true;
        else 
        {
            $this->form_validation->set_message('check_user_id', 'UCSF ID must be unique');
            $response = false;
        }
        return $response;
    }

    /**
	 * Check if username exist
	 *
	 * @param	string  $user_name  Name of user
	 * @return	bool
	 */
    public function check_user_name($user_name) {        
        if ($this->input->post('id'))
            $id = $this->input->post('id');
        else
            $id = '';
        $result = $this->usersmanagement->check_unique_user_name($id, $user_name);
        if ($result == 0)
            $response = true;
        else 
        {
            $this->form_validation->set_message('check_user_name', 'Account Name must be unique');
            $response = false;
        }
        return $response;
    }

    /**
	 * Check if email user exist
	 *
	 * @param	string  $email  Email of user
	 * @return	bool
	 */
    public function check_user_email($email) {        
        if ($this->input->post('id'))
            $id = $this->input->post('id');
        else
            $id = '';
        $result = $this->usersmanagement->check_unique_user_email($id, $email);
        if ($result == 0)
            $response = true;
        else 
        {
            $this->form_validation->set_message('check_user_email', 'Email must be unique');
            $response = false;
        }
        return $response;
    }

    /**
	 * Save user
	 *
	 * @return	void
	 */
    public function save() {
        if (!$this->input->is_ajax_request()) 
        { 
            echo 'no valid request.';
        }
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('user_id','UCSF id','trim|required|callback_check_user_id', array('required' => 'UCSF ID field is required.'));
        $this->form_validation->set_rules('user_name','Account Name','trim|required|callback_check_user_name', array('required' => 'Account Name field is required.'));
        
        if ($this->input->post('email')) 
        {
            $this->form_validation->set_rules('email','Email','trim|valid_email|callback_check_user_email', array('valid_email' => 'Email field must contain a valid email address.'));    
        }
        else 
        {
            $this->form_validation->set_rules('email','Email','trim|valid_email', array('valid_email' => 'Email field must contain a valid email address.'));
        }
        $this->form_validation->set_rules('authorized_role','Role','trim|required', array('required' => 'Role field is required.'));

        if ($this->form_validation->run()===FALSE) 
        {
            echo json_encode($this->form_validation->error_array());
        }
        else 
        {
            if ($this->input->post('id')) 
            {
                $result = $this->usersmanagement->update_user($this->input->post('id'));
                log_message('info', 'User '.$this->session->userdata['userid'].' edit user with id: ' . $this->input->post('id'));
            }
            else 
            {
                $result = $this->usersmanagement->update_user();
                log_message('info', 'User '.$this->session->userdata['userid'].' add new user');
            }
            echo json_encode($result);
        }
    }

    /**
	 * Delete user
	 *
	 * @return	void
	 */
    public function delete() {
        $id = $this->input->post('id');
        $user_id = $this->input->post('user_id');
        if ($id) 
        {
            $this->usersmanagement->delete_user($id, $user_id);
            log_message('info', 'User '.$this->session->userdata['userid'].' delete user with id: ' . $id);
        }
    }

    /**
     * Check exist department in Department allowed of User
     *
     * @return  bool
     */
    public function checkDeptAllowed() {
        $listDept = $this->input->post('listDeptIds');
        $deptAmount = $this->input->post('deptAmount');
        echo $this->usersmanagement->checkExistInDeptAllowed($listDept, $deptAmount);
    }
}
