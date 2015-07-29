<?php 

/* 
	Plugin Name: WP Post List UI 
	Author: Rajesh Kumar Sharma
	Author URI: http://sitextensions.com/
	Plugin URI: http://wordpress.org/plugins/wp-post-list-ui/
	Version: 1.1
	Description: List the post by make the custom list.
*/

/*add_action( 'admin_menu', 'register_wp_post_list_ui_page' );
function register_wp_post_list_ui_page(){
	add_menu_page( 'WP Post List UI', 'WP Post List UI', 'manage_options', 'wp-post-list-ui', 'register_wp_post_list_ui_page_callback' ); 
}

function my_custom_menu_page(){
	echo "Admin Page Test";	
}*/

/*add_action( 'admin_init', 'register_wp_post_list_ui_setting' );
function register_wp_post_list_ui_setting() {
	register_setting( 'wp_post_list_ui_setting', 'wp_post_list_ui_setting', 'wp_post_list_ui_setting_callback' ); 
}

function wp_post_list_ui_setting_callback($result = array()){
	return $result;
} */

/**
 * Register a book post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
add_action( 'init', 'wplpui_init' );
function wplpui_init() {
	$labels = array(
		'name'               => 'Lists',
		'singular_name'      => 'List',
		'menu_name'          => 'List Posts UI',
		'name_admin_bar'     => 'List',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New List',
		'new_item'           => 'New List',
		'edit_item'          => 'Edit List',
		'view_item'          => 'View List',
		'all_items'          => 'All Lists',
		'search_items'       => 'Search Lists',
		'parent_item_colon'  => 'Parent Lists:',
		'not_found'          => 'No lists found.',
		'not_found_in_trash' => 'No lists found in Trash.'
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'post-list-ui' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => null,
		'supports'           => array( 'title' )
	);

	register_post_type( 'wplpui', $args );
}


// Add the List Post Meta Boxes
add_action( 'add_meta_boxes', 'add_wplpui_metabox' );
function add_wplpui_metabox() {
	add_meta_box('wplpui_metabox_section', 'WP Post List UI', 'wplpui_metabox_section_callback', 'wplpui', 'advanced', 'high');
}

function wplpui_metabox_section_callback(){
	global $post;
	
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="wplpuimeta_noncename" id="wplpuimeta_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	// Get the location data if its already been entered
	// $location = get_post_meta($post->ID, '_location', true);
	$wplpui_atts = get_post_meta($post->ID, '_wplpui_atts', true);
	$wplpui_atts = json_decode($wplpui_atts);
	// print_r($wplpui_atts);
	
	// Echo out the field
	// echo '<input type="text" name="_location" value="' . $location  . '" class="widefat" />';
	$post_types = get_post_types();
	
	?>
		<label>
			<span>Post Type</span>
			<select name="_wplpui_atts[_post_types][]" id="wplpui_post_types" multiple>
				<?php 
					foreach ( $post_types as $post_type ) {
						$selected = (in_array($post_type, $wplpui_atts->_post_types)) ? 'selected' : '';
					   	echo '<option value="'. $post_type .'" '.$selected.'>' . ucfirst($post_type) . '</option>';
					}
				?>
			</select>
		</label>

		<label>
			<span>Categories</span>
			<select name="_wplpui_atts[_tax_query][]" id="wplpui_post_categories" multiple></select>
		</label>

		<label>
			<span>Order By</span>
			<select name="_wplpui_atts[_orderby]" id="wplpui_orderby">
				<option value="date" <?php echo $wplpui_atts->_orderby == 'date' ? 'selected' : ''; ?>>Date</option>
				<option value="ID" <?php echo $wplpui_atts->_orderby == 'ID' ? 'selected' : ''; ?>>ID</option>
				<option value="title" <?php echo $wplpui_atts->_orderby == 'title' ? 'selected' : ''; ?>>Title {Post Title}</option>
				<option value="name" <?php echo $wplpui_atts->_orderby == 'name' ? 'selected' : ''; ?>>Name {Post Slug}</option>
				<option value="type" <?php echo $wplpui_atts->_orderby == 'type' ? 'selected' : ''; ?>>Post Type</option>
				<option value="modified" <?php echo $wplpui_atts->_orderby == 'modified' ? 'selected' : ''; ?>>Modified</option>
				<option value="rand" <?php echo $wplpui_atts->_orderby == 'rand' ? 'selected' : ''; ?>>Randam</option>
				<option value="comment_count" <?php echo $wplpui_atts->_orderby == 'comment_count' ? 'selected' : ''; ?>>Comment Count</option>
				<option value="menu_order" <?php echo $wplpui_atts->_orderby == 'menu_order' ? 'selected' : ''; ?>>Menu Order</option>
				<option value="none" <?php echo $wplpui_atts->_orderby == 'none' ? 'selected' : ''; ?>>None</option>
			</select>
		</label>

		<label>
			<span>Order</span>
			<select name="_wplpui_atts[_order]" id="wplpui_order">
				<option value="asc" <?php echo $wplpui_atts->_order == 'asc' ? 'selected' : ''; ?>>Ascending</option>
				<option value="desc" <?php echo $wplpui_atts->_order == 'desc' ? 'selected' : ''; ?>>Descending</option>
			</select>
		</label>

		<label>
			<span>Show Posts Per Page</span>
			<input type="number" value="<?php echo $wplpui_atts->_posts_per_page; ?>" min="-1" name="_wplpui_atts[_posts_per_page]" id="wplpui_posts_per_page" />
		</label>

		<label>
			<span>Posts Offset</span>
			<input type="number" value="<?php echo $wplpui_atts->_offset; ?>" min="0" name="_wplpui_atts[_offset]" id="wplpui_offset" />
		</label>

		<label>
			<span>Posts In</span>
			<input type="text" placeholder="Enter posts IDs separeted by COMMA(,) to include." value="<?php echo $wplpui_atts->_post__in; ?>" name="_wplpui_atts[_post__in]" id="wplpui_post__in" />
		</label>

		<label>
			<span>Posts Not In</span>
			<input type="text" placeholder="Enter posts IDs separeted by COMMA(,) to exclude." value="<?php echo $wplpui_atts->_post__not_in; ?>" name="_wplpui_atts[_post__not_in]" id="wplpui_post__not_in" />
		</label>

		<label>
			<span>Show Pagination</span>
			<select name="_wplpui_atts[_pagination]" id="wplpui_order">
				<option value="no" <?php echo $wplpui_atts->_pagination == 'no' ? 'selected' : ''; ?>>No</option>
				<option value="yes" <?php echo $wplpui_atts->_pagination == 'yes' ? 'selected' : ''; ?>>Yes</option>
			</select>
		</label>

		<label>
			<span>Post Status</span>
			<select name="_wplpui_atts[_post_status]" id="wplpui_post_status">
				<option value="publish" <?php echo $wplpui_atts->_post_status == 'publish' ? 'selected' : ''; ?>>Publish</option>
				<option value="future" <?php echo $wplpui_atts->_post_status == 'future' ? 'selected' : ''; ?>>Future</option>
				<option value="draft" <?php echo $wplpui_atts->_post_status == 'draft' ? 'selected' : ''; ?>>Draft</option>
				<option value="pending" <?php echo $wplpui_atts->_post_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
				<option value="private" <?php echo $wplpui_atts->_post_status == 'private' ? 'selected' : ''; ?>>Private</option>
				<option value="trash" <?php echo $wplpui_atts->_post_status == 'trash' ? 'selected' : ''; ?>>Trash</option>
				<option value="auto-draft" <?php echo $wplpui_atts->_post_status == 'auto-draft' ? 'selected' : ''; ?>>Auto-Draft</option>
				<option value="inherit" <?php echo $wplpui_atts->_post_status == 'inherit' ? 'selected' : ''; ?>>Inherit</option>
			</select>
		</label>

		<label>
			<span>Select Template</span>
			<select name="_wplpui_atts[_template]" id="wplpui_template">
				<?php 
					$list_dir = list_dir_files();
					foreach ($list_dir as $key => $value) {
						$selected = $wplpui_atts->_template == $value ? 'selected' : '';
						echo '<option '.$selected.' value="'.$value.'">';
						echo ucfirst($value);
						echo '</option>';
					}
				?>
			</select>
		</label>

		<label id="template_name_label" style="display:none;">
			<span>Template Name</span>
			<input type="text" value="<?php echo $wplpui_atts->_template_name; ?>" placeholder="example" name="_wplpui_atts[_template_name]" id="wplpui_template_name" />
		</label>

		<label for="wplpui_template_code">
			<span>Put your own code here...</span>
		</label>
		<textarea id="wplpui_template_code" name="_wplpui_atts[_wplpui_template_code]"><?php echo $wplpui_atts->_wplpui_template_code; ?></textarea>

		<script>
			/*var value = "// The bindings defined specifically in the Sublime Text mode\nvar bindings = {\n";
			var map = CodeMirror.keyMap.sublime;
			for (var key in map) {
				var val = map[key];
				if (key != "fallthrough" && val != "..." && (!/find/.test(val) || /findUnder/.test(val)))
			  		value += "  \"" + key + "\": \"" + val + "\",\n";
			}

			value += "}\n\n// The implementation of joinLines\n";
			value += CodeMirror.commands.joinLines.toString().replace(/^function\s*\(/, "function joinLines(").replace(/\n  /g, "\n") + "\n";*/
			
			// var editor = CodeMirror(document.body.getElementsByTagName("article")[0], {
			var editor = CodeMirror.fromTextArea(document.getElementById("wplpui_template_code"), {
				// value: value,
				lineNumbers: true,
				// mode: "application/x-httpd-php",
				// mode: "text/x-php",
				mode: "javascript",
				keyMap: "sublime",
				autoCloseBrackets: true,
				matchBrackets: true,
				showCursorWhenSelecting: true,
				theme: "monokai",
				height: "350px",
			});
		</script>
		
		<script type="text/javascript">
		
			function get_category_ajax(post_type){
				jQuery.ajax({
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					method: 'POST',
					// dataType: 'html',
					data:{'action':'list_all_categories', 'post_type':post_type, 'post_id': <?php echo $post->ID; ?>},
					success:function(r){
						jQuery('#wplpui_post_categories').html(r);
					}
				});
			}

			function template_content(){
				if(jQuery('#wplpui_template').val() == 'custom'){
					jQuery('#template_name_label').show();
					// jQuery('#wplpui_template_code').val('');
					editor.getDoc().setValue('');
				}
				else{
					jQuery.ajax({
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						method: 'POST',
						// dataType: 'html',
						data:{'action':'get_file_content', 'file_name': jQuery('#wplpui_template').val()},
						success:function(r){
							// jQuery('#wplpui_post_categories').html(r);
							editor.getDoc().setValue( stripslashes(r) );
						}
					});
				}
			}

			jQuery(document).ready(function($){
				get_category_ajax($('#wplpui_post_types').val());
				$('#wplpui_post_types').on('change', function(){
					get_category_ajax($(this).val());
				});

				template_content();
				$('#wplpui_template').on('change', function(){
					template_content();
				});

				setInterval(function(){
					editor.save();
				}, 1000);
			});
		</script>
	<?php 

}


// Save the Metabox Data
add_action('save_post', 'wplpui_save_meta', 1, 2); // save the custom fields
function wplpui_save_meta($post_id, $post) {
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['wplpuimeta_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	
	if($_POST['_wplpui_atts']['_template'] == 'custom'){
		$file_name = trim($_POST['_wplpui_atts']['_template_name']) != '' ? $_POST['_wplpui_atts']['_template_name'] : 'mycode';
		$file = 'content-' . $file_name . '.php';
		$path = plugin_dir_path(__FILE__) . "templates" . DIRECTORY_SEPARATOR;

		file_put_contents($path . $file, stripslashes($_POST['_wplpui_atts']['_wplpui_template_code']));
		// chmod($path . $file, 0777);

		$_POST['_wplpui_atts']['_template'] = $_POST['_wplpui_atts']['_template_name'];
	}
	else{
		$file_name = $_POST['_wplpui_atts']['_template'];
		$file = 'content-' . $file_name . '.php';
		$path = plugin_dir_path(__FILE__) . "templates" . DIRECTORY_SEPARATOR;

		file_put_contents($path . $file, stripslashes($_POST['_wplpui_atts']['_wplpui_template_code']));
		// chmod($path . $file, 0777);
	}


	unset($_POST['_wplpui_atts']['_wplpui_template_code']);
	$wplpui_meta['_wplpui_atts'] = json_encode($_POST['_wplpui_atts']);

	// $wplpui_meta = $_POST['_wplpui_atts'];
	
	// Add values of $wplpui_meta as custom fields
	
	foreach ($wplpui_meta as $key => $value) { // Cycle through the $wplpui_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
			$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}
	// print_r($_POST['_wplpui_atts']);die;
}

add_action( 'current_screen', 'post_active_inactive' );
function post_active_inactive($current_screen){
   	if ( 'wplpui' == $current_screen->post_type && 'post' == $current_screen->base && empty($current_screen->action) ) {
		// Do something in the edit screen of this post type
		add_action( 'add_meta_boxes', 'add_wplpui_shortcode_metabox' );
	}
}

function add_wplpui_shortcode_metabox() {
	add_meta_box('wplpui_shortcode_metabox_section', 'WP Post List UI Shortcode', 'wplpui_shortcode_metabox_section_callback', 'wplpui', 'side', 'high');
}

function wplpui_shortcode_metabox_section_callback(){
	global $post;
	?>
		<p>Use following shortcode to display posts on any page.</p>
		<p><strong>Editor Shortcode</strong></p>
		<p>[wp-post-list-ui id="<?php echo $post->ID; ?>"]</p>
		<p><strong>PHP Shortcode</strong></p>
		<p><code><?php echo '&lt;?php echo do_shortcode("[wp-post-list-ui id=\''.$post->ID.'\']"); ?&gt;'; ?></code></p>
	<?php 
}


add_action( 'wp_ajax_list_all_categories', 'list_all_categories' );
function list_all_categories(){
	$post_type = $_POST['post_type'];
	$post_id = $_POST['post_id'];

	$wplpui_atts = get_post_meta($post_id, '_wplpui_atts', true);
	$wplpui_atts = json_decode($wplpui_atts);

	$selected = (in_array('all', $wplpui_atts->_tax_query)) ? 'selected' : '';

	echo '<option '.$selected.' value="all">';
	echo 'All';
	echo '</option>';

	if(!empty($post_type) && is_array($post_type)){

		foreach ($post_type as $key => $value) {
			$customPostTaxonomies = get_object_taxonomies($value);
			// print_r($customPostTaxonomies);
			$cat_tax = isset($customPostTaxonomies[0]) ? $customPostTaxonomies[0] : '';

			if($cat_tax != ''){
				$terms = get_terms($cat_tax); // Get all terms of a taxonomy
				// print_r($terms);

				if(!empty($terms)){
					foreach($terms as $term){
						$selected = (in_array($cat_tax.'|'.$term->slug, $wplpui_atts->_tax_query)) ? 'selected' : '';
						echo '<option '.$selected.' value="'.$cat_tax.'|'.$term->slug.'">';
						echo $term->name;
						echo '</option>';
					}
				}
			}	
		}
	}

	die;
}


add_shortcode('wp-post-list-ui', 'wp_post_list_ui_shortcode');
function wp_post_list_ui_shortcode($atts){

	$atts = shortcode_atts( array(
							'id' => '',
							), $atts, 'wp-post-list-ui' );

	$id = $atts['id'];

	$post = get_post($id);
	
	$meta = get_post_meta($id);
	$meta = json_decode($meta['_wplpui_atts'][0]);
	// print_r($meta);

	// Template Name
	$template = $meta->_template;
	// $template = 'default';
	$path = plugin_dir_path(__FILE__) . "templates" . DIRECTORY_SEPARATOR;

	// Post Types
	$post_types = !empty($meta->_post_types) ? $meta->_post_types : array('post');

	// Taxonomy Query
	$tax_query = array();
	if(!empty($meta->_tax_query) && !in_array('all', $meta->_tax_query)){
		foreach ($meta->_tax_query as $key => $value) {
			$value = explode('|', $value);
			$taxonomy = $value[0];
			$terms = $value[1];

			$tax_query[] = array(
								'taxonomy' => $taxonomy,
								'field' => 'slug',
								'terms' => $terms 
							);

		}
	}

	// Order By 
	$orderby = $meta->_orderby;
	// Order
	$order = $meta->_order;
	// Posts Per Page
	$posts_per_page = trim($meta->_posts_per_page) == '' ? get_option('posts_per_page') : $meta->_posts_per_page;
	// Offset
	$offset = $meta->_offset;
	// Post In
	$post__in = trim($meta->_post__in) != '' ? explode(',', $meta->_post__in) : array();
	// Post Not In
	$post__not_in = trim($meta->_post__not_in) != '' ? explode(',', $meta->_post__not_in) : array();
	// Show Pagination
	$pagination = $meta->_pagination;
	// Post Status
	$post_status = $meta->_post_status;

	$args = array(
			'post_type' => $post_types,
			'orderby' => $orderby,
			'order' => $order,
			'posts_per_page' => $posts_per_page,
			'offset' => $offset,
			'post__in' => $post__in,
			'post__not_in' => $post__not_in,
			'post_status' => $post_status,
			'tax_query' => $tax_query
		);

	$listing = new WP_Query($args);

	while ( $listing->have_posts() ){
		$listing->the_post();
		setup_postdata($listing);
		if(file_exists($path . 'content-' . $template . '.php')){
			include($path . 'content-' . $template . '.php');	
		}
		else{
			include($path . 'content-default.php');
		}
	}
	wp_reset_postdata();

	/*$original_atts = $atts;

	$atts = shortcode_atts( array(
		'title'              => '',
		'author'              => '',
		'category'            => '',
		'date_format'         => '(n/j/Y)',
		'display_posts_off'   => false,
		'exclude_current'     => false,
		'id'                  => false,
		'ignore_sticky_posts' => false,
		'image_size'          => false,
		'include_title'       => true,
		'include_author'      => false,
		'include_content'     => false,
		'include_date'        => false,
		'include_excerpt'     => false,
		'meta_key'            => '',
		'meta_value'          => '',
		'no_posts_message'    => '',
		'offset'              => 0,
		'order'               => 'DESC',
		'orderby'             => 'date',
		'post_parent'         => false,
		'post_status'         => 'publish',
		'post_type'           => 'post',
		'posts_per_page'      => '10',
		'tag'                 => '',
		'tax_operator'        => 'IN',
		'tax_term'            => false,
		'taxonomy'            => false,
		'wrapper'             => 'ul',
		'wrapper_class'       => 'wp-post-list-ui-listing',
		'wrapper_id'          => false,
	), $atts, 'wp-post-list-ui' );*/

	// echo 'Hello';

	/*'tax_query' => array( 
        array( 
            'taxonomy' => 'category', //or tag or custom taxonomy
            'field' => 'id', 
            'terms' => array('9') 
        ),
        array( 
            'taxonomy' => 'category', //or tag or custom taxonomy
            'field' => 'id', 
            'terms' => array('9') 
        ),
        array( 
            'taxonomy' => 'category', //or tag or custom taxonomy
            'field' => 'id', 
            'terms' => array('9') 
        )  
    ) */
}

function list_dir_files(){
	$list_files = scandir(plugin_dir_path(__FILE__) . 'templates');
	$list_files_new = array();

	foreach ($list_files as $key => $value) {
		if(end(explode('.', $value)) == 'php'){
			$list_files_new[] = str_replace('content-', '', str_replace('.php', '', $value));
		}
	}

	$list_files_new[] = 'custom';

	return $list_files_new;
}


add_action( 'wp_ajax_get_file_content', 'get_file_content' );
function get_file_content(){
	$file_name = $_POST['file_name'];
	$file = 'content-' . $file_name . '.php';
	$path = plugin_dir_path(__FILE__) . "templates" . DIRECTORY_SEPARATOR;

	$content = file_get_contents($path . $file);
	$content = str_replace(PHP_EOL, ' ', $content);

	die( addslashes($content) );
}


add_action('admin_enqueue_scripts', 'include_script_files');
function include_script_files(){

	$include_url = plugin_dir_url(__FILE__);
	$include_url_codemirror = plugin_dir_url(__FILE__) . 'codemirror' . DIRECTORY_SEPARATOR;

	wp_enqueue_style('codemirror-css', $include_url_codemirror . 'lib/codemirror.css');
	wp_enqueue_style('foldgutter-css', $include_url_codemirror . 'addon/fold/foldgutter.css');
	wp_enqueue_style('dialog-css', $include_url_codemirror . 'addon/dialog/dialog.css');
	wp_enqueue_style('monokai-css', $include_url_codemirror . 'theme/monokai.css');

	wp_enqueue_script('codemirror-js', $include_url_codemirror . 'lib/codemirror.js');
	wp_enqueue_script('searchcursor-js', $include_url_codemirror . 'addon/search/searchcursor.js');
	wp_enqueue_script('search-js', $include_url_codemirror . 'addon/search/search.js');
	wp_enqueue_script('dialog-js', $include_url_codemirror . 'addon/dialog/dialog.js');
	wp_enqueue_script('matchbrackets-js', $include_url_codemirror . 'addon/edit/matchbrackets.js');
	wp_enqueue_script('closebrackets-js', $include_url_codemirror . 'addon/edit/closebrackets.js');
	wp_enqueue_script('comment-js', $include_url_codemirror . 'addon/comment/comment.js');
	wp_enqueue_script('hardwrap-js', $include_url_codemirror . 'addon/wrap/hardwrap.js');
	wp_enqueue_script('foldcode-js', $include_url_codemirror . 'addon/fold/foldcode.js');
	wp_enqueue_script('brace-fold-js', $include_url_codemirror . 'addon/fold/brace-fold.js');
	wp_enqueue_script('javascript-codemirror-js', $include_url_codemirror . 'mode/javascript/javascript.js');
	wp_enqueue_script('keymap-sublime-js', $include_url_codemirror . 'keymap/sublime.js');
	// wp_enqueue_script('htmlmixed-js', $include_url_codemirror . 'htmlmixed/htmlmixed.js');
	// wp_enqueue_script('xml-js', $include_url_codemirror . 'xml/xml.js');
	// wp_enqueue_script('css-js', $include_url_codemirror . 'css/css.js');
	// wp_enqueue_script('clike-js', $include_url_codemirror . 'clike/clike.js');
	// wp_enqueue_script('php-js', $include_url_codemirror . 'php.js');


	wp_enqueue_style('wplpui-css', $include_url . 'wplpui.css');
	wp_enqueue_script('wplpui-js', $include_url . 'wplpui.js');
}