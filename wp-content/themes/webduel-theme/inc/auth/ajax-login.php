<?php

//   ajax login 
function ajax_login_init()
{
    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action('wp_ajax_nopriv_ajaxlogin', 'ajax_login');
}

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}

function ajax_login()
{
    // First check the nonce, if it fails the function will break
    check_ajax_referer('ajax-login-nonce', 'security');

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;
    // $redirectLink = $_POST['redirectLink']; 

    $user_signon = wp_signon($info, false);
    if (is_wp_error($user_signon)) {
        echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.')));
    } else {
        echo json_encode(array('loggedin' => true, 'message' => __('Sign in successful, redirecting...')));
        // wp_redirect($redirectLink);
    }
    die();
}

// redirect a user to home page if logged in
function add_login_check()
{
    if (is_user_logged_in()) {
        if (is_page('sign-in') or is_page('create-account')) {
            wp_redirect(home_url());
            exit;
        }
    }
}

add_action('wp', 'add_login_check');
