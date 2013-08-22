<?php
/**
 * Tea TO backend functions and definitions
 * 
 * @package TakeaTea
 * @subpackage Tea Theme Options
 * @since Tea Theme Options 1.4.4
 *
 */

if (!defined('ABSPATH') || !defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

//Delete configs
delete_option('tea_config_pages');
delete_option('tea_config_cpts');

//Delete FlickR
delete_option('tea_flickr_user_info');
delete_option('tea_flickr_user_details');
delete_option('tea_flickr_user_recent');
delete_option('tea_flickr_connection_update');

//Delete Instagram
delete_option('tea_instagram_access_token');
delete_option('tea_instagram_user_info');
delete_option('tea_instagram_user_recent');
delete_option('tea_instagram_connection_update');

//Delete Twitter
delete_option('tea_twitter_access_token');
delete_option('tea_twitter_user_info');
delete_option('tea_twitter_user_recent');
delete_option('tea_twitter_connection_update');
