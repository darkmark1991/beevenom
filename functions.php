<?php
//include "widgets.php";

/* Setup */
if ( ! function_exists( 'beevenom_setup' ) ) :
    function beevenom_setup()
    {
        $args = array(
            'flex-width'    => true,
            'width'         => 948,
            'flex-height'   => true,
            'height'        => 388,
            'default-image' => get_template_directory_uri() . '/images/header.png',
        );
        add_theme_support( 'custom-header', $args );

        register_nav_menus(array(
            'primary' => __('Primary Menu', 'beevenom'),
            'social' => __('Social Links Menu', 'beevenom'),
        ));
    }
endif;
add_action( 'after_setup_theme', 'beevenom_setup' );

/* Scripts & Styles */
function beevenom_scripts() {
//    wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css');
//    wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array ('jquery', 'jquery-ui') );
}
add_action( 'wp_enqueue_scripts', 'beevenom_scripts' );

/* Register Widget Areas */
function beevenom_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Front Central Widget Area', 'beevenom' ),
        'id'            => 'front-widgets',
        'description'   => __( 'Add widgets here to appear on the front page.', 'beevenom' ),
        'before_widget' => '<section id="%1$s" class="front-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Header Right Area', 'beevenom' ),
        'id'            => 'header-widgets',
        'description'   => __( 'Add widgets here to appear at the top of the header to the right.', 'beevenom' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'beevenom_widgets_init' );

/* Featured Image */
add_theme_support( 'post-thumbnails' );

/* Admin Bar */
show_admin_bar( false );

/* Front Page Meta */
function front_meta_settings() {
    global $post;
    $frontpage_id = get_option('page_on_front');
    if($post->ID == $frontpage_id):
        remove_post_type_support(
            'page',
            'editor'
        );
        remove_meta_box(
            'pageparentdiv',
            'page',
            'side'
        );
        add_meta_box(
            'Warning',
            'Front Page Warning:',
            'warning_meta_callback',
            'page',
            'normal',
            'core'
        );
    endif;
}
add_action('add_meta_boxes', 'front_meta_settings');

/* Warning Meta Box */
function warning_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'warning_meta_nonce');
    $warning_meta = get_post_meta( $post->ID );
    ?>
    <div>
        <div class="warning_meta_row">
            <div class="warning_meta_excerpt">
                <?php
                $content = get_post_meta( $post->ID, 'warning_meta_excerpt', true);
                $editor = 'warning_meta_excerpt';
                $settings = array(
                    'textarea_rows' =>  3,
                    'media_buttons' =>  false
                );

                wp_editor( $content, $editor, $settings );
                ?>
            </div>
        </div>
    </div>
    <?php
}

function warning_meta_save( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset($_POST['warning_meta_nonce']) && wp_verify_nonce($_POST['warning_meta_nonce'], basename( __FILE__ ))) ? true : false;

    if( $is_autosave || $is_revision || !$is_valid_nonce)
        return;

    if( isset($_POST['warning_meta_excerpt']))
        update_post_meta( $post_id, 'warning_meta_excerpt', sanitize_text_field($_POST['warning_meta_excerpt']));
}
add_action('save_post', 'warning_meta_save');



/* Beevenom Link Vidget */
class beevenom_link_widget extends WP_Widget {

    /* Register widget with WordPress */
    function __construct() {
        parent::__construct(
            'beevenom_link_widget', // Base ID
            __( 'BeeVenom Link Widget', 'text_domain' ), // Name
            array( 'description' => __( 'This widget will display a link and an image in asidebar.', 'text_domain' ), ) // Args
        );
    }

    /* Front-end display of widget */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        $title = '';
        $excerpt = '';
        $page_url = '';
        $img_url = '';
        if(empty($instance['title']) || empty($instance['excerpt']) || empty($instance['page_url']) || empty($instance['img_url'])){
            return;
        } else {
            $title = $instance['title'];
            $excerpt = $instance['excerpt'];
            $page_url = $instance['page_url'];
            $img_url = $instance['img_url'];
        }
        ?>
        <a href="<?php _e($page_url); ?>" class="beevenom-link-wrap">
            <span class="beevenom-link-text-wrap">
                <h1><?php _e($title); ?></h1>
                <p><?php _e($excerpt); ?></p>
            </span>
            <img src="<?php echo $img_url; ?>" alt="" class="beevenom-link-image">
        </a>
        <?php
        echo $args['after_widget'];
    }

    /* Back-end widget form */
    public function form( $instance ) {
        $title      =! empty($instance['title'])    ?  __($instance['title'])    : __('', 'beevenom');
        $excerpt    =! empty($instance['excerpt'])  ?  __($instance['excerpt'])  : __('', 'beevenom');
        $page_url   =! empty($instance['page_url']) ?  __($instance['page_url']) : __('', 'beevenom');
        $img_url    =! empty($instance['img_url'])  ?  __($instance['img_url'])  : __('', 'beevenom');
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'excerpt' ) ); ?>"><?php _e( esc_attr( 'Description:' ) ); ?></label>
            <textarea class="widefat i18n-multilingual" id="<?php echo esc_attr( $this->get_field_id( 'excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'excerpt' ) ); ?>" type="text"><?php echo esc_attr( $excerpt ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'page_url' ) ); ?>"><?php _e( esc_attr( 'Page URL:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'page_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_url' ) ); ?>" type="url" value="<?php echo esc_attr( $page_url ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'img_url' ) ); ?>"><?php _e( esc_attr( 'Image URL:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'img_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'img_url' ) ); ?>" type="url" value="<?php echo esc_attr( $img_url ); ?>">
        </p>
        <?php
    }

    /* Sanitize widget form values as they are saved */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        if ($ret = parse_url($new_instance['page_url']))
            if (!isset($ret["scheme"]))
                $new_instance['page_url'] = "http://{$new_instance['page_url']}";

        $instance['title']    = (!empty($new_instance['title']))    ? strip_tags($new_instance['title'])    : '';
        $instance['excerpt']  = (!empty($new_instance['excerpt']))  ? strip_tags($new_instance['excerpt'])  : '';
        $instance['page_url'] = (!empty($new_instance['page_url'])) ? strip_tags($new_instance['page_url']) : '';
        $instance['img_url']  = (!empty($new_instance['img_url']))  ? strip_tags($new_instance['img_url'])  : '';

        return $instance;
    }

}

/* Register Beevenom Link Vidget */
function register_beevenom_link_widget() {
    register_widget( 'beevenom_link_widget' );
}
add_action( 'widgets_init', 'register_beevenom_link_widget' );



/* Beevenom Link Vidget */
class beevenom_social_widget extends WP_Widget {

    /* Register widget with WordPress */
    function __construct() {
        parent::__construct(
            'beevenom_social_widget', // Base ID
            __( 'BeeVenom Social Widget', 'text_domain' ), // Name
            array( 'description' => __( 'This widget will display social and language icons in asidebar.', 'text_domain' ), ) // Args
        );
    }

    /* Front-end display of widget */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if(empty($instance['fb_url']))
             $fb_url = '';
        else $fb_url = esc_attr($instance['fb_url']);

        if(empty($instance['disp_lang']))
             $disp_lang = '';
        else $disp_lang = esc_attr($instance['disp_lang']);

        ?>
        <a href="<?php _e($fb_url); ?>" class="beevenom-social-wrap">lol</a>
        <?php
        if($fb_url != ''){

        }
        if($disp_lang){
            echo 1;
        }
        echo $args['after_widget'];
    }

    /* Back-end widget form */
    public function form( $instance ) {
        $fb_url     =! empty($instance['fb_url'])       ?  __($instance['fb_url'])      : __('', 'fb_url');
        $disp_lang  =! empty($instance['disp_lang'])    ?  __($instance['disp_lang'])   : __('', 'disp_lang');
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'fb_url' ) ); ?>"><?php _e( esc_attr( 'FB Page URL:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'fb_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fb_url' ) ); ?>" type="text" value="<?php echo esc_attr( $fb_url ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'disp_lang' ) ); ?>"><?php _e( esc_attr( 'Display Languages:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'disp_lang' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'disp_lang' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $disp_lang ); ?>>
        </p>
        <?php
    }

    /* Sanitize widget form values as they are saved */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        if ($ret = parse_url($new_instance['page_url']))
            if (!isset($ret["scheme"]))
                $new_instance['page_url'] = "http://{$new_instance['page_url']}";

        $instance['fb_url']     = (!empty($new_instance['fb_url']))     ? strip_tags($new_instance['fb_url'])       : '';
        $instance['disp_lang']  = (!empty($new_instance['disp_lang']))  ? strip_tags($new_instance['disp_lang'])    : '';

        return $instance;
    }

}

/* Register Beevenom Link Vidget */
function register_beevenom_social_widget() {
    register_widget( 'beevenom_social_widget' );
}
add_action( 'widgets_init', 'register_beevenom_social_widget' );