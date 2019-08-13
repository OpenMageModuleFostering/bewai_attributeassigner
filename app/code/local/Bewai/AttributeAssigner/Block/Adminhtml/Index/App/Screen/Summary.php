<?php
/**
 * Summaries selections and propose submit
 *
 * 
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Bewai_AttributeAssigner_Block_Adminhtml_Index_App_Screen_Summary extends Bewai_AttributeAssigner_Block_Adminhtml_Index_App {
    
    /**
     * Retreives selected products collection
     * @return \Varien_Data_Collection
     */
    public function getProductsCollection()
    {
        $products = $this->_helper()->getSelectedValue('products');
        if (!$products) return new Varien_Data_Collection();
        
        return Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $products));
    }
    
    /**
     * Retreives products that already have the selected option value
     * @return \Varien_Data_Collection
     */
    public function getTargetProductCollection()
    {
        $attribute = $this->_helper()->getSelectedValue('attribute');
        if (!$attribute) return new Varien_Data_Collection();
        
        return Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter($attribute->getAttributeCode(), $this->getValue()->getOptionId());
    }
}
