<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Autosearch
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Controller\Product;

class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     */
    public function execute()
    {
        $helper = $this->_objectManager->create('Lof\Affiliate\Helper\Data');
        if($helper->getConfig('general_settings/enable')) {
            $trackcode_route = $helper->getTrackingCodeRoute();
            if($this->getRequest()->getParam($trackcode_route)){
                $customerSession = $this->_objectManager->get('\Magento\Catalog\Model\Session');
                $customerSession->setTrackingCode($this->getRequest()->getParam($trackcode_route));               
            } 
        }
        
        return parent::execute();
    }   
}