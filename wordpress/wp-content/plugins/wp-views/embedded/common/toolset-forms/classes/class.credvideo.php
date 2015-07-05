<?php
require_once 'class.credfile.php';
require_once 'class.video.php';

/**
 * Description of class
 *
 * @author Srdjan
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/1.5/toolset-forms/classes/class.credvideo.php $
 * $LastChangedDate: 2014-08-22 10:23:29 +0000 (Fri, 22 Aug 2014) $
 * $LastChangedRevision: 26350 $
 * $LastChangedBy: francesco $
 *
 */
class WPToolset_Field_Credvideo extends WPToolset_Field_Credfile
{
    protected $_settings = array('min_wp_version' => '3.6');

    public function metaform()
    {
        //TODO: check if this getValidationData does not break PHP Validation _cakePHP required file.
        $validation = $this->getValidationData();
        $validation = WPToolset_Field_Video::addTypeValidation($validation);
        $this->setValidationData($validation);
        return parent::metaform();        
    }
}
