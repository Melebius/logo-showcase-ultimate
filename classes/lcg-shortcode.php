<?php 
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access is not allow' );
class Lcg_shortcode
{
	public function __construct() {
		//shortcode hook
		add_shortcode("logo_showcase", array( $this, 'lcg_shortcode_creat' ) );
	}

	//shortcode cr

    /**
     * @param $atts
     * @param null $content
     * @return string
     */
    public function lcg_shortcode_creat($atts, $content = null ) {
		ob_start();

		extract( shortcode_atts(array(
                    'id' => "",
                ), $atts
                )
            );

		if ( empty( $id) ) {
                return esc_html__('No shortcode ID provided', LCG_TEXTDOMAIN);
        }
        $this->lcg_enqueue_files();

        $lcg_value = get_post_meta($id,'lcg_scode',true);

        $data_encoded   = ( !empty($lcg_value) ) ? lcg()::adl_enc_unserialize( $lcg_value ) : array();
        extract($data_encoded);
        $rand_id            = rand();
        $cg_title 			= !empty($cg_title) ? $cg_title : '';
        $lcg_type 			= !empty($lcg_type) ? $lcg_type : 'latest';
        $layout   			= !empty($layout) ? $layout : 'carousel';
        $c_theme  			= !empty($c_theme) ? $c_theme : 'carousel-theme-1';
        $g_theme  			= !empty($g_theme) ? $g_theme : 'grid-theme-1';
        $image_crop 		= !empty($image_crop) ? $image_crop : "yes";
		$upscale		   	= !empty($upscale) ? $upscale : "yes";
        $image_width 		= !empty($image_width) ? $image_width : 185; // Image width for cropping
        $image_height 		= !empty($image_height) ? $image_height : 119;
        $target				= !empty($target) ? $target : '_blank';
        $c1_nav             = !empty($c1_nav)? $c1_nav : 'yes';
        $tooltip_show       = !empty($tooltip)? $tooltip : 'yes';
        $c1_nav_position    = !empty($c1_nav_position)? $c1_nav_position : 'top_right';
        $c2_nav             = !empty($c2_nav) ? $c2_nav : 'yes';
        $g_columns          = !empty($g_columns) ? intval($g_columns) : 6;
        $g_columns_tablet   = !empty($g_columns_tablet) ? intval($g_columns_tablet) : 4;
        $g_columns_mobile   = !empty($g_columns_mobile) ? intval($g_columns_mobile) : 2;
        $tooltip_posi       = !empty($tooltip_posi) ? $tooltip_posi : "top";
        $total_logos        = !empty($total_logos) ? intval($total_logos) : 12;
        
        //carousel settings
        $carousel_pagination         = !empty($carousel_pagination) ? $carousel_pagination : 'no';
        $A_play                      = !empty($A_play) ? $A_play : 'yes';
        $navigation                  = !empty($navigation) ? $navigation : 'yes';
        $scrool                      = ! empty( $scrool ) ? $scrool : 'per_item';
        $stop_hover                  = ! empty( $stop_hover ) ? $stop_hover : 'yes';
        $marquee                     = ! empty( $marquee ) ? $marquee : 'yes';

        $paged 			    = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        
        
        if( 'carousel' == $layout ) {
            $theme = $c_theme;
        } else {
            $theme = $g_theme;
        }

        $marquee_class = '';
        if( 'yes' == $marquee ) {
            $marquee_class = 'wpwax-lsu-carousel-marquee';
        }
        
        $args = array();
        $common_args = array(
            'post_type'      => 'lcg_mainpost',
            'posts_per_page' => $total_logos,
            'paged'          => $paged
        );

        if ($lcg_type == "latest") {
            $args = $common_args;
        }

        elseif ($lcg_type == "category") {
            $category_args = array(
                'tax_query' => array(
                    array(
                        'taxonomy' => 'lcg_category',
                        'field' => 'term_id',
                        'terms' => ( !empty($custom_terms) ) ?  (array) $custom_terms : null,
                    )
                )
            );
            $args = array_merge($common_args, $category_args);
        }

        elseif ($lcg_type == "older") {
            $older_args = array(
                'orderby'     => 'date',
                'order'       => 'ASC'
            );
            $args = array_merge($common_args, $older_args);
        }

        elseif ($lcg_type == "rand") {
            $rand_args = array(
                'orderby'     => 'rand',);
            $args = array_merge($common_args, $rand_args);
        }

        elseif ($lcg_type == "title_desc") {
            $title_desc = !empty($title_desc)  ?  (array) $title_desc : null;
            if( null == $title_desc) {
                $title_desc_args = array(
                    'orderby' => 'title',
                    'order' => 'DESC'
                );
                $args = array_merge($common_args, $title_desc_args);
            } else {
                $title_desc_args = array(
                    'orderby' => 'title',
                    'order' => 'DESC',
                    'tax_query' => array(
                    array(
                        'taxonomy' => 'lcg_category',
                        'field' => 'term_id',
                        'terms' => ( !empty($title_desc) ) ?  (array) $title_desc : null,
                    )
                )
                );
                $args = array_merge($common_args, $title_desc_args);
            }
        }

        elseif ($lcg_type == "title_asc") {
            $title_asc = !empty($title_asc)  ?  (array) $title_asc : null;
            if(null == $title_asc) {
                $title_asc_args = array(
                    'orderby' => 'title',
                    'order' => 'ASC'
                );
                $args = array_merge($common_args, $title_asc_args);
            } else {
                $title_asc_args = array(
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'lcg_category',
                            'field' => 'term_id',
                            'terms' => ( !empty($title_asc) ) ?  (array) $title_asc : null,
                        )
                )
                );
                $args = array_merge($common_args, $title_asc_args);
            }
        }

        elseif ($lcg_type == "logosbyid") {
            $logosbyid_args = array(
                'post__in' => (!empty($logos_byid) ? explode(',', $logos_byid) : 0)
            );
            $args = array_merge($common_args, $logosbyid_args);
        }

        elseif ($lcg_type == "logosbyyear") {
            $logosbyyear_args = array(
                'year' => !empty($logos_from_year) ? intval($logos_from_year) : 0
            );
            $args = array_merge($common_args, $logosbyyear_args);
        }

        elseif ($lcg_type == "logosbymonth") {
            $logosbymonth_args = array(
                'monthnum' => !empty($logos_from_month) ? intval($logos_from_month) : 0,
                'year' 	   => !empty($logos_from_month_year) ? intval($logos_from_month_year) : 0
            );
            $args = array_merge($common_args, $logosbymonth_args);
        }

        else {
            $args = $common_args;
        }

	    $adl_logo = new WP_Query( $args );
        

        if ( $adl_logo->have_posts() ) { ?>

        <h4 class="wpwax-lsu-title"><?php echo ! empty( $cg_title ) ? $cg_title : ''; ?></span> </h4>

            <div class="wpwax-lsu-ultimate wpwax-lsu-grid <?php echo $marquee_class; ?> wpwax-lsu-<?php echo $theme; ?> <?php echo ( 'grid' == $layout ) ? 'wpwax-lsu-logo-col-lg-' . $g_columns . ' wpwax-lsu-logo-col-md-' . $g_columns_tablet . ' wpwax-lsu-logo-col-sm-' . $g_columns_mobile . '' : 'wpwax-lsu-carousel wpwax-lsu-' . $theme . ' wpwax-lsu-carousel-nav-top'; ?>"
            <?php if( 'carousel' == $layout ) { ?>
                data-lsu-items="5"
                data-lsu-margin="20" 
                data-lsu-loop="false" 
                data-lsu-perslide="1"
                data-lsu-speed="10000"
                data-lsu-autoplay='
                <?php if( 'yes' == $A_play ) { ?>
                {
                    "delay": "0",
                    "pauseOnMouseEnter": true,
                    "disableOnInteraction": false,
                    "stopOnLastSlide": true,
                    "reverseDirection": false
                }
                <?php } else { ?>
                    false
                <?php } ?>
                '
                data-lsu-responsive ='{
                    "0": {"slidesPerView": "<?php echo $c_mobile; ?>",  "slidesPerGroup": "<?php echo 'per_item' == $scrool ? '1' : $c_mobile; ?>", "spaceBetween": "15"}, 
                    "768": {"slidesPerView": "<?php echo $c_tablet; ?>",  "slidesPerGroup": "<?php echo 'per_item' == $scrool ? '1' : $c_tablet; ?>", "spaceBetween": "15"}, 
                    "979": {"slidesPerView": "<?php echo $c_desktop_small; ?>",  "slidesPerGroup": "<?php echo 'per_item' == $scrool ? '1' : $c_desktop_small; ?>", "spaceBetween": "20"}, 
                    "1199": {"slidesPerView": "<?php echo $c_desktop; ?>",  "slidesPerGroup": "<?php echo 'per_item' == $scrool ? '1' : $c_desktop; ?>", "spaceBetween": "30"}
                }'
            <?php } ?>   
            >

                <div class="<?php echo ( 'grid' == $layout ) ? 'wpwax-lsu-content' : 'swiper-wrapper'; ?>">

                    <?php
                    while ($adl_logo->have_posts()) : $adl_logo->the_post();
                        $tooltip  = get_post_meta(get_the_id(), 'img_tool', true);
                        $img_link = get_post_meta(get_the_id(), 'img_link', true);
                        $image_id = get_post_thumbnail_id();
                        $im = wp_get_attachment_image_src($image_id, 'full', true);
                        //$img = aq_resize($im[0], $image_width, $image_height,true,true, $upscale);
                        $thumb = get_post_thumbnail_id();
                        // crop the image if the cropping is enabled.
                        if (!empty($image_crop) && 'no' != $image_crop) {
                            $lcg_img = lcg_image_cropping($thumb, $image_width, $image_height, true, 100)['url'];
                        } else {
                            $aazz_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large');
                            $lcg_img = $aazz_thumb['0'];
                        }
                    ?>

                    <?php

                    include LCG_PLUGIN_DIR . 'template/theme/' . $theme .'.php'; 

                    endwhile;
                    wp_reset_postdata();
                    ?>
        
                </div>
                <?php 
                if( 'carousel' == $layout && 'yes' == $navigation ) {
                    include LCG_PLUGIN_DIR . 'template/navigation.php'; 
                }
                if( 'carousel' == $layout && 'yes' == $carousel_pagination ) {
                    include LCG_PLUGIN_DIR . 'template/carousel-pagination.php'; 
                }
                
                ?>
            </div>

            <?php
                        
        }
		$true =  ob_get_clean();
		return $true;
	}

	//enqueue all files to shortcode
	public function lcg_enqueue_files () {
		wp_enqueue_style('lcg-style');
        wp_enqueue_style('lcg-tooltip');
        wp_enqueue_style('lcg-swiper-min-css');

		wp_enqueue_script('main-js');
        wp_enqueue_script('lcg-swiper-min-js');
        wp_enqueue_script('lcg-popper-js');
        wp_enqueue_script('lcg-tooltip-js');
	}
}