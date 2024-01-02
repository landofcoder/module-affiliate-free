<?php
/**
 * landofcoder
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
 * @category   landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Affiliate\Controller\Account;

use Magento\Customer\Model\Session;
use Lof\Affiliate\Model\AccountAffiliateFactory;

class ReferCode extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $session;
    protected $accountAffiliateFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param AccountAffiliateFactory $accountAffiliateFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        AccountAffiliateFactory $accountAffiliateFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;
        $this->accountAffiliateFactory = $accountAffiliateFactory;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultPage = $this->resultPageFactory->create();
        $modelAffiliateAccount = $this->accountAffiliateFactory->create();
        if ($this->session->isLoggedIn()) {
            $dataRequest = $this->getRequest()->getParams();
            $resultPage->getConfig()->getTitle()->set(__('Refer Code'));
            if ($dataRequest) {
                $accountAffiliateId = $dataRequest['account_affiliate_id'];
                $campaign_code = $dataRequest['campaign_code'];

                $affiliateAccount = $modelAffiliateAccount->load($accountAffiliateId);
                if ($affiliateAccount) {
                    $affiliateAccount->setCampaignCode($campaign_code)
                        ->save();
                    $this->messageManager->addSuccessMessage(__('Saved Successfully'));
                }
            }
            return $resultPage;
        } else {
            $resultRedirect->setPath('affiliate/account/login');
            return $resultRedirect;
        }
    }
}
