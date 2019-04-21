<?php
/*
Plugin Name: JHL Slack Auto Invite
Description: Automatically invite someone to a slack channel
Version: 1.0.0
Author: Jason Lawton <jason@jasonlawton.com>
*/

define( 'JHL_SAI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JHL_SAI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action( 'admin_menu', 'jhl_sai_add_admin_menu' );
add_action( 'admin_init', 'jhl_sai_settings_init' );

function jhl_sai_add_admin_menu(  ) { 

	add_options_page( 'JHL Slack Auto Invite', 'JHL Slack Auto Invite', 'manage_options', 'jhl_sai', 'jhl_sai_options_page' );

}

function jhl_sai_settings_init(  ) { 

	register_setting( 'pluginPage', 'jhl_sai_settings' );

	add_settings_section(
		'jhl_sai_pluginPage_section', 
		__( 'Settings for JHL Slack Auto Invite', 'jhl_sai' ), 
		'jhl_sai_settings_section_callback', 
		'pluginPage'
	);

}

function jhl_sai_settings_section_callback(  ) { 

	echo __( 'Use this page to manage settings', 'jhl_sai' );

}


function jhl_sai_options_page(  ) { 
    
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die('Go away.');
    }

    if ( !empty( $_POST ) ) {
        // if (!wp_verify_nonce( '_wp_nonce', 'jhl_sai_option_page_update' )) {
        //     wp_die('Nonce verification failed');
        // }

        if (isset($_POST['jhl_sai_url'])) {
            update_option('jhl_sai_url', $_POST['jhl_sai_url']);
            $value = $_POST['jhl_sai_url'];
        }

        if (isset($_POST['jhl_sai_token'])) {
            update_option('jhl_sai_token', $_POST['jhl_sai_token']);
            $value = $_POST['jhl_sai_token'];
        }

        if (isset($_POST['jhl_sai_channel'])) {
            update_option('jhl_sai_channel', $_POST['jhl_sai_channel']);
            $value = $_POST['jhl_sai_channel'];
        }
    }

    include 'options-form.php';

}

// shortcode for invite form
function jhl_sai_shortcode() {
    
    ob_start();
    ?>
    <form id="jhl_sai_form" action="">
        <label for="jhl_sai_email">Email:</label>
        <input type="email" id="jhl_sai_email" name="jhl_sal_email">
        <br>
        <button>Request Invite</button>
    </form>

    <script>
    jQuery( document ).ready(function() {
        jQuery( '#jhl_sai_form button' ).on( 'click', function( e ) {
            e.preventDefault();
            var email = jQuery('#jhl_sai_form #jhl_sai_email' ).val();
            if ( !email ) {
                alert('Enter an email address');
                return;
            }
            // make api call
            // post(url, data, success)
            jQuery.post(
                '<?php echo get_site_url(); ?>/wp-json/jhl-sai/v1/request-invite/',
                { email : email },
                function( data ) {
                    // console.log( data );
                    if ( data.success == 1 ) {
                        if ( data.messages[0].ok == true ) {
                            alert('You should receive an email with your invitation.');
                            jQuery('#jhl_sai_form #jhl_sai_email').val('');
                        } else if ( data.messages[0].ok == false ) {
                            if ( data.messages[0].error == 'already_invited' ) {
                                alert('This email has already been invited');
                            } else if ( data.messages[0].error == 'already_in_team' ) {
                                alert('That email has already accepted an invite');
                            } else {
                                alert('Error: ' + data.messages[0].error);
                            }
                        }
                    } else {
                        alert('There was an error, try again later');
                    }
                }
            );
        });
    });
    </script>
    <?php
    $content = ob_get_clean();
    return $content;

}
add_shortcode( 'jhl_sai', 'jhl_sai_shortcode' );


// api
// set up api endpoint
add_action( 'rest_api_init', function () {

    // post email parameter to /wp-json/jhl-sai/v1/request-invite/
    register_rest_route( 'jhl-sai/v1', '/request-invite/', array(
        'methods'  => 'post',
        'callback' => 'jhl_sai_request_invite',
    ) );

} );

// api functions
function jhl_sai_request_invite( $data ) {

    // get options
    $jhl_sai_url     = get_option( 'jhl_sai_url', '' ) . '/api/users.admin.invite?&_x_version_ts=' . time();
    $jhl_sai_token   = get_option( 'jhl_sai_token', '' );
    $jhl_sai_channel = get_option( 'jhl_sai_channel', '' );
    $email           = $data['email'];

    $response = wp_remote_post( $jhl_sai_url, [
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => [],
        'cookies'     => [],
        'body'        => [
            'token'      => $jhl_sai_token,
            'channel'    => $jhl_sai_channel,
            'email'      => $email,
            'set_active' => true,
            '_attempts'  => 1,
        ],
    ] );
 
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        return [
            'success'  => 0,
            'messages' => [
                'Something went wrong',
                $error_message
            ]
        ];
    } else {
        return [
            'success'  => 1,
            'messages' => [
                json_decode( wp_remote_retrieve_body( $response ), true )
            ]
        ];
    }

}