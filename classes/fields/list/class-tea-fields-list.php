<?php
/**
 * Tea Theme Options List field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields List
 * @since Tea Theme Options 1.4.5
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
 * Tea Fields List
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.5
 *
 */
class Tea_Fields_List extends Tea_Fields
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
        $contents = isset($content['contents']) ? $content['contents'] : array();

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
        //Default variables
        $contents = isset($content['contents']) ? $content['contents'] : array();

        //Get template
        include('in_pages.tpl.php');
    }


    //--------------------------------------------------------------------------//

    /**
     * PRE SAVE METHOD
     **/

    /**
     * Edit contents before saving.
     *
     * @param array $content Content sent throught Dahsboard forms.
     * @return array $content Content modified.
     *
     * @since Tea Theme Options 1.4.5
     */
    static function saveContent($content)
    {
        //Check for options
        if (!isset($content['contents']))
        {
            return $content;
        }

        //Check for __OPTNUM__
        if (isset($content['contents']['__OPTNUM__']))
        {
            unset($content['contents']['__OPTNUM__']);
        }


        //Iterate on each contents
        foreach ($content['contents'] as $k => $ctn)
        {
            //Check label
            if (empty($ctn[1]))
            {
                unset($content['contents'][$k]);
                continue;
            }
        }

        //Return modified contents
        return $content;
    }
}
