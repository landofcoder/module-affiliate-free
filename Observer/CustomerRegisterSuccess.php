<?php
namespace Lof\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterSuccess implements ObserverInterface
{      
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    // protected $_coreRegistry;

    protected $scopeConfig;
    protected $customerSession;
    protected $helper;
    protected $catalogSession;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ){
        $this->scopeConfig = $scopeConfig;
        $this->catalogSession = $catalogSession;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->_coreRegistry     = $registry;
    }
    
    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if(!$this->helper->getConfig('general_settings/enable')) return;

        $customerData = $observer->getCustomer();
        $customer = $observer->getEvent()->getCustomer();

        if($this->helper->getConfig('general_settings/auto_create')){
    
            $data = array(
                'paypal_email' => $customerData->getEmail(),
                'skrill_email' => $customerData->getEmail(),
                'refering_website' => '',
            );

            $this->helper->createAffiliateAccount($data, $customerData);

        }

        // Pay Per Lead
        $this->helper->checkPPLCommission($this->catalogSession->getAffiliateCode(), $this->catalogSession->getCampaignCode(), $customerData);

        // Add Refering Customer
        if ($this->catalogSession->getAffiliateCode()) {
            $this->helper->addReferingCustomer($this->catalogSession->getAffiliateCode(), $customerData->getEmail());
        }

        return $this;
        }
}
