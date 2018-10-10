<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Monthly_trend_data_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

     /**
     * getMonthlyTrendDataId
     * */
     public function getMonthlyTrendDataId($fy, $fp,$businessUnitCd,$site,$deptId) 
     {
        try {
            $monthlyTrendDataId= 0;
            
            $query = $this->db->query(" select Id from MonthlyTrendData where fy = ? and fp = ? and BussinessCd = ? and [site] = ? and DeptId = ?",array($fy, $fp,$businessUnitCd,$site,$deptId));
            if($query){
                $data = $query->result();
                if(count($data)>0){
                    $monthlyTrendDataId= $data[0]->Id;
                }else {
                    $this->db->query('INSERT INTO MonthlyTrendData(Fy,Fp,BussinessCd,Site,DeptId) VALUES( ? , ?,?,?,?);', array($fy, $fp,$businessUnitCd,$site,$deptId));
                    $monthlyTrendDataId = $this->db->insert_id();
                }
            }

            
            log_message('info',"getMonthlyTrendDataId SQL= " . $this->db->last_query());  
            return $monthlyTrendDataId;
        }
        catch(Exception $e){
            log_message('error',"getMonthlyTrendDataId: ".$e->getMessage());
            return null;
        }
    }


    /**
     * getGLVComentTypeIdForMonthlyTrendDataId
     * */
     public function getGLVComentTypeIdForMonthlyTrendDataId($id) 
     {
        try {
            $GLVComentTypeId= "";
            
            $query = $this->db->query(" select Id from Comment_GlvType where UniqueId = ? and CommentType = 'MonthlyTrend' ",array($id));
            if($query){
                $data = $query->result();
                if(count($data)>0){
                    $GLVComentTypeId= $data[0]->Id;
                }
            }

            
            log_message('info',"getGLVComentTypeIdForMonthlyTrendDataId SQL= " . $this->db->last_query());  
            return $GLVComentTypeId;
        }
        catch(Exception $e){
            log_message('error',"getGLVComentTypeIdForMonthlyTrendDataId: ".$e->getMessage());
            return null;
        }
    }


     /**
     * getGLVComentTypeIdForMonthlyTrendDataId
     * */
     public function checkExistedCommentForMonthlyTrendDataId($id) 
     {
        try {
            $bool= false;
            
            $query = $this->db->query(" select Id from Comment where Comment_GlvType = ? ",array($id));
            if($query){
                $data = $query->result();
                if(count($data)>0){
                    $bool= true;
                }
            }

            
            log_message('info',"checkExistedCommentForMonthlyTrendDataId SQL= " . $this->db->last_query());  
            return $bool;
        }
        catch(Exception $e){
            log_message('error',"checkExistedCommentForMonthlyTrendDataId: ".$e->getMessage());
            return null;
        }
    }


}