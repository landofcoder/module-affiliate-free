<?php
namespace Lof\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class LayoutLoadBefore implements ObserverInterface
{      
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $catalogSession;
    /**
     * @param Lof\Affiliate\Helper\Data
     */    
    protected $helper;

    protected $affiliateHelper;

    protected $logger;
    /**
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Session $catalogSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Lof\Affiliate\Helper\AffiliateHelper $affiliateHelper,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->scopeConfig = $scopeConfig;
        $this->catalogSession = $catalogSession;
        $this->helper = $helper;
        $this->affiliateHelper = $affiliateHelper;
        $this->logger = $logger;
    }
    
    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->info('kc_layout');
        if(!$this->helper->getConfig('general_settings/enable')) return;

        $param_code = $this->helper->getParamCode();
        $campaign_param_code = $this->helper->getCampaignParamCode();

        // Set session tracking code
        if( isset($_REQUEST[$param_code]) ) {
            $this->catalogSession->setAffiliateCode($_REQUEST[$param_code]);
            $this->affiliateHelper->setTracking($_REQUEST[$param_code], 'affiliate_code');

            $this->catalogSession->setCampaignCode('');
            if (isset($_REQUEST[$campaign_param_code])) {
                $this->catalogSession->setCampaignCode($_REQUEST[$campaign_param_code], 'campaign_code');
            }

            $this->catalogSession->setAffiliateParams('');
            $url = $this->helper->getCurrentUrlPage();
            $this->catalogSession->setAffiliateParams($url);
        }
        $this->logger->info($this->catalogSession->getAffiliateCode());
        return $this;
    }
}
