<?php
/**
 * Tea TO backend functions and definitions
 * 
 * @package TakeaTea
 * @subpackage Tea Theme Options
 * @since Tea Theme Options 1.4.3
 *
 * Plugin Name: Tea Theme Options
 * Version: 1.4.3
 * Plugin URI: https://github.com/Takeatea/tea_to_wp
 * Description: The Tea Theme Options (or "Tea TO") allows you to easily add professional looking theme options panels to your WordPress theme.
 * Author: Achraf Chouk
 * Author URI: http://takeatea.com/
 * License: GPL v3
 *
 * Tea Theme Options Plugin
 * Copyright (C) 2013, Achraf Chouk - ach@takeatea.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}


//---------------------------------------------------------------------------------------------------------//

//Usefull definitions for the Tea Theme Options
defined('TTO_VERSION')      or define('TTO_VERSION', '1.4.3');
defined('TTO_I18N')         or define('TTO_I18N', 'teathemeoptions');
defined('TTO_DURATION')     or define('TTO_DURATION', 86400);
defined('TTO_URI')          or define('TTO_URI', plugins_url().'/'.basename(dirname(__FILE__)).'/');
defined('TTO_PATH')         or define('TTO_PATH', plugin_dir_path(__FILE__));
defined('TTO_BASENAME')     or define('TTO_BASENAME', plugin_basename(__FILE__));
defined('TTO_ACTION')       or define('TTO_ACTION', 'tea_json_options');
defined('TTO_NONCE')        or define('TTO_NONCE', 'tea-ajax-nonce');


//---------------------------------------------------------------------------------------------------------//

/**
 * Tea Theme Option page.
 *
 * To get its own settings
 *
 * @since Tea Theme Options 1.4.3
 * @todo Special field:     Typeahead, Date, Geolocalisation
 * @todo Shortcodes panel:  Youtube, Vimeo, Dailymotion, Google Maps, Google Adsense,
 *                          Related posts, Private content, RSS Feed, Embed PDF,
 *                          Price table, Carousel, Icons
 */
class Tea_Theme_Options
{
    //Define protected vars
    protected $adminmessage;
    protected $breadcrumb = array();
    protected $capability = 'edit_pages';
    protected $categories = array();
    protected $can_upload = false;
    protected $current = '';
    protected $directory = array();
    protected $duration = 86400;
    protected $icon_small = '/img/teato/icn-small.png';
    protected $icon_big = '/img/teato/icn-big.png';
    protected $identifier;
    protected $includes = array();
    protected $index = null;
    protected $is_admin;
    protected $pages = array();
    protected $wp_contents = array();

    /**
     * Constructor.
     *
     * @uses add_filter()
     * @uses load_plugin_textdomain()
     * @uses register_activation_hook()
     * @uses register_uninstall_hook()
     * @uses wp_next_scheduled()
     * @uses wp_schedule_event()
     * @param string $identifier Define the plugin main slug
     *
     * @since Tea Theme Options 1.4.3
     */
    public function __construct($identifier = 'tea_theme_options')
    {
        //Check if we are in admin panel
        $this->setIsAdmin();
        $is_admin = $this->getIsAdmin();

        //Admin panel
        if ($is_admin)
        {
            //i18n
            load_plugin_textdomain(TTO_I18N, false, dirname(TTO_BASENAME));

            //Registration hooks
            register_activation_hook(__FILE__, array(&$this, '__adminInstall'));
            register_uninstall_hook(__FILE__, array('Tea_Theme_Options', '__adminUninstall'));

            //Page component
            require_once(TTO_PATH . 'classes/class-tea-pages.php');
            $teapages = new Tea_Pages($identifier);
        }

        //Define custom schedule
        if (!wp_next_scheduled('tea_task_schedule'))
        {
            wp_schedule_event(time(), 'hourly', 'tea_task_schedule');
        }

        //Register custom schedule filter
        add_filter('tea_task_schedule', array(&$this, '__cronSchedules'));

        //Page component
        require_once(TTO_PATH . 'classes/class-tea-custom-post-types.php');
        $teacustomposttypes = new Tea_Custom_Post_Types();
    }


    //--------------------------------------------------------------------------//

    /**
     * WORDPRESS USED HOOKS
     **/

    /**
     * Hook install plugin.
     *
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __adminInstall()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Nothing to do...
    }

    /**
     * Hook uninstall plugin.
     *
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __adminUninstall()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Delete configs
        _del_option('tea_config_pages');
        _del_option('tea_config_cpts');

        //Delete FlickR
        _del_option('tea_flickr_user_info');
        _del_option('tea_flickr_user_details');
        _del_option('tea_flickr_user_recent');
        _del_option('tea_flickr_connection_update');

        //Delete Instagram
        _del_option('tea_instagram_access_token');
        _del_option('tea_instagram_user_info');
        _del_option('tea_instagram_user_recent');
        _del_option('tea_instagram_connection_update');

        //Delete Twitter
        _del_option('tea_twitter_access_token');
        _del_option('tea_twitter_user_info');
        _del_option('tea_twitter_user_recent');
        _del_option('tea_twitter_connection_update');
    }

    /**
     * Display a warning message on the admin panel.
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __cronSchedules()
    {
        //Require file
        require_once(TTO_PATH . 'classes/fields/network/class-tea-fields-network.php');

        //Make the magic
        $field = new Tea_Fields_Network();
        $field->updateNetworks();
    }

    /**
     * CONTENTS METHODS
     **/

    /**
     * Get is_admin.
     *
     * @return bool $is_admin Define if we are in admin panel or not
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set is_admin.
     *
     * @param bool $is_admin Define if we are in admin panel or not
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function setIsAdmin()
    {
        $this->is_admin = is_admin() ? true : false;
    }
}

//Instanciate a new Tea_Theme_Options
$tea = new Tea_Theme_Options();

/**
 * Set a value into options
 *
 * @since Tea Theme Options 1.2.9
 */
function _del_option($option, $transient = false)
{
    //If a transient is asked...
    if ($transient)
    {
        //Delete the transient
        delete_transient($option);
    }

    //Delete value from DB
    delete_option($option);
}

/**
 * Return a value from options
 *
 * @since Tea Theme Options 1.2.1
 */
function _get_option($option, $default = '', $transient = false)
{
    //If a transient is asked...
    if ($transient)
    {
        //Get value from transient
        $value = get_transient($option);

        if (false === $value)
        {
            //Get it from DB
            $value = get_option($option);

            //Put the default value if not
            $value = false === $value ? $default : $value;

            //Set the transient for this value
            set_transient($option, $value, TTO_DURATION);
        }
    }
    //Else...
    else
    {
        //Get value from DB
        $value = get_option($option);

        //Put the default value if not
        $value = false === $value ? $default : $value;
    }

    //Return value
    return $value;
}

/**
 * Set a value into options
 *
 * @since Tea Theme Options 1.2.1
 */
function _set_option($option, $value, $transient = false)
{
    //If a transient is asked...
    if ($transient)
    {
        //Set the transient for this value
        set_transient($option, $value, TTO_DURATION);
    }

    //Set value into DB
    update_option($option, $value);
}
