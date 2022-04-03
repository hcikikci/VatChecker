<?php
/**
 * Plugin Name: Super Banden Filter
 * Plugin URI: Plugin Author Link
 * Author: Halitcan Çıkıkçı
 * Author URI: Plugin Author Link
 * Description: This plugin does wonders
 * Version: 0.1.0
 * License: 0.1.0
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: prefix-plugin-name
*/
$products = new WP_Query( array(
    'post_type'      => array('product'),
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_query'     => array( array(
         'key' => '_visibility',
         'value' => array('catalog', 'visible'),
         'compare' => 'IN',
     ) ),
    'tax_query'      => array( array(
         'taxonomy'        => 'pa_width',
         'field'           => 'slug',
         'terms'           =>  array('165'),
         'operator'        => 'IN',
     ) )
 ) );
 
 // The Loop
 if ( $products->have_posts() ): while ( $products->have_posts() ):
     $products->the_post();
     echo($products->post->ID);
     $product_ids[] = $products->post->ID;
 endwhile;
     wp_reset_postdata();
 endif;
 
 // TEST: Output the Products IDs
 print_r(length($product_ids));

?>

