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

class Login extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Session
     */
    protected $session;

    protected $_accountAffiliate;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        \Lof\Affiliate\Model\AccountAffiliate $accountAffiliate
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;
        $this->_accountAffiliate = $accountAffiliate;
        parent::__construct($context);
    }

    /**
     * Affiliate Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $emailCustomer = $this->session->getCustomer()->getEmail();
        $checkAccountExist = $this->_accountAffiliate->checkAccountExist($emailCustomer);
        if ($this->session->isLoggedIn() && $checkAccountExist == '1') {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/edit');
            return $resultRedirect;
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Affiliate Login'));
        return $resultPage;
    }
}
