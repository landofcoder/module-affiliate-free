<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Affiliate\Controller\Adminhtml\WithdrawAffiliate;

use Magento\Backend\App\Action\Context;

class Braintree extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\Affiliate\Model\WithdrawAffiliate
     */
    protected $_withdrawModel;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Braintree\Model\Config
     */
    //protected $config;

    /**
     * @var BraintreeTransaction
     */
    protected $braintreeTransaction;

    /**
     * @param Context
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\WithdrawAffiliate $withdrawModel,
        \Magento\Store\Model\StoreManager $storeManager
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_withdrawModel = $withdrawModel;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Affiliate::withdraw');
    }

    public function execute()
    {
        $request = $this->getRequest()->getParams();
        $withdrawId = $this->getRequest()->getParam('withdraw_id');
        $type = $this->getRequest()->getParam('type');
        $model = $this->_withdrawModel->load($withdrawId);
        $model->setStatus('Complete');
        $customerId = $this->getRequest()->getParam('customer_id');
        $amount = $this->getRequest()->getParam('amount');
        $storeId = $this->_storeManager->getStore()->getId();
    }
}