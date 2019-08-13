<?php

/**
 * Admin controller
 *
 * @category    Bewai
 * @package     Bewai_AttributeAssigner
 * @copyright   Copyright (c) 2014 Bewai (http://www.bewai.fr)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Bewai_AttributeAssigner_AdminController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * Retreives helper
     * @return Bewai_AttributeAssigner_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('bewai_attributeassigner');
    }
    
    /**
     * Does common stuff (title, breadcrumb, menu)
     * 
     * @return \Bewai_AttributeAssigner_AdminController
     */
    protected function _initModuleLayout()
    {
        if (!$this->_isLayoutLoaded) $this->loadLayout();
        
        $this->_setActiveMenu('catalog/attributes/bewai_attributeassigner')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
             ->_addBreadcrumb(Mage::helper('bewai_attributeassigner')->__('Assign Attributes'), Mage::helper('bewai_attributeassigner')->__('Assign Attributes'))
             ->_title($this->__('Catalog'))
             ->_title(Mage::helper('bewai_attributeassigner')->__('Assign Attributes'));
        
        return $this;
    }
    
    /**
     * Front page (Grid)
     */
    public function indexAction()
    {
        $step = $this->getRequest()->getParam('step');
        if ($step && in_array($step, $this->_helper()->getSteps())) {
            Mage::register('bewai_attributeassigner_force_current_step', $step);
        }
        
        $this->_processSessionData();
        $this->_initModuleLayout();
        $this->renderLayout();
    }
    
    /**
     * Processes all ajax calls
     * @return boolean
     * @deprecated since version 0.1.0
     */
    public function indexAjaxAction()
    {
        if (!$this->getRequest()->isPost() || !$this->getRequest()->isAjax()) {
            $this->getResponse()->setBody(' ');
            return false;
        }
        
        $this->_processSessionData();
            
        $this->loadLayout('bewai_attributeassigner_admin_index');
        $this->getResponse()->setBody(
                $this->getLayout()
                     ->getBlock('bewai_attributeassigner_index_app')
                     ->toHtml()
        );
    }
    
    /**
     * Resets all submitted data
     */
    public function resetAction()
    {
        $this->_helper()->setAttributeSelection();
        $this->_helper()->setProductsSelection();
        $this->_helper()->setValueSelection();
        
        $this->_redirect('*/*/index');
    }
    
    /**
     * Processes any given data or parameters
     */
    protected function _processSessionData()
    {
        if ($attribute_id = $this->getRequest()->getParam('attribute')) {
            $this->_helper()->setAttributeSelection($attribute_id);
            // Clear steps after
            $this->_helper()->setProductsSelection();
            $this->_helper()->setValueSelection();
        }
        
        if ($product_ids = $this->getRequest()->getParam('products')) {
            $this->_helper()->setProductsSelection($product_ids);
            // Clear steps after
            $this->_helper()->setValueSelection();
        }
        
        if ($value_id = $this->getRequest()->getParam('value')) {
            $this->_helper()->setValueSelection($value_id);
        }
        
        if ($this->_helper()->isReady() && $this->getRequest()->isPost()) {
            $this->_redirect('*/*/index');
            return false;
        }
    }
    
     /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Filters products grid
     * 
     * @return boolean
     */
    public function ajaxFilterGridAction() 
    {        
        $this->_initModuleLayout();
        $this->loadLayout('bewai_attributeassigner_admin_index');
        $this->getResponse()->setBody(
            $this->getLayout()
                ->getBlock('bewai_attributeassigner_index_app_products')
                ->toHtml());
    }
    
    /**
     * Processes attribute change
     * 
     * @return boolean
     */
    public function confirmAction()
    {
        try {
            
            $products_ids    = $this->_helper()->getSelectedValue('products');
            $attribute  = $this->_helper()->getSelectedValue('attribute');
            $value = $this->_helper()->getSelectedValue('value');
            
            if (!$products_ids || count($products_ids) === 0) {
                throw new Exception ($this->__('Please provide product ids to change.'));
            }
            
            if (empty($attribute) || empty($value)) {
                throw new Exception ($this->__('Please specify the attribute to change and the target value.'));
            }
            if (!$this->_helper()->isReady()) {
                 throw new Exception ($this->__('You have to validate every step before submit.'));
            }
        
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($products_ids, array($attribute->getAttributeCode() => $value->getId()), Mage::app()->getStore()->getId());
            
            $this->_getSession()->addSuccess($this->__('The attribute value for %s has been successfully change to %s for selected products.', 
                    $attribute->getFrontendLabel(), $value->getValue()
            ));
            
            $this->_redirect('*/*/reset');
            return false;
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occured while updating products : %s', $e->getMessage()));
        }
           
        $this->_redirectReferer();
    }
    
}

?>