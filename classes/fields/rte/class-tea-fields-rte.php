<?php
/**
 * Tea Theme Options RTE field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Rte
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
 * Tea Fields RTE
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.0
 *
 */
class Tea_Fields_Rte extends Tea_Fields
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
        //Default variables
        $id = isset($content['id']) ? $content['id'] : '';
        $title = isset($content['title']) ? $content['title'] : '';
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';

        //Get template
        include('in_dashboard.tpl.php');
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
        //Check if an id is defined at least
        $this->checkId($content);

        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea RTE', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';

        //Check selected
        $val = $this->getOption($id, $std);
        $val = stripslashes($val);

        //Get template
        include('in_pages.tpl.php');
    }
}
