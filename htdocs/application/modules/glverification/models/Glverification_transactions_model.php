<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Glverification_transactions_model extends CI_Model {

    var $table = 'SOM_AA_TransactionSummary';
    var $column = array('ReconItemCd','ReconGroupTitle','ReconItemTitle','NotVerified','Pending','Complete', 'AutoComplete','PriorNotVerified','PriorPending','AmtM01x','AmtM02x','AmtM03x','AmtM04x','AmtM05x','AmtM06x','AmtM07x','AmtM08x','AmtM09x','AmtM10x','AmtM11x','AmtM12x','AmtTotx'); //set column field database for datatable searchable
    var $order = array("Sort1" => "asc", "Sort2" => "asc");

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get datatable query
     * */
    private function _get_datatables_query($userid,$recongouptitle)
    {
        try {
            if ($recongouptitle === NULL || $recongouptitle=="Total") {
                $this->db->select('ReconItemCd,ReconGroupTitle,ReconItemTitle,NotVerified,Pending,Complete, AutoComplete,PriorNotVerified,PriorPending,AmtM01x,AmtM02x,AmtM03x,AmtM04x,AmtM05x,AmtM06x,AmtM07x,AmtM08x,AmtM09x,AmtM10x,AmtM11x,AmtM12x,AmtTotx');
                $this->db->from('SOM_AA_TransactionSummary');   
                $this->db->where("SessionUserid",$userid);         
            }else{
                $this->db->select('ReconItemCd,ReconGroupTitle,ReconItemTitle,NotVerified,Pending,Complete, AutoComplete,PriorNotVerified,PriorPending,AmtM01x,AmtM02x,AmtM03x,AmtM04x,AmtM05x,AmtM06x,AmtM07x,AmtM08x,AmtM09x,AmtM10x,AmtM11x,AmtM12x,AmtTotx');
                $this->db->from('SOM_AA_TransactionSummary'); 
                $this->db->where("SessionUserid",$userid);
                $this->db->where("ReconGroupTitle", $recongouptitle);                       
            }
            $this->db->order_by('Sort1 asc, Sort2 asc');    

        }
        catch(Exception $e){
            log_message('error',"_get_datatables_query: ".$e->getMessage());
            return "";
        }    
    }

    /**
     * Get datatable for transaction
     * */
    function get_datatables($userid,$recongouptitle)
    {
        try {
            $this->_get_datatables_query($userid,$recongouptitle);
            if($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            log_message('info',"get_datatables SQL= " . $this->db->last_query());
            return $query->result();
        }
        catch(Exception $e){
            log_message('error',"get_datatables: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Count data filtered
     * */
    function count_filtered($userid,$recongouptitle)
    {
        try {
            $this->_get_datatables_query($userid,$recongouptitle);
            $query = $this->db->get();
            log_message('info',"count_filtered SQL= " . $this->db->last_query());

            return $query->num_rows();
        }
        catch(Exception $e){
            log_message('error',"count_filtered: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Count all data
     * */
    public function count_all()
    {
        try {
            $this->db->from($this->table);
            log_message('info',"count_all SQL= " . $this->db->last_query());
            return $this->db->count_all_results();
        }
        catch(Exception $e){
            log_message('error',"count_all: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get data by id
     * */
    public function get_by_id($id)
    {
        try {
            $this->db->from($this->table);
            $this->db->where('id',$id);
            $query = $this->db->get();
            log_message('info',"get_by_id SQL= " . $this->db->last_query());
            return $query->row();
        }
        catch(Exception $e){
            log_message('error',"get_by_id: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Save data
     * */
    public function save($data)
    {
        try {
            $this->db->insert($this->table, $data);
            log_message('info',"save SQL= " . $this->db->last_query());

            return $this->db->insert_id();
        }
        catch(Exception $e){
            log_message('error',"save: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Update data
     * */
    public function update($where, $data)
    {
        try {
            $this->db->update($this->table, $data, $where);
            log_message('info',"update SQL= " . $this->db->last_query());
            return $this->db->affected_rows();
        }
        catch(Exception $e){
            log_message('error',"update: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Delete data by id
     * */
    public function delete_by_id($id)
    {
        try {
            $this->db->where('id', $id);
            $this->db->delete($this->table);
            log_message('info',"delete_by_id SQL= " . $this->db->last_query());

        }
        catch(Exception $e){
            log_message('error',"delete_by_id: ".$e->getMessage());
            return "";
        }
    }

    /**
     * Funtion get data for tab review and verify transaction
     */
    public function get_ReviewVerifyTransactions($userid,$recongouptitle, $start, $length)
    {
        try {
            if ($recongouptitle === NULL || $recongouptitle=="Total") {
                $this->db->select("ReconItemCd,ReconGroupTitle,ReconItemTitle,NotVerified,Pending,Complete, AutoComplete,PriorNotVerified,PriorPending,NotVerifiedCount,PendingCount,CompleteCount,AutoCompleteCount,PriorNotVerifiedCount,PriorPendingCount,AmtM01x,AmtM02x,AmtM03x,AmtM04x,AmtM05x,AmtM06x,AmtM07x,AmtM08x,AmtM09x,AmtM10x,AmtM11x,AmtM12x,AmtTotx");
                $this->db->from("SOM_AA_TransactionSummary");
                $this->db->where("SessionUserid",$userid);
              //  $this->db->where("Sort1 != 'UnCoded'");
                
            }else{
                $this->db->select("ReconItemCd,ReconGroupTitle,ReconItemTitle,NotVerified,Pending,Complete, AutoComplete,PriorNotVerified,PriorPending,NotVerifiedCount,PendingCount,CompleteCount,AutoCompleteCount,PriorNotVerifiedCount,PriorPendingCount,AmtM01x,AmtM02x,AmtM03x,AmtM04x,AmtM05x,AmtM06x,AmtM07x,AmtM08x,AmtM09x,AmtM10x,AmtM11x,AmtM12x,AmtTotx");
                $this->db->from("SOM_AA_TransactionSummary");
                $this->db->where("SessionUserid",$userid);
                $this->db->where("ReconGroupTitle",$recongouptitle);
               // $this->db->where("Sort1 != 'UnCoded'");          
            }
            if ($length != 0)
                $this->db->limit($length,$start);
            $this->db->order_by("Sort1 asc, Sort2 asc");           
            $query = $this->db->get();
            log_message('info',"get_ReviewVerifyTransactions SQL= " . $this->db->last_query());
            
            return $query->result();   
        }
        catch(Exception $e){
            log_message('error',"get_ReviewVerifyTransactions: ".$e->getMessage());
            return "";
        }   
    }
}