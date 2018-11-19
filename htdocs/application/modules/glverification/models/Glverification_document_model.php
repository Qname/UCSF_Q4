<?php
/**
 * PHP version 5.6
 * @author     Mobileanywhere <mobileanywhere.io>
 * @copyright  Copyright Mobileanywhere LLC 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Glverification_document_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Add new documents ( 1 or mutiple new document)
     * */
    public function addNewDocuments($uniqueId,$documentNames,$date,$glvType) 
    {
        try {
        $documentName = $documentNames[0];
        $document_glvTypeId= "";
        $this->db->trans_start();
        $this->db->query('INSERT INTO Document_GlvType(UniqueId,GlvType) VALUES( ? , ?);', array($uniqueId,$glvType));
        $document_glvTypeId = $this->db->insert_id();
        $this->db->query('insert into Document(DocumentName,Document_GlvTypeId,CreatedDate)
        values( ? , ? , ?  );', array($documentName,$document_glvTypeId,$date));

        if($glvType == TRANSACTION_TYPE){
            $this->db->query("Update COA_SOM_LedgerData set RECON_Link = ? where uniqueid = ?", array($document_glvTypeId, $uniqueId));
        } else{
            $this->db->query("Update SOM_BFA_ReconEmployeeGLV set RECON_Link = ? where uniqueid = ?", array($document_glvTypeId, $uniqueId));
        }

        //if user upload multiple document so we need to add it
        if(($numberOfNewDocument = count($documentNames)) > 1){
            for($i = 1; $i < $numberOfNewDocument;$i++){
                $this->db->query('insert into Document(DocumentName,Document_GlvTypeId,CreatedDate)
                values ( ? , ? , ?  );', array($documentNames[$i],$document_glvTypeId,$date));
            }
        }
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
        }
        else
        {
            $this->db->trans_commit();
        }
            log_message('info',"addNewDocument SQL= " . $this->db->last_query());  
          return $document_glvTypeId;
        }
        catch(Exception $e){
            log_message('error',"addNewDocument: ".$e->getMessage());
            return null;
        }
    }

     /**
     * Add additional documents 
     * */
    public function addAdditionalDocuments($documentNames,$date,$document_GlvTypeId) 
    {
        try {
            $data = array();
            foreach($documentNames as $documentName){
                $commentItem =  array(
                    'DocumentName' => $documentName,
                    'CreatedDate'  => $date,
                    'Document_GlvTypeId' =>$document_GlvTypeId
                );
                $data[] = $commentItem;
            }
            $this->db->insert_batch('Document', $data);
            log_message('info',"addAdditionalDocuments SQL= " . $this->db->last_query()); 
            return true;
        }
        catch(Exception $e){
            log_message('error',"addAdditionalDocuments: ".$e->getMessage());
            return false;
        }
    }

     /**
     * Check if document exist 
     * */
    public function checkIfDocumentExist($uniqueId,$documentNames,$document_GlvTypeId,$glvType) 
    {
        try {
            if($glvType == TRANSACTION_TYPE){
                $query = $this->db->query("select  Document.DocumentName from COA_SOM_LedgerData 
                inner join Document_GlvType on Document_GlvType.GlvType = ?
                and Document_GlvType.Id = COA_SOM_LedgerData.RECON_Link 
                inner join Document on
                Document.Document_GlvTypeId = Document_GlvType.Id
                and COA_SOM_LedgerData.uniqueid = ? and  Document.Document_GlvTypeId = ? ",array($glvType,$uniqueId,$document_GlvTypeId));
                $data = $query->result();
            } else{ // if payroll
                $query = $this->db->query("select  Document.DocumentName from SOM_BFA_ReconEmployeeGLV 
                inner join Document_GlvType on Document_GlvType.GlvType = ?
                and Document_GlvType.Id = SOM_BFA_ReconEmployeeGLV.RECON_Link 
                inner join Document on
                Document.Document_GlvTypeId = Document_GlvType.Id
                and SOM_BFA_ReconEmployeeGLV.uniqueid = ? and  Document.Document_GlvTypeId = ? ",array($glvType,$uniqueId,$document_GlvTypeId));
                $data = $query->result();
            }
            $existedDocumentName = array();
            $haveSimilarName = false;
            if($data){
                foreach($documentNames as $documentname){
                    foreach($data as $item){
                        if(str_replace(' ','_',$documentname) == rawurldecode($item->DocumentName)){
                            $existedDocumentName[]= $documentname;
                            $haveSimilarName = true;
                        }
                    }
                }
               
            }
            log_message('info',"checkIfDocumentExist SQL= " . $this->db->last_query());  
            if($haveSimilarName) {
                return $existedDocumentName; 
            } else {
                return null; 
            }
        }
        catch(Exception $e){
            log_message('error',"checkIfDocumentExist: ".$e->getMessage());
            return null;
        }
    }

    /**
     * get Link Documents 
     */
    public function getListDocuments($uniqueId,$glvType,$document_glvTypeId){
        try{
            if($glvType == TRANSACTION_TYPE){
                $query = $this->db->query("select  Document.DocumentName,Document.Id from COA_SOM_LedgerData 
                inner join Document_GlvType on Document_GlvType.GlvType = ?
                and Document_GlvType.Id = COA_SOM_LedgerData.RECON_Link 
                inner join Document on
                Document.Document_GlvTypeId = Document_GlvType.Id
                and COA_SOM_LedgerData.uniqueid = ? and  Document.Document_GlvTypeId = ? order by Document.Id desc ",array($glvType,$uniqueId,$document_glvTypeId));
                $data = $query->result();
            } else{ // if payroll
                $query = $this->db->query("select  Document.DocumentName,Document.Id from SOM_BFA_ReconEmployeeGLV 
                inner join Document_GlvType on Document_GlvType.GlvType = ?
                and Document_GlvType.Id = SOM_BFA_ReconEmployeeGLV.RECON_Link 
                inner join Document on
                Document.Document_GlvTypeId = Document_GlvType.Id
                and SOM_BFA_ReconEmployeeGLV.uniqueid = ?  and  Document.Document_GlvTypeId = ? order by Document.Id desc",array($glvType,$uniqueId, $document_glvTypeId));
                $data = $query->result();
            }
           
            log_message('info',"getListDocuments SQL= " . $this->db->last_query());
            return $data;
        }
        catch( Exception $e){
            log_message('error',"getListDocuments: ".$e->getMessage());
            return "";
        }
    }

    /**
     * delete document
     * */
    public function deleteDocument($documentId,$document_GlvTypeId,$glvType,$uniqueId) 
    {
        try {
            $this->db->trans_start();
            $res = $this->db->query('select count(1) as NumberOfDocument from Document where  Document_GlvTypeId = ?', $document_GlvTypeId);
            log_message('info',"deleteDocument SQL= " . $this->db->last_query());  
            if($res && $res->result() && $res->result()[0]->NumberOfDocument == 1){
                $this->db->query('delete from Document_GlvType where  Id = ?', $document_GlvTypeId);
                if($glvType == TRANSACTION_TYPE){
                    $this->db->query('update  COA_SOM_LedgerData set RECON_Link = NULL where  uniqueid = ?', $uniqueId);
                } else {
                    $this->db->query('update  SOM_BFA_ReconEmployeeGLV set RECON_Link = NULL where  uniqueid = ?', $uniqueId);
                }
                
            } else {
                $this->db->query('delete from Document where  Id = ?', $documentId);
            }
            log_message('info',"deleteDocument SQL= " . $this->db->last_query());  
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                log_message('info',"deleteDocument SQL= " . $this->db->last_query());  
                return false;
            }
            else
            {
                $this->db->trans_commit();
                log_message('info',"deleteDocuments SQL= " . $this->db->last_query());  
                return true;
            }
        }
        catch(Exception $e){
            log_message('error',"checkIfDocumentExistByNuniqueIdAndGlvType: ".$e->getMessage());
            return false;
        }
    }
    
}
