<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Account data model
 */
class Account_model extends CI_Model {
    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
	 * Get user information by id of user
	 *
     * @param	string  $uidnumber    Id of user
	 * @return	array
	 */
    function getusers_by_uidnumber($uidnumber) {
        $this->db->select('*');
        $this->db->from('lkp_userprofile');
        $this->db->where('user_id', $uidnumber);
        try {
            $query = $this->db->get();
            log_message('info',"getusers_by_uidnumber SQL= " . $this->db->last_query());            
            
            if ($query->num_rows() > 0)
            {
                return $query->row_array();
            }
            else 
            {
                return false;
            }
        }
        catch(Exception $e){
            log_message('error',"getusers_by_uidnumber " . $e->getMessage());
            return false;
        }
    }

    /**
	 * Get role of user by id of user
	 *
     * @param	string  $uidnumber    Id of user
	 * @return	array
	 */
    public function get_userrole($uidnumber)
    {
        try {
            $query = $this->db->query("SELECT authorized_role 
                FROM lkp_user_roles 
                WHERE user_id= ?",array($uidnumber));
            $ret = $query->row();
            log_message('info',"get_userrole SQL= " . $this->db->last_query());            
            return $ret->authorized_role;
        }
        catch(Exception $e){
            log_message('error',"get_userrole " . $e->getMessage());
            return "";
        }
        
    }

    /**
	 * Add user
	 *
     * @param	array   $data   User information
     * @param	string  $role   Role of user
	 * @return	array
	 */
    function add_user($data,$role)
    {
        try {
            if ($this->db->insert('lkp_userprofile', $data))
            {
                $addrole = array(
                    "user_id" => $data['user_id'],
                    "authorized_role" => $role,
                    "createdate" =>date('Y-m-d H:i:s')
                );
                $this->db->insert('lkp_user_roles', $addrole);
                log_message('info',"add_user SQL= " . $this->db->last_query());            
                return true;
            }
            return false;
        }
        catch(Exception $e){
            log_message('error',"add_user " . $e->getMessage());
            return false;
        }
        
    }

    /**
	 * Login into system
	 *
     * @param	array   $data   User information
	 * @return	array
	 */
    public function login($data) {
        try {
            $this->db->select('*');
            $this->db->from('lkp_userprofile');
            $this->db->where('email', $data['email']);
            $this->db->where('password', $data['password']);

            $this->db->limit(1);
            $query = $this->db->get();
            log_message('info',"login SQL= " . $this->db->last_query());            
            
            if ($query->num_rows() == 1) 
            {
                return true;
            } 
            else 
            {
                return false;
            }
        }
        catch(Exception $e){
            log_message('error',"login " . $e->getMessage());
            return false;
        }
        
    }
}