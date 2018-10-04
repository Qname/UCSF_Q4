<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Usersmanagement data model
 */
class Usersmanagement_model extends CI_Model {
	public $table = 'lkp_userprofile';
    public $primary_key = 'id';
    
    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
	 * Get list of user
	 *
	 * @return	array
	 */
    public function get_list_users()
    {
        try {
            $query = $this->db->query("SELECT distinct lkp_userprofile.id, lkp_userprofile.user_id, lkp_userprofile.user_name,lkp_userprofile.nameLast, lkp_userprofile.nameFirst, lkp_userprofile.email, lkp_userprofile.departmentname, lkp_userprofile.createdate, lkp_user_roles.authorized_role
                FROM lkp_userprofile 
                INNER JOIN lkp_user_roles 
                ON lkp_userprofile.user_id = lkp_user_roles.user_id 
                ORDER BY createdate DESC");
            $data = $query->result();
            log_message('info',"get_list_users SQL= " . $this->db->last_query());            
            return $data;
        }
        catch(Exception $e){
            log_message('error',"get_list_users " . $e->getMessage());
            return "";
        }
        
    }

    /**
	 * Get user by user id
     * 
	 * @param	string  $id    Id of user
	 * @return	array
	 */
    public function get_user_byid($id)
    {
        try {
            $query = $this->db->query("SELECT distinct lkp_userprofile.id, lkp_userprofile.user_id, lkp_userprofile.user_name,lkp_userprofile.nameLast, lkp_userprofile.nameFirst, lkp_userprofile.email, lkp_userprofile.departmentname, lkp_userprofile.createdate, lkp_user_roles.authorized_role,lkp_userprofile.allowDeptId
                FROM lkp_userprofile 
                INNER JOIN lkp_user_roles 
                ON lkp_userprofile.user_id = lkp_user_roles.user_id 
                WHERE lkp_userprofile.id = ?", array($id));
            log_message('info',"get_user_byid SQL= " . $this->db->last_query());            
            return $query->row_array();
        }
        catch(Exception $e){
            log_message('error',"get_user_byid " . $e->getMessage());
            return "";
        }
    }

    /**
	 * Get list of role
     * 
	 * @return	array
	 */
    public function get_list_roles()
    {
        try {
            $query = $this->db->query("SELECT id, role_name 
                FROM lkp_roles 
                ORDER BY role_name asc");
            $data = $query->result();
            log_message('info',"get_list_roles SQL= " . $this->db->last_query());            
            return $data;
        }
        catch(Exception $e){
            log_message('error',"get_list_roles " . $e->getMessage());
            return "";
        }
    }

    /**
	 * Check if user exist
	 *
	 * @param	string  $id         New user id
     * @param	string  $user_id    Old user id
	 * @return	int                 Number of row
	 */
    function check_unique_user_id($id = '', $user_id) {
        try {
            $this->db->where('user_id', $user_id);
            if ($id) 
            {
                $this->db->where_not_in('id', $id);
            }
            $data= $this->db->get('lkp_userprofile')->num_rows();
            log_message('info',"check_unique_user_id SQL= " . $this->db->last_query());   
            return $data;         
        }
        catch(Exception $e){
            log_message('error',"check_unique_user_id " . $e->getMessage());
            return 0;
        }
    }

    /**
	 * Check if username exist
	 *
	 * @param	string  $id         Id of user
     * @param	string  $user_name  Username of user
	 * @return	int                 Number of row
	 */
    function check_unique_user_name($id = '', $user_name) {
        try {
            $this->db->where('user_name', $user_name);
            if ($id) 
            {
                $this->db->where_not_in('id', $id);
            }
            $data= $this->db->get('lkp_userprofile')->num_rows();
            log_message('info',"check_unique_user_name SQL= " . $this->db->last_query());   
            return $data;
        }
        catch(Exception $e){
            log_message('error',"check_unique_user_name " . $e->getMessage());
            return 0;
        } 
    }

    /**
	 * Check if useremail exist
	 *
	 * @param	string  $id     Id of user
     * @param	string  $email  Email of user
	 * @return	int             Number of row
	 */
    function check_unique_user_email($id = '', $email) {
        try {
            $this->db->where('email', $email);
            if ($id) 
            {
                $this->db->where_not_in('id', $id);
            }
            $data= $this->db->get('lkp_userprofile')->num_rows();
            log_message('info',"check_unique_user_email SQL= " . $this->db->last_query()); 
            return $data;  
        }
        catch(Exception $e){
            log_message('error',"check_unique_user_email " . $e->getMessage());
            return 0;
        }
    }

    /**
	 * Update user
	 *
	 * @param	string  $id     Id of user
	 * @return	string
	 */
    public function update_user($id = 0)
    {
        $this->load->helper('url', 'date');
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'user_name' => $this->input->post('user_name'),
            'nameLast' => $this->input->post('nameLast'),
            'nameFirst' => $this->input->post('nameFirst'),
            'email' => $this->input->post('email'),
            'departmentname' => $this->input->post('departmentname'),
            'allowDeptId' => $this->input->post('allowDeptId')
        );
        if ($id == 0) 
        {
        	$data['createdate'] = date('Y-m-d H:i:s',time());            
            $data_role = array(
            	'user_id' => $this->input->post('user_id'),
            	'authorized_role' => $this->input->post('authorized_role'),
            	'createdate' => date('Y-m-d H:i:s',time())
            );
            try {
                if ($this->db->insert('lkp_userprofile', $data) && $this->db->insert('lkp_user_roles', $data_role)){
                   log_message('info',"update_user SQL= " . $this->db->last_query()); 
                   return $result['Code'] = "success";
               }
               else
                return $result['Code'] = "error";
        }
        catch(Exception $e){
            log_message('error',"update_user " . $e->getMessage());
            return $result['Code'] = "error";
        }
    } 
    else 
    {
        try {
            $this->db->where('id', $id);
            $this->db->update('lkp_userprofile', $data);
            $data_role = array(
                'user_id' => $this->input->post('user_id'),
                'authorized_role' => $this->input->post('authorized_role'),
            );
            $this->db->where('user_id', $this->input->post('user_id_old'));
            if ($this->db->update('lkp_user_roles', $data_role)){
               log_message('info',"update_user SQL= " . $this->db->last_query()); 
               return $result['Code'] = "success";
           }
           else
            return $result['Code'] = "error";
    }
    catch(Exception $e){
        log_message('error',"update_user " . $e->getMessage());
        return $result['Code'] = "error";
    } 
}
}

    /**
	 * Delete user
	 *
	 * @param	string  $id         Id of user in table lkp_userprofile
     * @param	string  $user_id    Id of user in table lkp_user_roles
	 * @return	void
	 */
    public function delete_user($id,$user_id="") {
        try {
            $this->db->where('user_id', $user_id);
            $this->db->delete('lkp_user_roles');
            log_message('info',"delete_user SQL= " . $this->db->last_query()); 
            $this->db->where('id', $id);
            $this->db->delete('lkp_userprofile');
            log_message('info',"delete_user SQL= " . $this->db->last_query()); 
        }
        catch(Exception $e){
            log_message('error',"delete_user " . $e->getMessage());
        }
        
    }

    /**
     * Check exist department in Department allowed of User
     *
     * @param   string  $listDeptIds         List department data
     * @param   int  $deptAmount    Number of department
     * @return  string
     */
    public function checkExistInDeptAllowed($listDeptIds,$deptAmount) {
        try {
            $this->db->where_in('DEPT_CD', $listDeptIds);
            $this->db->from('COA_SF_DEPDCH');
            $data= $this->db->count_all_results();
            log_message('info',"checkExistInDeptAllowed SQL= " . $this->db->last_query()); 
            if((int)$data==$deptAmount){
                return true;
            }else{

                return $this->getListValidDeptFromList($listDeptIds);
            }
        }
        catch(Exception $e){
            log_message('error',"checkExistInDeptAllowed " . $e->getMessage());
        }
        
    }

     /**
     * Get list department ID valid in department list
     *
     * @param   string  $listDeptIds         List department data
     * @return  string
     */
     public function getListValidDeptFromList($listDeptIds) {
        try {
            $this->db->select('DEPT_CD');
            $this->db->from("COA_SF_DEPDCH");
            $this->db->where_in('DEPT_CD', $listDeptIds);

            $data = $this->db->get()->result();
            log_message('info',"getListValidDeptFromList SQL= " . $this->db->last_query()); 
            
            
            for($i=0;$i<count($data);$i++){
                $listDeptData.= "".$data[$i]->DEPT_CD.",";
            }
            return $listDeptData;
        }
        catch(Exception $e){
            log_message('error',"getListValidDeptFromList " . $e->getMessage());
        }
        
    }

}