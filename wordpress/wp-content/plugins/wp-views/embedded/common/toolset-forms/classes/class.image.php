<?php
require_once 'class.file.php';

/**
 * Description of class
 *
 * @author Srdjan
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/1.5/toolset-forms/classes/class.image.php $
 * $LastChangedDate: 2014-09-29 16:01:46 +0000 (Mon, 29 Sep 2014) $
 * $LastChangedRevision: 27533 $
 * $LastChangedBy: marcin $
 *
 */
class WPToolset_Field_Image extends WPToolset_Field_File
{
    public function metaform()
    {
        $validation = $this->getValidationData();
        $validation = self::addTypeValidation($validation);
        $this->setValidationData($validation);
        return parent::metaform();
    }

    public static function addTypeValidation($validation)
    {
        $valid_extensions = array(
            'bmp',
            'gif',
            'jpeg',
            'jpg',
            'png',
            'svg',
            'webp',
        );
        $valid_extensions = apply_filters( 'toolset_valid_image_extentions', $valid_extensions);
        $validation['extension'] = array(
            'args' => array(
                'extension',
                implode('|', $valid_extensions),
            ),
            'message' => __( 'You can add only images.', 'wpv-views' ),
        );
        return $validation;
    }
}
