<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Glvsetting data model
 */
class Glvsetting_model extends CI_Model {
	public $table = 'GLVSetting_Upload';
    public $primary_key = 'Id';
    
    /**
	 * Constructor
	 *
	 * @return	void
	 */
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

   

    public function get_upload_setting()
    {
        try {
            $query = $this->db->query(" select ValueSize from GLVSetting_Upload where UploadType = 'FileUpload' ");
            log_message('info',"get_upload_size SQL= " . $this->db->last_query());            
            return $query->row_array();
        }
        catch(Exception $e){
            log_message('error',"get_upload_size " . $e->getMessage());
            return "";
        }
    }

    /**
	 * Update size upload
	 *
	 * @param	string  $id     Id of user
	 * @return	string
	 */
    public function update_size_upload($size=0)
    {
        try {
            $data = array(
            'ValueSize' => $size
             );
            $this->db->where('UploadType', 'FileUpload');
            $this->db->update($this->table, $data);
            log_message('info',"update SQL= " . $this->db->last_query());
            return $this->db->affected_rows();
        }
        catch(Exception $e){
            log_message('error',"update: ".$e->getMessage());
            return "";
        }
    }

    

}