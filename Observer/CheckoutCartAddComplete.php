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

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
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

    ){

        $this->_resource = $resource;
        $this->_collection = $collection;
        $this->_objectManager= $objectManager;

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
        if(!$this->_helper->getConfig('general_settings/enable')) return;
        if( !$this->_helper->checkSalesAffiliateCode() ) return;

        $campaignCode = $this->_helper->checkValidCampaign($this->_catalogSession->getCampaignCode());
        $affiliateCode = $this->_catalogSession->getAffiliateCode();
        $affiliateParams = $this->_catalogSession->getAffiliateParams();

        if($affiliateCode != ''){
            $quote = $this->checkoutSession->getQuote();
            $quote->setAffiliateCode($affiliateCode)
                    ->setCampaignCode($campaignCode)
                    ->setAffiliateParams($affiliateParams)
                    ->save();

            // $quoteItem = ( $quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem );var_dump($quoteItem);die();

            // if ($campaignCode != '') {
        
            //     // $event = $observer->getEvent();
            //     $product = $observer->getEvent()->getProduct();
            //     $product_id = $product->getId();

                // $validProduct = $this->_helper->validateRuleProduct($product_id, $campaignCode);
                // if( !$validProduct ) {
                //     $this->_messageManager->addWarning(__('This Product does not valid with Affiliate Rules of Campaign.'));
                // }
            
            //     $customer_id = $this->_customerSession->getCustomerId();
            //     $customer = $this->_customerRepository->getById($customer_id);
            //     $quote = $this->_quoteFactory->create()->loadByCustomer($customer);
                        
            //     if ($quote->isVirtual()) {
            //         $address = $quote->getBillingAddress();
            //     } else {
            //         $address = $quote->getShippingAddress();
            //     } 

            //     $data = array('quote' => $address);

            //     $result = $this->_helper->validateRuleCart($data, $campaignCode);
            //     if( !$result ) {
            //         $this->_messageManager->addWarning(__('This Cart does not valid with Affiliate Rules of Campaign.'));
            //     }
            // }
        }

        // end event  
    }
}
