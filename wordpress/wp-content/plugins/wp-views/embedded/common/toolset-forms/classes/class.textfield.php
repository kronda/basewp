<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/1.5/toolset-forms/classes/class.textfield.php $
 * $LastChangedDate: 2015-02-25 11:07:34 +0000 (Wed, 25 Feb 2015) $
 * $LastChangedRevision: 31886 $
 * $LastChangedBy: marcin $
 *
 */
require_once "class.field_factory.php";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Franko
 */
class WPToolset_Field_Textfield extends FieldFactory
{
    public function metaform()
    {
        $metaform = array();
        $metaform[] = array(
            '#type' => 'textfield',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#value' => $this->getValue(),
            '#validate' => $this->getValidationData(),
            '#repetitive' => $this->isRepetitive(),
            '#attributes' => $this->getAttr(),
            'wpml_action' => $this->getWPMLAction(),
        );
        return $metaform;
    }

}
