<?php

/**
 * App container block
 *
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Bewai_AttributeAssigner_Block_Adminhtml_Index extends Mage_Adminhtml_Block_Widget_Container {
    
    /**
     * Sets basic conf for the block
     * @return nothing
     */
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = Mage::helper('bewai_attributeassigner')->__('Assign Attributes');
        $this->_addButton('reset', array(
            'label'     => Mage::helper('adminhtml')->__('Reset'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/reset').'\')',
        ), -1);
    }
}

?>