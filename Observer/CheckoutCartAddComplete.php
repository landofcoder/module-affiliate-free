<?php

namespace Lof\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CheckoutCartAddComplete implements ObserverInterface
{
    /** @var CheckoutSession */
    protected $checkoutSession;

    protected $_quoteFactory;

    protected $_helper;
    protected $_accountAffilite;
    protected $_customerSession;
    protected $_catalogSession;

    protected $_objectManager;
    protected $_resource;
    protected $_collection;
    protected $_messageManager;
    protected $_customerRepository;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Collection $collection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\CollectionFactory $accountAffilite
    ) {

        $this->_resource = $resource;
        $this->_collection = $collection;
        $this->_objectManager = $objectManager;
        $this->_accountAffilite = $accountAffilite;
        $this->_customerSession = $customerSession;
        $this->_catalogSession = $catalogSession;
        $this->_customerRepository = $customerRepository;
        $this->_quoteFactory = $quoteFactory;
        $this->_messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->_helper = $helper;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->getConfig('general_settings/enable')) return;

        $affiliateCode = $this->_catalogSession->getAffiliateCode();

        if (!$this->_helper->checkSalesAffiliateCode($affiliateCode)) return;

        $campaignCode = $this->_helper->checkValidCampaign($this->_catalogSession->getCampaignCode());

        $affiliateParams = $this->_catalogSession->getAffiliateParams();

        if ($affiliateCode != '') {
            $quote = $this->checkoutSession->getQuote();
            $quote->setAffiliateCode($affiliateCode)
                ->setCampaignCode($campaignCode)
                ->setAffiliateParams($affiliateParams)
                ->save();
        }
        // end event
    }
}
