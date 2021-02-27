<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Controller\Adminhtml\WithdrawAffiliate;
use Magento\Backend\App\Action\Context;
//use Magento\Braintree\Model\Adapter\BraintreeTransaction;

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
        //\Magento\Braintree\Model\Config $config,
        //BraintreeTransaction $braintreeTransaction
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_withdrawModel = $withdrawModel;
        $this->_storeManager = $storeManager;
        //$this->config = $config;
        //$this->braintreeTransaction = $braintreeTransaction;
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
        //$this->config->initEnvironment($storeId);
        /*
        $result = $this->braintreeTransaction->sale(
            array(
                'amount' => '100',
                'serviceFeeAmount' => '100',
                'merchantAccountId' => 'minhnt',
                'creditCard' => array(
                    'number' => '4111111111111111',
                    'expirationDate' => '12/20'
                    )
                // 'paymentMethodNonce' => 'nonce',
                // 'customerId' => $customerId
                // 'amount' => '10',
                // 'creditCard' => array(
                //     'number' => '4111111111111111',
                //     'cardholderName' => 'Srinivas Tamada',
                //     'expirationDate' => '12/20',
                //     'cvv' => '123'
                //     )
                )                
        );
        if ($result->success) 
        {
            //print_r("success!: " . $result->transaction->id);
            if($result->transaction->id)
            {
                $braintreeCode=$result->transaction->id;
            }
            echo "1234";
        } 
        else if ($result->transaction) 
        {
            echo 'abcd';
        } 
        else 
        {
            echo 'bcds';
        }*/
    }

    // public function 
    
}