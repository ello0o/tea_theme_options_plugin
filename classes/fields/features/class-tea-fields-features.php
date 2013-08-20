<?php
/**
 * Tea Theme Options Features field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Features
 * @since Tea Theme Options 1.4.0
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}


//---------------------------------------------------------------------------------------------------------//

//Require master Class
require_once(TTO_PATH . 'classes/class-tea-fields.php');

//---------------------------------------------------------------------------------------------------------//

/**
 * Tea Fields Features
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.0
 *
 */
class Tea_Fields_Features extends Tea_Fields
{
    //Define protected vars

    /**
     * Constructor.
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __construct(){}


    //--------------------------------------------------------------------------//

    /**
     * MAIN FUNCTIONS
     **/

    /**
     * Build HTML component to display in the Tea T.O. dashboard.
     *
     * @param number $number Define the position of the input
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    public function templateDashboard($number = '__NUM__', $content = array())
    {
        return false;
    }

    /**
     * Build HTML component to display in all the Tea T.O. defined pages.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    public function templatePages($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Features', TTO_I18N);
        $contents = isset($content['contents']) ? $content['contents'] : array();

        //Get template
        include('in_pages.tpl.php');
    }
}
