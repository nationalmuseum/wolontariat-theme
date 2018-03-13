<?php 
/** @constant string THEME_NAME **/
define( 'THEME_NAME', get_option('stylesheet') );

function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), '1.0.2' );
           
    // First de-register the main stylesheet
    wp_deregister_style( 'style-css' );

    // Then add it again, using your custom version number
    wp_register_style( 'style-css', get_stylesheet_uri(), array(), "1.0.2" );

    //finally enqueue it again
    wp_enqueue_style( 'style-css');
} 
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/**
 * Theme setup
 */
function realhero_setup() {
	/**
	 * Theme text domain
	 */
	load_child_theme_textdomain( THEME_NAME, get_stylesheet_directory() . '/languages' );	
}
add_action( 'after_setup_theme', 'realhero_setup' );

/**
 * Register our sidebars and widgetized areas.
 *
 */
function wolontariat_widgets_init() {

    register_sidebar( array(
        'name'          => 'Pasek po prawej',
        'id'            => 'home_right_1',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );

}
add_action( 'widgets_init', 'wolontariat_widgets_init' );


/**
 * Custom script
 */
 function my_scripts_method() {
    wp_enqueue_script(
        'custom-script',
        get_stylesheet_directory_uri() . '/js/muzeariat.js',
        array( 'jquery' ),
        '1.2'
    );

    if ( !is_admin() ) {
        /** */
		wp_localize_script( 'custom-script', 'ajax', array(
            'url' =>            admin_url( 'admin-ajax.php' ),
            'ajax_nonce' =>     wp_create_nonce( 'noncy_nonce' ),
            'assets_url' =>     get_stylesheet_directory_uri(),
		) );
    }	
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

/**
 * Ajax newsletter
 * 
 * @url http://www.thenewsletterplugin.com/forums/topic/ajax-subscription
 */
function realhero_ajax_subscribe() {
    check_ajax_referer( 'noncy_nonce', 'nonce' );
    $data = urldecode( $_POST['data'] );

    if ( !empty( $data ) ) :
        $data_array = explode( "&", $data );
        $fields = [];
        foreach ( $data_array as $array ) :
            $array = explode( "=", $array );
            $fields[ $array[0] ] = $array[1];
        endforeach;
    endif;

    if ( !empty( $fields ) ) :
        global $wpdb;
		
		// check if already exists
		
		/** @var int $count **/
		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter WHERE email = %s", $fields['ne'] ) );
		
		if( $count > 0 ) {
	        $output = array(
	            'status'    => 'error',
	            'msg'       => __( 'Already in a database.', THEME_NAME )
	        );
        } elseif( !defined( 'NEWSLETTER_VERSION' ) ) {
            $output = array(
	            'status'    => 'error',
	            'msg'       => __( 'Please install & activate newsletter plugin.', THEME_NAME )
	        );           
        } else {
            /**
             * Generate token
             */
            
            /** @var string $token */
            $token =  wp_generate_password( rand( 10, 50 ), false );


	        $wpdb->insert( $wpdb->prefix . 'newsletter', array(
	                'email'         => $fields['ne'],
	                'status'        => $fields['na'],
                    'http_referer'  => $fields['nhr'],
                    'token'         => $token,
	            )
            );

            $opts = get_option('newsletter');

            $opt_in = (int) $opts['noconfirmation'];

            // This means that double opt in is enabled
            // so we need to send activation e-mail
            if ($opt_in == 0) {
                $newsletter = Newsletter::instance();
                $user = NewsletterUsers::instance()->get_user( $wpdb->insert_id );

                NewsletterSubscription::instance()->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
            }

	        $output = array(
	            'status'    => 'success',
	            'msg'       => __( 'Thank you!', THEME_NAME )
	        );	
		}
		
    else :
        $output = array(
            'status'    => 'error',
            'msg'       => __( 'An Error occurred. Please try again later.', THEME_NAME  )
        );
    endif;
	
    wp_send_json( $output );
}
add_action( 'wp_ajax_realhero_subscribe', 'realhero_ajax_subscribe' );
add_action( 'wp_ajax_nopriv_realhero_subscribe', 'realhero_ajax_subscribe' );