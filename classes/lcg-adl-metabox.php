<?php
//Protect direct access
if ( ! defined( 'ABSPATH' ) ) die( 'Are you cheating??? Accessing this file directly is forbidden.' );

class Lcg_Metabox
{
	public function __construct() {
		// Customize the updated messages for lcg custom posts
        add_filter( 'post_updated_messages', array($this, 'lcg_customize_updated_messages') );
        // customize the column name on the carousel listing page.
        add_filter('manage_lcg_shortcode_posts_columns', array($this, 'lcg_custom_column_carousel_screen'));

        add_action('manage_lcg_shortcode_posts_custom_column', array($this, 'lcg_manage_custom_columns_carousel_screen'), 10, 2);

        add_action('do_meta_boxes', array($this, 'lcg_change_logo_meta_box_position'));
        add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );
        add_action( 'edit_post', array($this, 'lcg_save_post_meta') );

	}


	//customizes post update message
	public function lcg_customize_updated_messages( $messages ) {
		global $post;
        // add the customized message for the custom post type . here  it is logo post type.
        $messages['lcg_mainpost'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Logo Updated.', LCG_TEXTDOMAIN ),
            2  => __( 'Logo field updated.', LCG_TEXTDOMAIN ),
            3  => __( 'Logo field deleted.', LCG_TEXTDOMAIN ),
            4  => __( 'Logo updated.', LCG_TEXTDOMAIN ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Logo restored to revision from %s', LCG_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Logo published.', LCG_TEXTDOMAIN ),
            7  => __( 'Logo saved.', LCG_TEXTDOMAIN ),
            8  => __( 'Logo submitted.', LCG_TEXTDOMAIN ),
            9  => sprintf(
                __( 'Logo scheduled for: <strong>%1$s</strong>.', LCG_TEXTDOMAIN ),
                date_i18n( __( 'M j, Y @ G:i', LCG_TEXTDOMAIN ), strtotime( $post->post_date ) )
            ),
            10 => __( 'Logo draft updated.', LCG_TEXTDOMAIN )
        );
        // add customized message for the shortcode generator/carousel generator
        $messages['lcg_shortcode'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Shortcode Updated.', LCG_TEXTDOMAIN ),
            2  => __( 'Shortcode Field updated.', LCG_TEXTDOMAIN ),
            3  => __( 'Shortcode Field deleted.', LCG_TEXTDOMAIN ),
            4  => __( 'Shortcode Updated.', LCG_TEXTDOMAIN ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Shortcode restored to revision from %s', LCG_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Shortcode Published.', LCG_TEXTDOMAIN ),
            7  => __( 'Shortcode Saved.', LCG_TEXTDOMAIN ),
            8  => __( 'Shortcode Submitted.', LCG_TEXTDOMAIN ),
            9  => sprintf(
                __( 'Shortcode Scheduled for: <strong>%1$s</strong>.', LCG_TEXTDOMAIN ),
                date_i18n( __( 'M j, Y @ G:i', LCG_TEXTDOMAIN ), strtotime( $post->post_date ) )
            ),
            10 => __( 'Shortcode Draft updated.', LCG_TEXTDOMAIN )
        );

        // return the customized message
        return $messages;
	}

	 public function lcg_custom_column_carousel_screen($new_columns){
        $new_columns = array();
        $new_columns['cb']   = '<input type="checkbox" />';
        $new_columns['title']   = esc_html__('Title', LCG_TEXTDOMAIN);
        $new_columns['shortcode']   = esc_html__('Logo Shortcode', LCG_TEXTDOMAIN);
        $new_columns['logo_id']   = esc_html__('Logo ID # (helpful for widget) ', LCG_TEXTDOMAIN);
        $new_columns['date']   = esc_html__('Created at', LCG_TEXTDOMAIN);
        return $new_columns;
    }

    //manage custom columns method
    public function lcg_manage_custom_columns_carousel_screen($column_name, $post_id ) {

        switch($column_name){
            case 'shortcode': ?>
                <textarea style="resize: none; background-color: #2e85de; color: #fff;" cols="32" rows="1" onClick="this.select();" >[logo_showcase id="<?php echo intval($post_id);?>"]</textarea>
                <?php
                break;
            case 'logo_id':
                ?>
                <strong><?= intval($post_id); ?></strong>
                <?php
                break;

            default:
                break;

        }
    }

    //change default location of logo
    public function lcg_change_logo_meta_box_position() {
        remove_meta_box( 'postimagediv', 'lcg_mainpost', 'side' );
        add_meta_box( 'postimagediv', __('Logo', LCG_TEXTDOMAIN), 'post_thumbnail_meta_box', 'lcg_mainpost', 'normal', 'high' );
    }

    //add metabox for logo link and tooltip
    public function add_meta_boxes() {
        add_meta_box( 'lcg_metabox', __( 'URL & Tooltip Settings',LCG_TEXTDOMAIN ), array($this, 'lcg_meta_logo_tooltip_markup'), 'lcg_mainpost', 'normal' );
        add_meta_box( 'lcg_sg_metabox', __( 'Shortcode Generator and Settings',LCG_TEXTDOMAIN ), array($this, 'meta_carousel_markup'), 'lcg_shortcode', 'normal' );
    }


    public function lcg_meta_logo_tooltip_markup($post) {

    	// Add a nonce field so we can check for it later.
        wp_nonce_field( 'lcg_action', 'lcg_nonce' );

        $img_link          = get_post_meta( $post->ID, 'img_link', true );
        $img_tool          = get_post_meta( $post->ID, 'img_tool', true );
        $short_description = get_post_meta( $post->ID, 'short_description', true );

        ?>
        <!-- short description -->
        <div class="lcsp-row">
            <div class="lcsp-th">
                <label for="img_link"><?php esc_html_e('Short Description', LCG_TEXTDOMAIN); ?></label>
            </div>
            <div class="lcsp-td">
                <textarea name="short_description" rows="2" cols="50"><?php echo !empty($short_description) ? $short_description : ''; ?></textarea>
            </div>
        </div>
        <!-- logo link -->
        <div class="lcsp-row">
            <div class="lcsp-th">
                <label for="img_link"><?php esc_html_e('Logo link', LCG_TEXTDOMAIN); ?></label>
            </div>
            <div class="lcsp-td">
                <input type="text" class="lcsp-text-input" 
                name="img_link" 
                id="img_link" 
                value="<?php echo !empty($img_link) ? esc_url($img_link) : ''; ?>">
            </div>
        </div>

        <!--Tool tip-->
        <div class="lcsp-row">
            <div class="lcsp-th">
                <label for="img_tool"><?php esc_html_e('Tooltip Text', LCG_TEXTDOMAIN); ?></label>
            </div>
            <div class="lcsp-td">
                <input type ="text" class="lcsp-text-input"
                name="img_tool" 
                id="img_tool" 
                value="<?php echo !empty($img_tool) ? esc_attr($img_tool) : ''; ?>">
            </div>
        </div>

    	<?php
    }

    //feature of all logos
    public function meta_carousel_markup($post) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'lcg_action', 'lcg_nonce' );

        $lcg_svalue = get_post_meta( $post->ID, 'lcg_scode', true );
        $s_value = lcg()::adl_enc_unserialize( $lcg_svalue );
        $value = is_array( $s_value ) ? $s_value : array();
        extract( $value );

        //all category custom
        function list_term_items($term_name = '', $var_name=array(), $old_terms = array() ){
            global $post;
            $old_terms = (!is_array($old_terms)) ? (array) $old_terms : $old_terms;

            $terms = get_terms($term_name);

            echo '<div class="lcsp-checkbox-wrapper">';

            if(!empty($terms)) {
                echo '<ul class="cmb2-list">';
                foreach ( $terms as $term ) {
                    if ( in_array( $term->term_id, $old_terms ) ) {
                        echo "<li class='{$term->term_id}'> <input class='lcsp_taxonomy_terms' id='lcsp_taxonomy_terms_{$term->name}' type='checkbox' checked name={$var_name}[{$term->term_id}] value ='{$term->term_id}' /><label for='lcsp_taxonomy_terms_{$term->name}' > {$term->name} </label ></li>";
                    }
                    else {
                        echo "<li class='{$term->term_id}'> <input class='custom_terms' id='custom_terms_{$term->name}' type='checkbox' name={$var_name}[{$term->term_id}] value ='{$term->term_id}' /><label for='lcsp_taxonomy_terms_{$term->name}' > {$term->name} </label ></li>";
                    }
                }
            }else{
                echo '<ul>';
            }

            echo '</ul></div>';
        }
        function list_term_items_title_asc($term_name = '', $var_name=array(), $old_terms = array() ){
            global $post;
            $old_terms = (!is_array($old_terms)) ? (array) $old_terms : $old_terms;

            $terms = get_terms($term_name);

            echo '<div class="title-asc">';

            if(!empty($terms)) {
                echo '<ul class="cmb2-list">';
                foreach ( $terms as $term ) {
                    if ( in_array( $term->term_id, $old_terms ) ) {
                        echo "<li class='{$term->term_id}'> <input class='lcsp_taxonomy_terms' id='lcsp_taxonomy_terms_{$term->name}' type='checkbox' checked name={$var_name}[{$term->term_id}] value ='{$term->term_id}' /><label for='lcsp_taxonomy_terms_{$term->name}' > {$term->name} </label ></li>";
                    }
                    else {
                        echo "<li class='{$term->term_id}'> <input class='custom_terms' id='custom_terms_{$term->name}' type='checkbox' name={$var_name}[{$term->term_id}] value ='{$term->term_id}' /><label for='lcsp_taxonomy_terms_{$term->name}' > {$term->name} </label ></li>";
                    }
                }
            }else{
                echo '<ul>';
            }

            echo '</ul></div>';
        }
        function list_term_items_title_desc($term_name = '', $var_name=array(), $old_terms = array() ){
            global $post;
            $old_terms = (!is_array($old_terms)) ? (array) $old_terms : $old_terms;

            $terms = get_terms($term_name);

            echo '<div class="title-desc">';

            if(!empty($terms)) {
                echo '<ul class="cmb2-list">';
                foreach ( $terms as $term ) {
                    if ( in_array( $term->term_id, $old_terms ) ) {
                        echo "<li class='{$term->term_id}'> <input class='lcsp_taxonomy_terms' id='lcsp_taxonomy_terms_{$term->name}' type='checkbox' checked name={$var_name}[{$term->term_id}] value ='{$term->term_id}' /><label for='lcsp_taxonomy_terms_{$term->name}' > {$term->name} </label ></li>";
                    }
                    else {
                        echo "<li class='{$term->term_id}'> <input class='custom_terms' id='custom_terms_{$term->name}' type='checkbox' name={$var_name}[{$term->term_id}] value ='{$term->term_id}' /><label for='lcsp_taxonomy_terms_{$term->name}' > {$term->name} </label ></li>";
                    }
                }
            }else{
                echo '<ul>';
            }

            echo '</ul></div>';
        }

        require_once LCG_PLUGIN_DIR . 'classes/settings/settings.php';
    }

    //save all features
    public function lcg_save_post_meta( $post_id ) {
            
        // vail if the security check fails
        if (! $this->lcg_security_check('lcg_nonce', 'lcg_action', $post_id)) 
            return;

        
        // save the meta data if it is our post type lcg_mainpost post type
        if(!empty($_POST['post_type']) && ('lcg_mainpost' == $_POST['post_type']) ){
            
            // get the meta value
                $img_link           = !empty($_POST["img_link"]) ? esc_url_raw( $_POST["img_link"] ) : '';
                $img_tool           = !empty($_POST["img_tool"]) ? sanitize_text_field( $_POST["img_tool"] ) : '';
                $short_description  = !empty($_POST["short_description"]) ? sanitize_text_field( $_POST["short_description"] ) : '';
            
            //save the meta value
            update_post_meta($post_id, "img_link", $img_link);
            update_post_meta($post_id, "img_tool", $img_tool);
            update_post_meta($post_id, "short_description", $short_description);
            
        }

        // save the meta data if it is our post type lcg_mainpost post type
        if(!empty($_POST['post_type']) && ('lcg_shortcode' == $_POST['post_type']) ){

            $lcg_scode = !empty($_POST['lcg_scode']) ? lcg()::adl_enc_serialize($_POST['lcg_scode']) : lcg()::adl_enc_serialize(array());

            update_post_meta($post_id, "lcg_scode", $lcg_scode);
        }
    }

    //security check
    private function lcg_security_check($nonce_name, $action, $post_id){
        // checks are divided into 3 parts for readability.
        if ( !empty( $_POST[$nonce_name] ) && wp_verify_nonce( $_POST[$nonce_name], $action ) ) {
            return true;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything. returns false
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }
        // Check the user's permissions.
        if ( current_user_can( 'edit_post', $post_id ) ) {
            return true;
        }
        return false;
    }



}//end class