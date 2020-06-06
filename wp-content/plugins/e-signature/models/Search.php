<?php
/**
 *  @author abu shoaib
 *  @since 1.3.0
 */
class WP_E_Search extends WP_E_Model
{
    
	public function __construct(){
		parent::__construct();	
                $this->table = $this->table_prefix . "documents";
		$this->usertable = $this->table_prefix . "document_users";
	}
        
        public function get_search_user_id()
        {
                $esig_all_sender = isset($_GET['esig_all_sender'])?$_GET['esig_all_sender']:null;
                
                if($esig_all_sender)
                {
                   
                    return $esig_all_sender ;
                }
                else
                {
                    if(!is_esig_super_admin())
                    {
                       return  get_current_user_id() ;
                    }
                }
                
                return false;
        }
        
        public function is_sa_search()
        {
                 $esig_all_sender = isset($_GET['esig_all_sender'])?$_GET['esig_all_sender']:null;
                 
                if($esig_all_sender == "All Sender")
                {
                    return true;
                }
                
                if($esig_all_sender)
                {
                   
                    return false ;
                }
                else
                {
                    if(!is_esig_super_admin())
                    {
                       return  false ;
                    }
                }
                
                return true;
        }
 
        public function fetchAllOnSearch($esig_document_search)
        {
		$search = '%'. $this->esc_sql($esig_document_search) . '%';
               
                //pagination settings 
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                
                $document_status= isset( $_GET['document_status'] ) ?  $_GET['document_status'] : "awaiting";
		
		$limit = WP_E_General::get_doc_display_number();
		$offset = ( $pagenum - 1 ) * $limit;
                global $search_docs;
           
                if($this->is_sa_search())
                {
                   
                    $search_docs =$this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM ((SELECT d.document_id,d.document_title,d.document_type,d.document_status,d.last_modified,u.signer_name FROM ". $this->table ." as d LEFT JOIN ". $this->usertable ." as u on d.document_id=u.document_id WHERE d.document_status='%s') UNION DISTINCT (SELECT d.document_id,d.document_title,d.document_type,d.document_status,d.last_modified,u.signer_name FROM ". $this->table ." as d RIGHT JOIN ". $this->usertable ." as u on d.document_id=u.document_id WHERE d.document_status='%s')) as esig WHERE document_title LIKE '%s' OR signer_name LIKE '%s'",$document_status,$document_status,$search,$search));
                    return array_slice($search_docs, $offset, $limit);
                }
                else
                {
                     $user_id = $this->get_search_user_id();
                     $search_docs =$this->wpdb->get_results($this->wpdb->prepare("SELECT ". $this->table .".document_id,". $this->table .".user_id,".$this->table .".document_title,". $this->table .".document_status,". $this->table .".document_type,". $this->table .".date_created,". $this->table .".last_modified FROM " . $this->table . " LEFT OUTER JOIN ". $this->usertable ." ON ". $this->table .".document_id =". $this->usertable .".document_id WHERE ". $this->table .".user_id=%d and ".$this->table.".document_status=%s and ".$this->table.".document_title LIKE %s OR ".$this->table.".user_id=%d and ".$this->table.".document_status=%s and ". $this->usertable .".signer_name LIKE %s LIMIT %d,%d",$user_id,$document_status,$search,$user_id,$document_status, $search,$offset,$limit));
                     return $search_docs;
                }
		//$docs=apply_filters('esig-search-document-filter',$docs,$esig_document_search);
		return $search_docs;
	}
        
        public function esc_sql($searchWord){
          return $this->wpdb->esc_like(esc_sql($searchWord)); 
        }
        
        
        public function search_document_total($esig_document_search)
        {
                $search = '%'. $this->esc_sql($esig_document_search)  . '%';
                
                $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                
		$document_status= isset( $_GET['document_status'] ) ?  $_GET['document_status'] : "awaiting";
		
                if($this->is_sa_search())
                {
                    global $search_docs;
                    if(!is_null($search_docs)){
                        return count($search_docs);
                    }
                   
                    $docs=$this->wpdb->get_results($this->wpdb->prepare("SELECT count(*) as cnt FROM (SELECT d.document_id,d.document_title,d.document_type,d.document_status,d.last_modified,u.signer_name FROM ". $this->table ." as d LEFT JOIN ". $this->usertable ." as u on d.document_id=u.document_id WHERE d.document_status='%s' UNION DISTINCT SELECT d.document_id,d.document_title,d.document_type,d.document_status,d.last_modified,u.signer_name FROM ". $this->table ." as d RIGHT JOIN ". $this->usertable ." as u on d.document_id=u.document_id WHERE d.document_status='%s') as esig WHERE document_title LIKE '%s' OR signer_name LIKE '%s'",$document_status,$document_status,$search,$search));
                   
                    return esigget("cnt",$docs[0]);
                }
                else
                {
                    $user_id = $this->get_search_user_id();
                     $docs=$this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table . " INNER JOIN ". $this->usertable ." ON ". $this->table .".document_id =". $this->usertable .".document_id WHERE ". $this->table .".user_id=%d and ".$this->table.".document_status=%s and ".$this->table.".document_title LIKE %s OR ".$this->table.".user_id=%d and ".$this->table.".document_status=%s and ". $this->usertable .".signer_name LIKE %s",$user_id,$document_status,$search,$user_id,$document_status, $search));
                }
          
                 return count($docs);
        }
        
         /**
	* 
	* 
	* @return
	*/
	public function pagination()
	{
		$status = isset($_GET['document_status']) ? sanitize_text_field($_GET['document_status']) : 'awaiting';
		
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
		
                $esig_document_search = ESIG_SEARCH_GET('esig_document_search');
                
                if($esig_document_search)
                {
                   $total =$this->search_document_total($esig_document_search);
                }
                else
                {   
                    $doc_obj= new WP_E_Document();
                    $total =  $doc_obj->getDocumentsTotal($status);
                }
                
                $limit = WP_E_General::get_doc_display_number();
              
		$num_of_pages = ceil( $total / $limit );
		
		$page_links = paginate_links( array(
					'base' => add_query_arg( 'pagenum', '%#%' ),
					'format' => '',
					'prev_text' => __( '&laquo;', 'aag' ),
					'next_text' => __( '&raquo;', 'aag' ),
					'total' => $num_of_pages,
					'current' => $pagenum
				) );

			$page_text = "";
			if ( $page_links ) {
				$page_text='<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
			}
			
			return $page_text ; 		
	}
   
}