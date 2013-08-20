<?php
/**
 * Tea Theme Options Background field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Background
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
 * Tea Fields Background
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.0
 *
 */
class Tea_Fields_Background extends Tea_Fields
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
        $description = isset($content['description']) ? $content['description'] : '';
        $default = isset($content['default']) && (true === $content['default'] || '1' == $content['default']) ? true : false;
        $width = isset($content['width']) ? $content['width'] : 150;
        $height = isset($content['height']) ? $content['height'] : 60;

        //Default values
        $std = isset($content['std']) ? $content['std'] : array();
        $std['image'] = isset($content['std']['image']) ? $content['std']['image'] : '';
        $std['image_custom'] = isset($content['std']['image_custom']) ? $content['std']['image_custom'] : '';
        $std['color'] = isset($content['std']['color']) ? $content['std']['color'] : '';
        $std['position'] = isset($content['std']['position']) ? $content['std']['position'] : array();
        $std['position']['x'] = isset($content['std']['position']['x']) ? $content['std']['position']['x'] : 'left';
        $std['position']['y'] = isset($content['std']['position']['y']) ? $content['std']['position']['y'] : 'top';
        $std['repeat'] = isset($content['std']['repeat']) ? $content['std']['repeat'] : 'repeat';

        //Get details
        $bgdetails = $this->getDefaults('background-details');

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
        $title = isset($content['title']) ? $content['title'] : __('Tea Background', TTO_I18N);
        $height = isset($content['height']) ? $content['height'] : '60';
        $width = isset($content['width']) ? $content['width'] : '150';
        $description = isset($content['description']) ? $content['description'] : '';
        $default = isset($content['default']) && (true === $content['default'] || '1' == $content['default']) ? true : false;
        $can_upload = true; //$this->can_upload;
        $delete = __('Delete selection', TTO_I18N);

        //Default values
        $std = isset($content['std']) ? $content['std'] : array();
        $std['image'] = isset($content['std']['image']) ? $content['std']['image'] : '';
        $std['image_custom'] = isset($content['std']['image_custom']) ? $content['std']['image_custom'] : '';
        $std['color'] = isset($content['std']['color']) ? $content['std']['color'] : '';
        $std['position'] = isset($content['std']['position']) ? $content['std']['position'] : array();
        $std['position']['x'] = isset($content['std']['position']['x']) ? $content['std']['position']['x'] : 'left';
        $std['position']['y'] = isset($content['std']['position']['y']) ? $content['std']['position']['y'] : 'top';
        $std['repeat'] = isset($content['std']['repeat']) ? $content['std']['repeat'] : 'repeat';

        //Get options
        $options = isset($content['options']) ? $content['options'] : array();

        if ($default)
        {
            $default = $this->getDefaults('images');
            $options = array_merge($default, $options);
        }

        //Positions
        $details = $this->getDefaults('background-details');
        $url = TTO_URI . 'classes/fields/background/img/';

        //Get value
        $val = $this->getOption($id, $std);

        //Get template
        include('in_pages.tpl.php');
    }
}
