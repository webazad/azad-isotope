<?php
/* 
Plugin Name: Azad Isotope
Description: This plugin is a widget to display any type of posts in a isotop format.
Plugin URI: gittechs.com/plugin/azad-isotop
Author: Md. Abul Kalam Azad
Author URI: gittechs.com/author
Author Email: webdevazad@gmail.com
Version: 1.0.0
License: GPL2v
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: azad-isotope
Domain Path: /languages
@package: azad-isotope
*/ 

// EXIT IF ACCESSED DIRECTLY
defined('ABSPATH') || exit;

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$plugin_data = get_plugin_data( __FILE__ );

define( 'AZAD_ISOTOPE_NAME', $plugin_data['Name'] );
define( 'AZAD_ISOTOPE_VERSION', $plugin_data['Version'] );
define( 'AZAD_ISOTOPE_TEXTDOMAIN', $plugin_data['TextDomain'] );
define( 'AZAD_ISOTOPE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AZAD_ISOTOPE_URL', plugin_dir_url( __FILE__ ) );
define( 'AZAD_ISOTOPE_BASENAME', plugin_basename( __FILE__ ) );

if(! class_exists('Azad_Isotop')){
    final class Azad_Isotop extends WP_Widget{
        public function __construct(){
            parent::__construct(
                'azad_isotop',
                esc_html__( 'Azad Isotop', AZAD_ISOTOPE_TEXTDOMAIN ),
                array(
                    //'classname'=>'azad-widget',
                    'description' => esc_html__( 'To display latest posts.', AZAD_ISOTOPE_TEXTDOMAIN )
                )
            );
            add_action( 'wp_enqueue_scripts', array( $this, 'azad_isotop_scripts' ) );
        }
		public function azad_isotop_scripts(){
            // STYLE SCRIPTS
            wp_register_style( 'azad-latest-posts-style', plugin_dir_url(__FILE__). 'assets/css/main-style.min.css', null, AZAD_ISOTOPE_VERSION, 'all' );
            wp_enqueue_style( 'azad-latest-posts-style' );

            // JAVA SCRIPTS
            wp_register_script( 'azad-isotop', plugins_url( 'assets/js/isotope.pkgd.min.js', __FILE__ ), array('jquery'), AZAD_ISOTOPE_VERSION, true );
            wp_enqueue_script('azad-isotop');

            wp_register_script( 'azad-isotop-activation', plugins_url( 'assets/js/azad-isotop-activation.js', __FILE__ ), array('jquery'), AZAD_ISOTOPE_VERSION, true );
            wp_enqueue_script('azad-isotop-activation');
        }
        public function widget($args,$instance){
            extract($args);

            $title      = apply_filters( 'azad_latest_posts_title', $instance['title'] );
            $category   = $instance['category'];
            $count      = $instance['count'];
            
            
            echo $before_widget;

            if($title){
                echo $before_title . $title . $after_title;
            }            
            global $post;
            $args = array(
                'category_name'=> $category ,
                'posts_per_page'=> $count
            );
            $posts = get_posts($args);

            echo '<div class="button-group filter-button-group">';
            echo '<button data-filter="*">show all</button>';
                $categories = get_categories(); 
                foreach( $categories as $category ) : ?>
                    <button data-filter=".<?php echo $category->slug; ?>"><?php echo $category->name; ?></button>
                <?php endforeach;
            echo '</div>';

            //echo '<div class="azad-isotope" style="width:90%;margin:0 auto;display:flex;flex-direction:column;">';

            if(count($posts)>0){
                $output .= '<div class="grid-container">';
                $output .= '<div class="grid">';
                foreach($posts as $post ) : setup_postdata($post);
                    $output .= '<div style="" class="grid-item '. the_filtered_clasess(get_the_id()) .'">';
                        //$output .= '<a class="" href="' . get_permalink() . '">' . get_the_post_thumbnail($post->ID,'azad-thumb',array('class'=>'azad_responsive'))';
                        if(has_post_thumbnail()){
                            $output .= '<div class="azad-content" style="background:url('.get_the_post_thumbnail_url(get_the_ID(), 'post-thumbnail' ).');background-repeat: no-repeat;
                            background-position: center;background-size:cover;">';
                            $output .= '<div class="grid-content"><h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3></div>';
                            $output .= '</div>';
                           // $output .= '</a>';
                        }
                    $output .= '</div>';
                endforeach;            
                wp_reset_query();
                $output .= '</div>';
                $output .= '</div>';
            }
            
            echo $output;
            
            echo $after_widget;
        }
        public function form( $instance ){ 
            $defaults = array(
                'title'     => 'Azad Isotop',
                'count'     => 4,
                'category'  => 'uncategorized'
            );
            $instance = wp_parse_args( (array)$instance, $defaults );
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">Widget Title</label>
                <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('count'); ?>">Count</label>
                <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>"/>
            </p>
        <?php
        }
        public function update( $new_instance, $old_instance ){
            $instance = array();
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['category'] = strip_tags($new_instance['category']);
            $instance['count'] = strip_tags($new_instance['count']);
            return $instance;
        }
    }
}
if(! function_exists('azad_isotop')){
    function azad_isotop(){
        register_widget('Azad_Isotop');
    }
}
add_action('widgets_init','azad_isotop');

function the_filtered_clasess($id){
    $cats = get_the_category($id);
    foreach($cats as $cat){
        return $cat->slug . ' ';
    }
}