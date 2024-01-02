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

namespace Lof\Affiliate\Controller\Account;

use Magento\Customer\Model\Session;

class PaymentForm extends \Magento\Framework\App\Action\Action
{
    CONST EMAILIDENTIFIER = 'sent_mail_after_withdraw';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\Affiliate\helper\Data
     */
    protected $_helper;

    /**
     * @var \Lof\Affiliate\Model\AccountAffiliate
     */
    protected $_accountModel;

    /**
     * @var Session
     */
    protected $session;

    protected $_scopeConfig;

    protected $_affData;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Affiliate\Helper\Data $helper,
        \Lof\Affiliate\Model\AccountAffiliate $accountModel,
        Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_accountModel = $accountModel;
        $this->session = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Affiliate Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $customer = $this->session->getCustomer();
        $request = $this->getRequest()->getParam('request');
        $enable_withdrawl = $this->_helper->getConfig("general_settings/enable_withdrawl");
        if(!$enable_withdrawl) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('affiliate/affiliate/home');
            return $resultRedirect;
        }
        $currency_code = $this->getRequest()->getParam('currency_code');
        $payment_method = $this->getRequest()->getParam('type');
        try {
            $this->_helper->saveWithdraw($request, $customer, $currency_code, $payment_method);
            $this->_accountModel->updateBalance($request, $customer);

            $this->messageManager->addSuccess(
                __('You send a request success.')
            );
            $emailFrom = $this->_helper->getConfig('general_settings/sender_email_identity');
            $emailTo = $customer->getEmail();
            $templateVar = array(
                'name' => $customer->getName()
            );
            $emailidentifier = self::EMAILIDENTIFIER;

            $resultRedirect->setPath('*/*/withdraw');
            return $resultRedirect;

        } catch (Exception $e) {
            $this->messageManager->addException($e, __('You can\'t send a request.'));
        }

    }
}
