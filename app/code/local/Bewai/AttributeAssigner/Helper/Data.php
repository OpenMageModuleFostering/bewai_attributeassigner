<?php

/**
 * Data Helper
 *
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Bewai_AttributeAssigner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * App steps
     * @var array 
     */
    protected $_steps = array(
        'attribute' => 'Attribute to change.',
        'products'  => 'Products to alter.',
        'value'     => 'Target attribute value.'
    );
    
    /**
     * Session key for session variables to prevent namespace collision
     * @var string
     */
    protected $_session_key = 'bewai_attributeassigner_selection_%s';
    
    /**
     * Getter for steps
     * 
     * @param boolean $with_title Whether to include titles as values
     * @return array
     */
    public function getSteps($with_title = false)
    {
        return $with_title ? $this->_steps : array_keys($this->_steps);
    }
    
    /**
     * Processes form data from block
     * 
     * @param integer $attribute_id
     * @return boolean
     */
    public function setAttributeSelection($attribute_id = false)
    {   
        if (!$attribute_id || $attribute_id === 'select') return $this->clearSelectedValue('attribute');
        
        $attr = Mage::getModel('catalog/resource_eav_attribute')->load($attribute_id);
        if (!$attr->getId()) return $this->clearSelectedValue('attribute');
        
        $this->setSelectedValue('attribute', $attr);
        
        return $this;
    }
    
    /**
     * Processes product data from grid
     * 
     * @param array $products
     * @return boolean
     */
    public function setProductsSelection($products = array())
    {
        if (count($products) == 0) return $this->clearSelectedValue('products');

        $count = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $products))
                ->getSize();
        
        if ($count == 0) return $this->clearSelectedValue('products');
        
        $this->setSelectedValue('products', $products);
        
        return $this;
    }
    
    /**
     * Processes form data from block
     * 
     * @param array $value
     * @return boolean
     */
    public function setValueSelection($value = false)
    {
        if (!$value || $value  === 'select') return $this->clearSelectedValue('value');

        $val = Mage::getModel('eav/entity_attribute_option')->load($value);
        if (!$val->getId()) return $this->clearSelectedValue('value');
        
        $attribute_value = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getSelectedValue('attribute')->getId())
                ->setPositionOrder('desc', true)
                ->addFieldToFilter('main_table.option_id', $val->getOptionId())
                ->load()
                ->getFirstItem();
        
        $this->setSelectedValue('value', $attribute_value);
        
        return $this;
    }
    
    /**
     * Sets the given value into session under the key for given type and code
     * 
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    public function setSelectedValue($type, $value)
    {
        return Mage::getSingleton('adminhtml/session')->setData(
                sprintf($this->_session_key, $type), $value
        );
    }
    
    /**
     * Retreives the selected value for given type and code
     * 
     * @param string $type
     * @return mixed
     */
    public function getSelectedValue($type)
    {
        return Mage::getSingleton('adminhtml/session')->getData(
                sprintf($this->_session_key, $type)
        );
    }
    
    /**
     * Removes previously selected value
     * 
     * @param string $type
     * @return type
     */
    public function clearSelectedValue($type)
    {
        Mage::getSingleton('adminhtml/session')->unsetData(
                sprintf($this->_session_key, $type)
        );
        
        return $this;
    }
    
   /**
    * 
    * @return array
    */
    public function getOptionArray($attribute_id) {
        
        $arr = array();
        
        $attr = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute_id)
                ->setPositionOrder('desc', true)
                ->load();
        foreach ($attr as $value) {
            $arr[$value->getId()] = $value->getValue();
        }
        return $arr;
    }
    
    /**
     * Retreives current step
     * @return string
     */
    public function getCurrentStep()
    {
        if (Mage::registry('bewai_attributeassigner_force_current_step')) {
            return Mage::registry('bewai_attributeassigner_force_current_step');
        }
        
        foreach ($this->getSteps() as $key) {
            if ($this->getSelectedValue($key)) {
                continue;
            }
            return $key;
        }
        
        return 'ready';
    }
    
    /**
     * Finds out if app is ready for action
     * @return boolean
     */
    public function isReady()
    {
        $ready = true;
        foreach ($this->getSteps() as $step) {
            $ready = $this->getSelectedValue($step);
            if (!$ready) return false;
        }
        return $ready;
    }
}