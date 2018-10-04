<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SOM BFA User preferences model
 */
class Som_bfa_userpreferences_model extends CI_Model {
    var $table = 'SOM_BFA_UserPreferences';

    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
	 * Get preferences by user ID
     * 
	 * @param	string  $userId    Id of user
	 * @return	array
	 */
    public function get_prefernce_by_userId($userId)
    {
        try {
            $this->db->select("Preference,String");
            $this->db->from($this->table);
            $this->db->where('UserId',$userId);

            $query = $this->db->get();
            log_message('info',"get_prefernce_by_userId SQL= " . $this->db->last_query());            
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_prefernce_by_userId: ".$e->getMessage());
            return "";
        }
    }

    /**
	 * Get preference by user ID and Preferences
     * 
	 * @param	string  $userId         Id of user
     * @param	string  $preferences    Preferences
	 * @return	array
	 */
    public function get_prefernce_by_userId_and_pre($userId, $preferences)
    {
        try {
            $this->db->select("String");
            $this->db->from($this->table);
            $this->db->where("UserId",$userId);
            $this->db->where("Preference",$preferences);
            $query = $this->db->get();
            log_message('info',"get_prefernce_by_userId_and_pre SQL= " . $this->db->last_query());            
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_prefernce_by_userId_and_pre: ".$e->getMessage());
            return "";
        }
    }

    /**
	 * Insert user preference
     * 
	 * @param	array  $data    Data input
	 * @return	bool
	 */
    public function insert_userPreferences($data) 
    {
        try {
            if ($this->db->insert($this->table, $data)){
               log_message('info',"insert_userPreferences SQL= " . $this->db->last_query());           

               return true;
           }
           else
            return false;
    }
    catch(Exception $e){
        log_message('error',"insert_userPreferences: ".$e->getMessage());
        return false;
    }
}

    /**
	 * Update user preference
     * 
	 * @param	array  $data    Data input
     * @param	array  $where   Condition
	 * @return	bool
	 */
    public function update_userPrefences($data, $where) 
    {
        try {
            if ($this->db->update($this->table, $data, $where)){
               log_message('info',"update_userPrefences SQL= " . $this->db->last_query());           
               
               return true;
           }
           else
            return false;
    }
    catch(Exception $e){
        log_message('error',"update_userPrefences: ".$e->getMessage());
        return false;
    }
}
}
?>