<?php
/**
 * Attribute Selector Grid Application
 *
 * 
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Bewai_AttributeAssigner_Block_Adminhtml_Index_App extends Bewai_AttributeAssigner_Block_Adminhtml_Index {
    
    /**
     * Constructor : no cache
     */
    protected function _construct() {
        parent::_construct();
        $this->setData('cache_lifetime', null);
    }
    
    /**
     * Retreives helper
     * @return Bewai_AttributeAssigner_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('bewai_attributeassigner');
    }
    
    /**
     * Retreives the list of attributes
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getAttributeList()
    {
        return Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('frontend_input', 'select');
    }
    
    
    /**
     * Retreives available values for selected attribute
     * @return array|Varien_Data_Collection
     */
    public function getSelectedAttributeValuesList()
    {
        if (!$this->getAttribute()) return array();
        
        return Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setPositionOrder('desc', true)
                ->load();
    }
    
    /**
     * Given value is selected one
     * @param string\boolean $val
     * @return boolean
     */
    public function isValueSelected($val)
    {
        if (!$this->getValue()) return false;
        return $val->getId() == $this->getValue()->getId();
    }
    
    /**
     * Given attribute is selected one
     * @param type $attr
     * @return boolean
     */
    public function isAttributeSelected($attr)
    {
        if (!$this->getAttribute()) return false;
        return $attr->getId() == $this->getAttribute()->getId();
    }
    
    /**
     * Retreives selected attribute
     * @return mixed
     */
    public function getAttribute()
    {
        return Mage::helper('bewai_attributeassigner')->getSelectedValue('attribute');
    }
    
    /**
     * Retreives selected value
     * @return mixed
     */
    public function getValue()
    {
        return Mage::helper('bewai_attributeassigner')->getSelectedValue('value');
    }
    
    /**
     * Form action url
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/index');
//      return $this->getUrl('*/*/indexAjax'); REMOVED AJAX CAUSE PROBLEM EVAL GRID SCRIPTS
    }
}
