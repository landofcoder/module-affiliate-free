<?php

namespace Lof\Affiliate\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Lof\Affiliate\Model\AccountAffiliateFactory;

class SaveInfo extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $accountAffiliate;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param AccountAffiliateFactory $accountAffiliate
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        AccountAffiliateFactory $accountAffiliate,
        \Lof\Affiliate\Helper\Data $helper
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->accountAffiliate = $accountAffiliate;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->session->isLoggedIn()) {
            $data = $this->getRequest()->getParams();
            $accountId = $data['affiliate_account_id'];
            $model = $this->accountAffiliate->create();
            $accountAffiliate = $model->load($accountId)->addData([
                'default_payment_method' => $data['default_payment_method'],
                'withdrawal_auto' => $data['withdrawal_auto'],
                'auto_payment_balance_reaches' => $data['auto_payment_balance_reaches'],
                'paypal_email' => $data['paypal_email'],
                'reserve_level' => $data['reserve_level']
            ]);
            try {
                $accountAffiliate->save();
                $this->messageManager->addSuccess(__('Saved successfully'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
            }
            $resultRedirect->setPath('*/*/info');
        }
        return $resultRedirect;
    }
}
