<?php
/**
 * Tea TO backend functions and definitions
 * 
 * @package TakeaTea
 * @subpackage Tea Custom Post Types
 * @since Tea Theme Options 1.4.2
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}


//---------------------------------------------------------------------------------------------------------//

/**
 * Tea Custom Post Types
 *
 * To get its own Custom Post Types
 *
 * @since Tea Theme Options 1.4.2
 *
 */
class Tea_Custom_Post_Types
{
    //Define protected vars

    /**
     * Constructor.
     *
     * @since Tea Theme Options 1.4.2
     */
    public function __construct()
    {
        //Build CPT
        $this->buildCustomPostTypes();
    }


    //--------------------------------------------------------------------------//

    /**
     * MAIN FUNCTIONS
     **/

    /**
     * Register custom post types.
     *
     * @uses add_action()
     *
     * @since Tea Theme Options 1.4.2
     */
    public function buildCustomPostTypes()
    {
        //Register global action hook
        add_action('init', array(&$this, '__buildMenuCustomPostType'));

        //Register custom supports action hook
        /*if ($this->getIsAdmin())
        {
            add_action('save_post', array(&$this, '__save'));

            if (!empty($this->customs))
            {
                add_action('admin_init', array(&$this, '__customs'));
            }

            //Register icons action hook
            if (!empty($this->images))
            {
                add_action('admin_head', array(&$this, '__icons'));
            }

            //Register columns action hook
            if (!empty($this->columns))
            {
                add_action('manage_edit-' . $this->posttype . '_columns', array(&$this, '__columns'));
            }

            //Register dashboard action hook
            if ($this->dashboard)
            {
                add_action('wp_dashboard_setup', array(&$this, '__dashboard'));
            }
        }*/
    }

    /**
     * Hook building menus for CPTS.
     *
     * @uses register_post_type()
     *
     * @since Tea Theme Options 1.4.2
     */
    public function __buildMenuCustomPostType()
    {
        //Get all registered pages
        $cpts = _get_option('tea_config_cpts', array());

        //Check if we have some CPTS to initialize
        if (empty($cpts))
        {
            return false;
        }

        //Iterate on each cpt
        foreach ($cpts as $key => $cpt)
        {
            //Check if no master page is defined
            if (!isset($cpt['title']) || empty($cpt['title']))
            {
                echo sprintf(__('Something went wrong in your parameters definition: no title defined for you <b>%s</b> custom post type. Please, try again by filling the form properly.', TTO_I18N), $key);
                return false;
            }

            //Special case: define a post as title to edit default post component
            if ('post' == strtolower($cpt['title']))
            {
                continue;
            }

            //Treat arrays
            $caps = isset($cpt['capability_type']) && !empty($cpt['capability_type']) ? array_keys($cpt['capability_type']) : 'post';
            $caps = is_array($caps) && 1 == count($caps) && 'post' == $caps[0] ? 'post' : $caps;
            $sups = isset($cpt['supports']) && !empty($cpt['supports']) ? array_keys($cpt['supports']) : array('title', 'editor', 'thumbnail');
            $taxs = isset($cpt['taxonomies']) && !empty($cpt['taxonomies']) ? array_keys($cpt['taxonomies']) : array('category', 'post_tag');

            //Build labels
            $labels = array(
                'name' => $cpt['title'],
                'singular_name' => isset($cpt['singular']) ? $cpt['singular'] : $cpt['title'],
                'menu_name' => isset($cpt['menu_name']) ? $cpt['menu_name'] : $cpt['title'],
                'all_items' => isset($cpt['all_items']) ? $cpt['all_items'] : $cpt['title'],
                'add_new' => isset($cpt['add_new']) ? $cpt['add_new'] : __('Add new', TTO_I18N),
                'add_new_item' => isset($cpt['add_new_item']) ? $cpt['add_new_item'] : sprintf(__('Add new %s', TTO_I18N), $cpt['title']),
                'edit' => isset($cpt['edit']) ? $cpt['edit'] : __('Edit', TTO_I18N),
                'edit_item' => isset($cpt['edit_item']) ? $cpt['edit_item'] : sprintf(__('Edit %s', TTO_I18N), $cpt['title']),
                'new_item' => isset($cpt['new_item']) ? $cpt['new_item'] : sprintf(__('New %s', TTO_I18N), $cpt['title']),
                'view' => isset($cpt['view']) ? $cpt['view'] : __('View', TTO_I18N),
                'view_item' => isset($cpt['view_item']) ? $cpt['view_item'] : sprintf(__('View %s', TTO_I18N), $cpt['title']),
                'search_items' => isset($cpt['search_items']) ? $cpt['search_items'] : sprintf(__('Search %s', TTO_I18N), $cpt['title']),
                'not_found' => isset($cpt['not_found']) ? $cpt['not_found'] : sprintf(__('No %s found', TTO_I18N), $cpt['title']),
                'not_found_in_trash' => isset($cpt['not_found_in_trash']) ? $cpt['not_found_in_trash'] : sprintf(__('No %s found in Trash', TTO_I18N), $cpt['title']),
                'parent_item_colon' => isset($cpt['parent_item_colon']) ? $cpt['parent_item_colon'] : sprintf(__('Parent %s', TTO_I18N), $cpt['title'])
            );

            //Build args
            $args = array(
                'labels' => $labels,
                'public' => isset($cpt['options']['public']) && $cpt['options']['public'] ? true : false,
                'show_ui' => isset($cpt['options']['show_ui']) && $cpt['options']['show_ui'] ? true : false,
                'show_in_menu' => isset($cpt['options']['show_ui']) && $cpt['options']['show_ui'] ? true : false,
                'capability_type' => $caps,
                'hierarchical' => isset($cpt['options']['hierarchical']) && $cpt['options']['hierarchical'] ? true : false,
                'rewrite' => isset($cpt['options']['rewrite']) && $cpt['options']['rewrite'] ? true : false,
                'supports' => $sups,
                'query_var' => isset($cpt['options']['query_var']) && $cpt['options']['query_var'] ? true : false,
                'permalink_epmask' => EP_PERMALINK,
                'taxonomies' => $taxs,
                'menu_icon' => isset($cpt['menu_icon_small']) ? $cpt['menu_icon_small'] : ''
            );

            //Action to register
            register_post_type(strtolower($cpt['title']), $args);
        }
    }
}
