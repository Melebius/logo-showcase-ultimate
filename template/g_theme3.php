<!-- wpwax logo Grid Theme-3  -->
<h4 class="wpwax-lsu-title">Grid Theme <span class="wpwax-lsu-serial">#3</span> </h4>
<div
    class="wpwax-lsu-ultimate wpwax-lsu-grid wpwax-lsu-grid-theme-3 wpwax-lsu-logo-col-lg-3 wpwax-lsu-logo-col-md-2 wpwax-lsu-logo-col-sm-1">

    <div class="wpwax-lsu-content">
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
        <div class="wpwax-lsu-item wpwax-lsu-item__bordered">

            <div class="wpwax-lsu-item-inner">

                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Conpany Name Sovware">

                    <img src="<?php echo !empty($lcg_img) ? $lcg_img : ''; ?>" alt="<?php the_title(); ?>">
                    
                </a>

            </div>

        </div>
        
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>

</div>