<?php
/**
 * Product Selector Grid
 *
 * 
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Bewai_AttributeAssigner_Block_Adminhtml_Index_App_Screen_Products extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Main conf
     */
    public function __construct() 
    {
        parent::__construct();
        
        $this->setId('bewai_attributeassigner_adminhtml_index_app_screen_products');
        $this->_controller = 'admin';
        $this->setDefaultSort('sku');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setCanDisplayContainer(true);
        
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
    * Collection
    * 
    * @return Bewai_AttributeAssigner_Block_Adminhtml_Index_Attributeselector
    */
    protected function _prepareCollection() 
    {
        if ($attr = $this->getAttribute()) {
            $collection = Mage::getModel('catalog/product')->getCollection()
                          ->addAttributeToSelect(array('sku', 'name', $attr->getAttributeCode()))
                          ->addAttributeToFilter('type_id', 'simple');

        }
        else {
            $collection = new Varien_Data_Collection();
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
    * Columns
    * @return Bewai_AttributeAssigner_Block_Adminhtml_Index_Attributeselector
    */
    protected function _prepareColumns() 
    {
        $this->addColumn('sku', array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));
        
        $this->addColumn('name', array(
            'header'=> Mage::helper('catalog')->__('Name'),
            'index' => 'name'
        ));
        
        if ($attr = $this->getAttribute()) {        
            $this->addColumn('value', array(
                    'header'=> $attr->getFrontendLabel(),
                    'width' => '100px',
                    'index' => $attr->getAttributeCode(),
                    'type'  => 'options',
                    'options' => Mage::helper('bewai_attributeassigner')->getOptionArray($attr->getId()),
            ));
        }
        return parent::_prepareColumns();
    }    
    
    /**
     * Massaction configuration (save)
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction() 
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('products');
        $this->getMassactionBlock()->addItem('change', array(
            'label' => Mage::helper('bewai_attributeassigner')->__('Validate'),
            'url' => $this->getUrl('*/*/index')
        ));
        
        return parent::_prepareMassaction();
    }
    
    /**
     * Sets the previously selected values to massaction if any
     * @return \Bewai_AttributeAssigner_Block_Adminhtml_Index_App_Screen_Products
     */
    protected function _prepareMassactionColumn() {
        parent::_prepareMassactionColumn();
        $values = Mage::helper('bewai_attributeassigner')->getSelectedValue('products');
        $this->_columns['massaction']->setValues($values);
        return $this;
    }
    
     /**
     * Grid URL
     * @return string
     */
    public function getGridUrl() {
        return  $this->getUrl('*/*/ajaxFilterGrid', array('_current' => true));
    }
}
