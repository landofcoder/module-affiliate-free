<?php

namespace Lof\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterSuccess implements ObserverInterface
{
    protected $scopeConfig;
    protected $customerSession;
    protected $helper;
    protected $catalogSession;
    protected $request;
    protected $messageManager;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->catalogSession = $catalogSession;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->_coreRegistry = $registry;
        $this->request = $request;
        $this->messageManager = $messageManager;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->getConfig('general_settings/enable')) return;
        $dataRequest = $this->request->getPostValue();
        $customerData = $observer->getCustomer();
        if ($this->helper->getConfig('general_settings/auto_create')) {

            $data = [
                'paypal_email' => $customerData->getEmail(),
                'skrill_email' => $customerData->getEmail(),
                'refering_website' => '',
            ];
            $this->helper->createAffiliateAccount($data, $customerData);
        }
        $affiliateCode = $this->catalogSession->getAffiliateCode();
        $campaignCode = $this->catalogSession->getCampaignCode();
        if (isset($dataRequest['refer_code']) && $dataRequest['refer_code']) {
            $_affiliateCode = $dataRequest['refer_code'];
            $affiliateAccount = $this->helper->getAffiliateAccountByTrackingCode($_affiliateCode);
            if (!$affiliateAccount->getId()) {
                $_affiliateCode = $affiliateCode;
                $affiliateAccount = $this->helper->getAffiliateAccountByTrackingCode($_affiliateCode);
            }

            if (isset($dataRequest['refer_cam']) && $dataRequest['refer_cam']) {
                $campaignCode = $dataRequest['refer_cam'];
            }elseif ($affiliateAccount->getId()) {
                $campaignCode = $affiliateAccount->getCampaignCode();
            }

            if ($affiliateAccount->getId()) {
                //check times used refer code
                $timesUsedForReferCode = $this->helper->getConfig('general_settings/times_used_refer_code');
                $timesUsed = $affiliateAccount->getTimesUsedReferCode();
                if ($timesUsed > $timesUsedForReferCode) {
                    $this->messageManager->addError(__("Out of times use the refer code"));
                    return;
                }
                $affiliateCode = $_affiliateCode;
                $timesUsed++;
                $affiliateAccount->setTimesUsedReferCode($timesUsed);
            }

        }

        // Pay Per Lead
        if ($campaignCode) {
            $this->helper->checkPPLCommission($affiliateCode, $campaignCode, $customerData);
        }
        // Add Refering Customer
        if ($affiliateCode) {
            $this->helper->addReferingCustomer($affiliateCode, $customerData->getEmail(), $campaignCode);
        }
        return $this;
    }
}
