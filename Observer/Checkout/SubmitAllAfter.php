<?php
namespace Lof\Affiliate\Observer\Checkout;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class SubmitAllAfter
{
    protected $_quote;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $catalogSession;

    protected $transactionAffiliate;

    protected $accountAffilite;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_collection;
    /**
     * @param Lof\Affiliate\Helper\Data
     */
        protected $_helper;

        protected $affiliateHelper;

    protected $logger;
    /**
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,

        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Quote\Model\Quote $quote,

        \Lof\Affiliate\Model\TransactionAffiliate $transactionAffiliate,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\CollectionFactory $accountAffilite,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\ResourceModel\Order\Collection $collection,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Lof\Affiliate\Helper\AffiliateHelper $affiliateHelper,
        \Psr\Log\LoggerInterface $logger

    ){
        $this->scopeConfig = $scopeConfig;
        $this->catalogSession = $catalogSession;
        $this->transactionAffiliate = $transactionAffiliate;
        $this->_orderFactory = $orderFactory;
        $this->_resource = $resource;
        $this->_accountAffilite = $accountAffilite;
        $this->_collection = $collection;
        $this->_objectManager= $objectManager;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->affiliateHelper = $affiliateHelper;
        $this->_quote = $quote;
        $this->logger = $logger;
    }

    public function beforeSave(OrderRepositoryInterface $repository, Order $order)
    {
        $this->execute($order);
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(Order $order)
    {
        $this->logger->info('kc_submit');

        if(!$this->_helper->getConfig('general_settings/enable')) return;

        if ($order->getEntityId() || $order->getAffiliateCode()) {
            return;
        }
        $this->logger->info('pass');

        // check all order id and compare with conditions config
        // $order = $observer->getEvent()->getOrder();
//        $orderIds = $order->getId();
        // if (!$orderIds || is_array($orderIds)) {
        //     return $this;
        // }
        $order_id = $order->getEntityId();
        $orderSubTotal = $order->getSubtotal();
        $incrementId = $order->getIncrementId();
        $account_data = $this->_helper->getAccountDataByCustomerEmail($order->getCustomerEmail());
        if (!empty($account_data)) {
            $group = $this->_helper->getGroupDataByAffiliateId($account_data['tracking_code']);
            if ($group['is_active'] == 1 && $group['commission_ppl'] > 0) {

                if ($group['commission_ppl_action'] == 1) {
                    $order_commission = $orderSubTotal*$group['commission_ppl']/100;
                } else {
                    $order_commission = $group['commission_ppl'];
                }
                $data['account_id'] = $account_data['accountaffiliate_id'];
                $data['commission_total'] = $order_commission;
                $data['description'] = __('ppl commision for order #') . $incrementId;
                $data['affiliate_code'] = $account_data['tracking_code'];
                $data['email_aff'] = $account_data['email'];
                $data['campaign_code'] = '';
//                $data['order_id'] = $order_id;
                $data['order_total'] = $orderSubTotal;
                $data['order_status'] = $order->getStatus();
                $data['transaction_stt'] = $order->getStatus();
                $data['increment_id'] = $incrementId;
                $data['base_currency_code'] = $order->getBaseCurrencyCode();
                $data['customer_email'] = $order->getCustomerEmail();
                $this->_helper->saveHistoryOrderAffiliate($data);
            }
        }


        $affiliateCode = $this->affiliateHelper->getTracking('affiliate_code');
        $this->logger->info($affiliateCode);
        if($affiliateCode == '') return;
        if(!$this->_helper->checkSalesAffiliateCode($affiliateCode)) return;
        $campaignCode = $this->affiliateHelper->getTracking('campaign_code');
        $affiliateParams = '';



        /** @var $order \Magento\Sales\Model\Order */
        $quote_id = $order->getQuoteId();
        $quote = $this->_quote->load($quote_id);

        $trackingcode = array(
            'affiliate_code' => $affiliateCode,
            'campaign_code' => $campaignCode,
            'affiliate_params' => $affiliateParams
        );
        $affiliate_email = $this->_helper->getEmailAffiliateByCode($affiliateCode);
        $affiliate_id = $this->_helper->getAffiliateIdByCode($affiliateCode);
        $customer_id = $this->_customerSession->getCustomerId();
        $orderSubTotal = $order->getSubtotal();
//        $order_id = $order->getEntityId();
//        $this->_helper->saveTrackingFromSalesOrder($order->getIncrementId(), $trackingcode);

        if ($campaignCode != '') {
            $campaign = $this->_helper->getCampaignByTrackingCode($campaignCode);
            $discount_action = $campaign->getDiscountAction();
            $discount_amount = $campaign->getDiscountAmount();
            if ($discount_action == 'by_percent') {
                $orderCommission = $orderSubTotal*$discount_amount/100;
            } else {
                $orderCommission = $discount_amount;
            }
            $commission = $campaign->getCommission();
            if (!empty($commission)) {
                $priceOrderTotal = 0;
                $orderTotal = 0;

                $commissionAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\CommissionAffiliate');
                $commissionAffiliate->loadByAttribute('affiliate_code', $affiliateCode);
                $commission_data = $commissionAffiliate->getData();

                if (!empty($commission_data)) {
                    $priceOrderTotal += $commissionAffiliate->getPriceOrderTotal();
                    $orderTotal += $commissionAffiliate->getOrderTotal();
                }
                $order_commission = $this->_helper->caculateCommission($commission, $priceOrderTotal, $orderTotal, $orderSubTotal, $orderCommission);
            }
        } else {
            $groupData = $this->_helper->getGroupDataByAffiliateId($affiliateCode);
            if ($groupData['is_active'] == 1 && $groupData['commission'] > 0) {
                $groupData['commission_action'] == 1 ? $discount_action = 'by_percent' : $discount_action = '';
                $discount_amount = $groupData['commission'];
                if ($discount_action == 'by_percent') {
                    $order_commission = $orderSubTotal*$discount_amount/100;
                } else {
                    $order_commission = $discount_amount;
                }
            }
        }
        if (!isset($order_commission) || $order_commission < 0) return;

        $incrementId = $order->getIncrementId();
        $data['account_id'] = $affiliate_id;
        $data['order_id'] = $order_id;
        $data['commission_total'] = $order_commission;
        $data['description'] = __('commision for order #') . $incrementId;

        $data['order_total'] = $orderSubTotal;
        $data['order_status'] = $order->getStatus();
        $data['customer_email'] = $order->getCustomerEmail();
        $data['base_currency_code'] = $order->getBaseCurrencyCode();

        $data['transaction_stt'] = $order->getStatus();
        $data['affiliate_code'] = $affiliateCode;
        $data['campaign_code'] = $campaignCode;

        $data['increment_id'] = $incrementId;
        $data['email_aff'] = $affiliate_email;

        $this->_helper->saveHistoryOrderAffiliate($data);
        // end event
    }
}
