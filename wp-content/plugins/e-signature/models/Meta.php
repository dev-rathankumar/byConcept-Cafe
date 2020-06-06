<?php
/**
 *  @author abu shoaib
 *  @since 1.2.9
 */
class WP_E_Meta extends WP_E_Model
{
    
    private $table;
    

	public function __construct(){
		parent::__construct();
 
		$this->table = $this->table_prefix . "documents_meta";
	}
    
    
    public function add($document_id,$meta_key,$meta_value)
    {
		
			if($this->exists($document_id,$meta_key))
			{
                            
				$this->update($document_id,$meta_key,$meta_value);
				return ; 
			}
		
            
			$this->wpdb->query(
				$this->wpdb->prepare(
					"INSERT INTO " . $this->table . " VALUES(null, %d, %s, %s)", 
					$document_id, 
					$meta_key, 
					$meta_value
				)
			);
		
		  return $this->wpdb->insert_id;
    }
    
     public function get($document_id,$meta_key)
    {
		
        $meta = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT meta_value FROM " . $this->table . " WHERE document_id=%d and meta_key=%s LIMIT 1",$document_id, $meta_key
			)
		);
		if(isset($meta)) return $meta->meta_value;
		else return false;
    }
    
    public function getall_bykey($meta_key){
        
        return $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT document_id,meta_value FROM " . $this->table . " WHERE meta_key = %s", $meta_key
				)
			);
    }
    
     public function metadata_by_keyvalue($meta_key,$meta_value){
        
         $meta = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT document_id FROM " . $this->table . " WHERE meta_key=%s and meta_value=%s LIMIT 1",$meta_key, $meta_value
			)
		);
		if(isset($meta)) return $meta->document_id;
		else return false;
    }
    
    public function get_all($meta_id){
        
        return $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT meta_key,meta_value FROM " . $this->table . " WHERE document_id = %d", $meta_id
				)
			);
    }
    
    public function clone_all_meta($document_id, $clone_doc_id) {
            $all_meta = $this->get_all($clone_doc_id);
            if (is_array($all_meta)) {
                foreach ($all_meta as $meta) {
                    $this->add($document_id, $meta->meta_key, $meta->meta_value);
                }
            }
        }
    
    public function exists($document_id,$meta_key){
		
		return $this->wpdb->query(
			$this->wpdb->prepare(
				"SELECT id FROM " . $this->table . " WHERE document_id=%d and meta_key='%s'",$document_id,$meta_key
			)
		);
		
	}
	
    public function update($document_id,$meta_key,$meta_value)
    {
			
        	$this->wpdb->query(
				$this->wpdb->prepare(
					"UPDATE " . $this->table . " SET meta_value=%s WHERE document_id=%d and meta_key=%s", 
					$meta_value, 
					$document_id, 
					$meta_key
				)
			);
           
            return $this->wpdb->insert_id;
    }
    
     public function delete($document_id,$meta_key)
    {
		
        	return	$this->wpdb->query(
				$this->wpdb->prepare(
					"DELETE from " . $this->table . " WHERE document_id=%d and meta_key=%s", 
					$document_id,
                    $meta_key
				)
			);
		
    }
    
    
     public function delete_all($document_id)
    {
		
        	return	$this->wpdb->query(
				$this->wpdb->prepare(
					"DELETE from " . $this->table . " WHERE document_id=%d", 
					$document_id
				)
			);
		
    }
    
    
}