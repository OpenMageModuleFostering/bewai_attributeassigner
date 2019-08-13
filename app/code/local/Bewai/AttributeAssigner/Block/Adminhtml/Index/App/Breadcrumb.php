<?php
/**
 * Breadcrumb / Path
 *
 * 
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Bewai_AttributeAssigner_Block_Adminhtml_Index_App_Breadcrumb extends Mage_Adminhtml_Block_Template {
    
    /**
     * Steps processed by the block
     * @var array
     */
    protected $_steps;
    
    /**
     * Retreives helper
     * @return Bewai_AttributeAssigner_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('bewai_attributeassigner');
    }
    
    /**
     * Prepares steps before display
     */
    public function _beforeToHtml()
    {
        parent::_beforeToHtml();
        
        foreach ($this->_helper()->getSteps('with title') as $key => $title) {
            $this->_steps[$key] = array(
              'title'   => $title,
              'content' => $this->getChildHtml($key)
            );
        }
        
        $this->_addStepClasses();
    }
    
    /**
     * Block steps getter
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }
    
    /**
     * Adds the classes to steps
     * @return \Bewai_AttributeAssigner_Block_Adminhtml_Index_App_Breadcrumb
     */
    protected function _addStepClasses() {
        $currentStep = $this->_helper()->getCurrentStep();
        
        foreach (array_keys($this->_steps) as $key) {
            $classes = array();
            if ($key === $currentStep) {
                $classes[] = 'active';
            }
            
            if ($this->_helper()->getSelectedValue($key)) {
                $classes[] = 'valid';
            }
            elseif (!in_array('active', $classes)) {
                $classes[] = 'disabled';
            }
            
            $this->_steps[$key]['class'] = implode(' ', $classes);
        }
        
        return $this;
    }
}