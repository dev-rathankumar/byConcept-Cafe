<?php
if (!defined('ABSPATH'))
  {
  exit;
  }
class Sbdzones_Post
  {
  private static $stop = false;
  public function __construct()
    {
    add_action('init', array(
      $this,
      'register_post_szbdzones'
    ));
    add_action('registered_post_type', array(
      $this,
      'eval_caps'
    ), 99, 2);
    }
  public function register_post_szbdzones()
    {
    $labels = array(
      'name' => __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN),
      'menu_name' => __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN),
      'name_admin_bar' => __('Shipping Zone Maps', SZBD::TEXT_DOMAIN),
      'all_items' => __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN),
      'singular_name' => __('Zone List', SZBD::TEXT_DOMAIN),
      'add_new' => __('New Shipping Zone', SZBD::TEXT_DOMAIN),
      'add_new_item' => __('Add New Zone', SZBD::TEXT_DOMAIN),
      'edit_item' => __('Edit Zone', SZBD::TEXT_DOMAIN),
      'new_item' => __('New Zone', SZBD::TEXT_DOMAIN),
      'view_item' => __('View Zone', SZBD::TEXT_DOMAIN),
      'search_items' => __('Search Zone', SZBD::TEXT_DOMAIN),
      'not_found' => __('Nothing found', SZBD::TEXT_DOMAIN),
      'not_found_in_trash' => __('Nothing found in Trash', SZBD::TEXT_DOMAIN),
      'parent_item_colon' => ''
    );
    $caps   = array(
      'edit_post' => 'edit_szbdzone',
      'read_post' => 'read_szbdzone',
      'delete_post' => 'delete_szbdzone',
      'edit_posts' => 'edit_szbdzones',
      'edit_others_posts' => 'edit_others_szbdzones',
      'publish_posts' => 'publish_szbdzones',
      'read_private_posts' => 'read_private_szbdzones',
      'delete_posts' => 'delete_szbdzones',
      'delete_private_posts' => 'delete_private_szbdzones',
      'delete_published_posts' => 'delete_published_szbdzones',
      'delete_others_posts' => 'delete_others_szbdzones',
      'edit_private_posts' => 'edit_private_szbdzones',
      'edit_published_posts' => 'edit_published_szbdzones',
      'create_posts' => 'edit_szbdzones'
    );
    $args   = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => false,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => false,
      'hierarchical' => false,
      'supports' => array(
        'title',
        'author'
      ),
      'exclude_from_search' => true,
      'show_in_nav_menus' => false,
      'show_in_menu' => 'woocommerce',
      'can_export' => true,
      'map_meta_cap' => true,
      'capability_type' => 'szbdzone',
      'capabilities' => $caps
    );
    register_post_type(SZBD::POST_TITLE, $args);
    }
  function eval_caps($post_type, $args)
    {
    if (SZBD::POST_TITLE === $post_type && self::$stop == false)
      {
         if(plugin_basename(__FILE__) == "shipping-zones-by-drawing-premium/classes/class-szbd-the-post.php"){
          include(plugin_dir_path(__DIR__) . 'includes/start-args-prem.php');
            }else{
                 include(plugin_dir_path(__DIR__) . 'includes/start-args.php');
            }

      self::$stop = true;
      register_post_type(SZBD::POST_TITLE, $args);
      }
    }
  }
new Sbdzones_Post();


function szbd_duplicate_post_at_edit(){
	global $wpdb;
  
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'szbd_duplicate_post_at_edit' == $_REQUEST['action'] ) ) ) {
		wp_die('No post to duplicate has been supplied!');
	}


	if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
		return;


	$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

	$post = get_post( $post_id );

	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;


	if (isset( $post ) && $post != null) {


		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);


		$new_post_id = wp_insert_post( $args );


		$taxonomies = get_object_taxonomies($post->post_type);
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}


		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				if( $meta_key == '_wp_old_slug' ) continue;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}



		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}
}
add_action( 'admin_action_szbd_duplicate_post_at_edit', 'szbd_duplicate_post_at_edit' );

/*
 * Add a duplicate link
 */
function szbd_duplicate_post_link( $actions, $post ) {

	if (current_user_can('edit_posts') && $post->post_type == 'szbdzones' ) {
		$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=szbd_duplicate_post_at_edit&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '"  rel="permalink">Duplicate</a>';
	}
	return $actions;
}

add_filter( 'post_row_actions', 'szbd_duplicate_post_link', 10, 2 );
?>
