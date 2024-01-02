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

namespace Lof\Affiliate\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\App\ObjectManager;

use Magento\SalesRule\Model\RuleFactory as RuleFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Lof\Affiliate\Helper\Trackcode;
use Lof\Affiliate\Model\ReferingcustomerAffiliateFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_scopeConfig;
    /**
     * @var Lof\Affiliate\Model\ResourceModel\BannerAffiliate\Collection
     */
    protected $_bannerCollectionFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceModel;


    protected $categoryCollectionFactory;
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Lof\Affiliate\Helper\Trackcode
     */
    protected $_trackcode;


    protected $_upload_file_path = "lof/affiliate/";

    protected $_upload_attachment_path = "lof/affiliate/attachments/";

    protected $_referringCustomer;

    protected $customer;

    protected $session;

    protected $_accountAffiliate;

    protected $catalogSession;

    protected $_objectManager;

    /**
     * @var EventManager
     */
    protected $_eventManager;

    protected $_serializeData;

    protected $ruleCollectionFactory;

    protected $_initTrackingCode = "";

    protected $_campaign_conditions = [];

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Lof\Affiliate\Model\ResourceModel\BannerAffiliate\CollectionFactory $bannerCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RuleFactory $ruleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceModel,
        \Magento\Framework\Serialize\Serializer\Json $serializeData,
        Timezone $stdTimezone,
        Trackcode $trackcode,
        ReferingcustomerAffiliateFactory $referringCustomer,
        Customer $customer,
        \Lof\Affiliate\Model\AccountAffiliateFactory $accountAffiliate,
        EventManager $eventManager
    ) {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
        $this->_blockFactory = $blockFactory;
        $this->_storeManager = $storeManager;
        $this->session = $customerSession;
        $this->catalogSession = $catalogSession;
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->_stdTimezone = $stdTimezone;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager = $messageManager;
        $this->_resourceModel = $resourceModel;
        $this->_serializeData = $serializeData;

        $this->_eventManager = $eventManager;
        $this->_trackcode = $trackcode;

        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->_referringCustomer = $referringCustomer;
        $this->customer = $customer;
        $this->_accountAffiliate = $accountAffiliate;
    }

    public function getCurrentAffiliateCode()
    {
        $affiliateCode = $this->catalogSession->getAffiliateCode();
        return $affiliateCode;
    }

    public function getCurrentAffiliateCampaignCode()
    {
        $campaignCode = $this->catalogSession->getCampaignCode();
        return $campaignCode;
    }

    public function getUploadFilePath()
    {
        return $this->_upload_file_path;
    }

    public function getUploadAttachmentPath()
    {
        return $this->_upload_attachment_path;
    }

    public function getAffiliateTrackingCode()
    {
        if (!isset($this->_initTrackingCode)) {
            $chunks = $this->getConfig("trackingcode_settings/chunks", null, 1);
            $letters = $this->getConfig("trackingcode_settings/letters", null, 9);
            $separate_text = $this->getConfig("trackingcode_settings/separate_text", null, "-");

            $this->_trackcode->numberChunks = (int)$chunks;
            $this->_trackcode->numberLettersPerChunk = (int)$letters;
            $this->_trackcode->separateChunkText = (int)$separate_text;
            $this->_initTrackingCode = true;
        }
        if ($this->getConfig("trackingcode_settings/use_customcode", null, 0)) {
            $prefix = $this->getConfig("trackingcode_settings/prefix");
            $surfix = $this->getConfig("trackingcode_settings/surfix");
            $serial_number = $this->_trackcode->generate();
            $serial_number = $prefix . $serial_number . $surfix;
        } else {
            $serial_number = uniqid();
        }

        return $serial_number;
    }

    public function getConfig($key, $store = null, $default = '')
    {
        $store = $this->_storeManager->getStore($store);
        //$websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofaffiliate/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);

        if ($result == "") {
            $result = $default;
        }

        return $result;
    }

    public function convertFlatToRecursive(array $data)
    {
        $arr = [];
        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = &$arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = &$node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * [validateRuleCart]
     * @return [type]
     */
    public function validateRuleCart($data, $campaign_code)
    {
        if (isset($data['quote'])) {
            $quote = $data['quote'];

            // get conditions campaign
            $conditions = $this->getConditionsByCampaignCode($campaign_code);
            $cond = $this->_serializeData->serialize($conditions);
            $row = [];
            $row['conditions_serialized'] = $cond;
            $row['actions_serialized'] = $cond;
            $ruleCollection = $this->ruleFactory->create();
            $ruleCollection->loadPost($row);

            // valid quote
            $conditions = $ruleCollection->getConditions();
            if (!$conditions->validate($quote)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $urlKey
     * @return mixed|null
     */
    protected function getCategoryReplacement($urlKey)
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $category = $categoryCollection->addAttributeToFilter('url_key', $urlKey)->getFirstItem();
        $categoryId = null;
        if (!empty($category)) {
            $categoryId = $category->getId();
        }
        return $categoryId;
    }

    // End Valid condition -------------------------------------------------------------------------------

    /**
     * [getListCampaignsByGroupID] select box value at frontend banners & links
     * @return [array] data
     */
    public function getListCampaignsByGroupID()
    {
        $data = [];

        $customer_id = $this->session->getCustomerId();
        $group_id = $this->getGroupIdByCustomerSession($customer_id);
        if ($group_id > 0) {
            $currentDate = $this->_stdTimezone->date()->format('Y-m-d');

            $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate')->getCollection();
            $listCampaigns = $campaignModel->addFieldToFilter('is_active', 1)
                ->addStoreFilter($this->_storeManager->getStore())
                ->addFieldToFilter('from_date', ['or' => [
                    0 => ['date' => true, 'lteq' => $currentDate],
                    1 => ['is' => new \Zend_Db_Expr('null')]]
                ], 'left')
                ->addFieldToFilter('to_date', ['or' => [
                    0 => ['date' => true, 'gteq' => $currentDate],
                    1 => ['is' => new \Zend_Db_Expr('null')]]
                ], 'left');
            $listCampaigns->getSelect()
                ->joinLeft(
                    [
                        'cat' => $this->_resourceModel->getTableName('lof_affiliate_campaign_group')],
                    'cat.campaign_id = main_table.campaign_id',
                    [
                        'campaign_id' => 'campaign_id',
                    ]
                )
                ->where('cat.group_id = (?)', $group_id);

            $value = $this->getConfig('general_settings/url_campaign_param_value');

            foreach ($listCampaigns as $campaign) {
                $data[] = [
                    'name' => $campaign['name'],
                    'value' => ($value == 1) ? $campaign['tracking_code'] : $campaign['campaign_id'],
                ];
            }
        }

        return $data;
    }

    public function checkValidCampaign($campaign_code)
    {
        $result = '';
        $currentDate = $this->_stdTimezone->date()->format('Y-m-d');
        $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate')->getCollection();
        $campaignModel->addFieldToFilter('is_active', 1)
            ->addStoreFilter($this->_storeManager->getStore())
            ->addFieldToFilter('tracking_code', $campaign_code)
            ->addFieldToFilter('from_date', ['or' => [
                0 => ['date' => true, 'lteq' => $currentDate],
                1 => ['is' => new \Zend_Db_Expr('null')],
                2 => ['date' => true, 'eq' => '']]
            ], 'left')
            ->addFieldToFilter('to_date', ['or' => [
                0 => ['date' => true, 'gteq' => $currentDate],
                1 => ['is' => new \Zend_Db_Expr('null')],
                2 => ['date' => true, 'eq' => '']]
            ], 'left');
        $data = $campaignModel->getData();
        if (!empty($data)) {
            $result = $campaign_code;
        }
        return $result;
    }

    public function getCurrentUrlPage()
    {
        $url = $this->_objectManager->get('Magento\Framework\UrlInterface')->getCurrentUrl();
        return $url;
    }

    // - save tracking code at table sales_order
    public function saveTrackingFromSalesOrder($order_id, $data)
    {
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($order_id);
        $order->setAffiliateCode($data['affiliate_code'])
            ->setCampaignCode($data['campaign_code'])
            ->setAffiliateParams($data['affiliate_params'])
            ->save();
        return true;
    }

    public function saveBalanceAffiliate($affiliate_code, $commission_total)
    {
        $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountAffiliate->loadByAttribute('tracking_code', $affiliate_code);
        $balance = $accountAffiliate->getBalance();

        $balance += $commission_total;

        $accountAffiliate->setBalance($balance);
        $accountAffiliate->save();

        return true;
    }

    public function cancelBalanceAffiliate($affiliate_code, $commission_total)
    {
        $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountAffiliate->loadByAttribute('tracking_code', $affiliate_code);

        $oldprice = $accountAffiliate->getBalance();
        $balance = $oldprice - $commission_total;

        $accountAffiliate->setBalance($balance);
        $accountAffiliate->save();

        return true;
    }

    // save history order id of customer when use tracking code + campaign code
    public function saveHistoryOrderAffiliate($data)
    {
        $transactionAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\TransactionAffiliate');
        $transactionAffiliate->setData($data);
        $transactionAffiliate->save();
        return true;
    }

    public function getDataOrderAffiliate($increment_id)
    {
        $transactionAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\TransactionAffiliate')->getCollection();
        $transactionAffiliate->addFieldToFilter('increment_id', $increment_id);
        $data = $transactionAffiliate->getData();
        return $data;
    }

    public function updateTransactionOrder($data)
    {
        $transactions = $this->_objectManager->create('Lof\Affiliate\Model\TransactionAffiliate')->getCollection();
        $transactions->addFieldToFilter('increment_id', $data['increment_id']);
        foreach ($transactions as $transaction) {
            $data['reason'] = isset($data['reason']) ? $data['reason'] : '';
            $transaction->setTransactionStt($data['transaction_stt'])
                ->setOrderStatus($data['order_status'])
                ->setIsActive($data['is_active'])
                ->setOrderId($data['order_id'])
                ->setReason($data['reason'])
                ->save();
        }

        return true;
    }
    // process check affiliate commission-------------------------------------------------------------------------------

    # 5. caculate commission for affiliate


    // save count++ order + price in table commission
    public function saveDataCommissionComplete($affiliate_code, $priceOrder, $commission)
    {
        $commissionTotal = $commission;
        $priceOrderTotal = $priceOrder;
        $orderTotal = 1;

        $commissionAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\CommissionAffiliate');
        $commissionAffiliate->loadByAttribute('affiliate_code', $affiliate_code);
        $data = $commissionAffiliate->getData();

        if (!empty($data)) {
            $priceOrderTotal += $commissionAffiliate->getPriceOrderTotal();
            $orderTotal += $commissionAffiliate->getOrderTotal();
            $commissionTotal += $commissionAffiliate->getCommission();
        }
        $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountAffiliate->loadByAttribute('tracking_code', $affiliate_code);

        $this->_eventManager->dispatch(
            'affiliate_comission_complete_before',
            [
            'commission' => $commissionTotal,
            'price_order_total' => $priceOrderTotal,
            'order_total' => $orderTotal,
            'affiliate_code' => $affiliate_code,
            'affiliate_account' => $accountAffiliate,
            'commission_affiliate' => $commissionAffiliate
            ]
        );
        $commissionAffiliate->setAffiliateCode($affiliate_code)
            ->setCommission($commissionTotal)
            ->setPriceOrderTotal($priceOrderTotal)
            ->setOrderTotal($orderTotal)
            ->save();


        $balance = $accountAffiliate->getBalance();
        $balance += $commission;

        $accountAffiliate->setBalance($balance);
        $accountAffiliate->save();

        //Check Withdrawal Auto
        if ($accountAffiliate->getWithdrawalAuto() == 1 && $balance >= $accountAffiliate->getAutoPaymentBalanceReaches()) {
            $customer_id = $accountAffiliate->getCustomerId();
            $customer_email = $accountAffiliate->getEmail();
            $customer = new \Magento\Framework\DataObject();
            $customer->setId($customer_id);
            $customer->setEmail($customer_email);
            $requestAmount = $balance - $accountAffiliate->getReserveLevel();
            $currency_code = $this->_storeManager->getStore()->getCurrentCurrencyCode();
            $payment_method = $accountAffiliate->getDefaultPaymentMethod();

            try {
                $this->saveWithdraw($requestAmount, $customer, $currency_code, $payment_method);

                $this->_accountAffiliate->create()->updateBalance($requestAmount, $customer);
            } catch (\Exception $e) {
                $this->messageManager->addException($e);
            }
        }
        $this->_eventManager->dispatch(
            'affiliate_comission_complete_after',
            [
            'commission' => $commissionTotal,
            'price_order_total' => $priceOrderTotal,
            'order_total' => $orderTotal,
            'balance' => $balance,
            'affiliate_code' => $affiliate_code,
            'affiliate_account' => $accountAffiliate,
            'commission_affiliate' => $commissionAffiliate
            ]
        );
        return true;

    }

    public function saveDataCommissionCancel($affiliate_code, $priceOrder, $commission)
    {
        $commissionAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\CommissionAffiliate');
        $commissionAffiliate->loadByAttribute('affiliate_code', $affiliate_code);
        $data = $commissionAffiliate->getData();

        if (!empty($data)) {
            $commissionTotal = $commissionAffiliate->getCommission() - $commission;
            $priceOrderTotal = $commissionAffiliate->getPriceOrderTotal() - $priceOrder;
            $orderTotal = $commissionAffiliate->getOrderTotal() - 1;

            $commissionAffiliate->setCommission($commissionTotal)
                ->setPriceOrderTotal($priceOrderTotal)
                ->setOrderTotal($orderTotal)
                ->save();
        }
        return true;

    }

    // get commission by campaign code
    public function getCommissionByCampaignCode($campaign_code)
    {
        $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
        $campaignModel->loadByAttribute('tracking_code', $campaign_code);

        $commission = $campaignModel->getCommission();

        return $commission;
    }

    // check form date => to date of campaign
    public function getListCampaignByCurrentDate($campaign_code, $currentDate)
    {
        $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
        $campaign = $campaignModel->getListCampaignsByDate($campaign_code, $currentDate);
        return $campaign;
    }

    public function caculateCommission($commissions, $orderTotalPrice, $orderTotalNumber, $priceOrder, $orderCommission)
    {
        $arrResult = [];
        foreach ($commissions as $commission) {
            // if type total number of all orders
            if ($commission['typeOrderNext'] == 0) {
                if ($orderTotalNumber > $commission['typeOrderValueNext']) {
                    if ($commission['typeOrder'] == 0) {
                        // caculate price with %
                        $result = $priceOrder * $commission['typeOrderValue'] / 100;
                        array_push($arrResult, $result);
                    } else {
                        // caculate price
                        $result = $commission['typeOrderValue'];
                        array_push($arrResult, $result);
                    }
                }
            }
            // if type total price of all orders
            if ($commission['typeOrderNext'] == 1) {
                if ($orderTotalPrice > $commission['typeOrderValueNext']) {
                    if ($commission['typeOrder'] == 0) {
                        // caculate price with %
                        $result = $priceOrder * $commission['typeOrderValue'] / 100;
                        array_push($arrResult, $result);
                    } else {
                        // caculate price
                        $result = $commission['typeOrderValue'];
                        array_push($arrResult, $result);
                    }
                    //echo "<pre>result b " . $i . ': '; print_r($result);
                }
            }
        }
        empty($arrResult) ? $max = $orderCommission : $max = max($arrResult);
        return $max;
    }
    // end process -------------------------------------------------------------------------------

    /**
     * [getListProductsByOrderID]
     * @return [array]
     */
    public function getListProductsByOrderID($order_id)
    {
        $orderItems = [];

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($order_id);
        $orderItems = $order->getAllItems();

        return $orderItems;
    }

    public function getGroupIdByCustomerSession($customer_id)
    {
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $group_id = $accountModel->getGroupId($customer_id);
        return $group_id;
    }

    public function getGroupDataByAffiliateId($affiliateCode)
    {
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate')->load($affiliateCode, 'tracking_code');
        $account = $accountModel->getData();
        $groupModel = $this->_objectManager->create('Lof\Affiliate\Model\GroupAffiliate')->load($account['group_id'], 'group_id');
        $group_data = $groupModel->getData();
        return $group_data;
    }

    public function getCampaignByTrackingCode($tracking_code)
    {
        $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
        $campaignModel->loadByAttribute('tracking_code', $tracking_code);
        return $campaignModel;
    }

    // - end function event

    public function getConditionsByCampaignID($campaign_id)
    {
        $result = [];
        $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
        $model = $campaignModel->loadByAttribute('campaign_id', $campaign_id);
        $conditions = $model->getConditions();
        if (!empty($conditions)) {
            $result = unserialize($conditions);
        }
        return $result;
    }

    public function getConditionsByCampaignCode($campaign_code)
    {
        if (!isset($this->_campaign_conditions)) {
            $this->_campaign_conditions = [];
        }
        if (!isset($this->_campaign_conditions[$campaign_code])) {
            $result = [];
            $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
            $model = $campaignModel->loadByAttribute('tracking_code', $campaign_code);
            $conditions = $model->getConditions();
            if (!empty($conditions)) {
                $result = unserialize($conditions);
            }
            $this->_campaign_conditions[$campaign_code] = isset($result['conditions']) ? $result : '';
        }
        return $this->_campaign_conditions[$campaign_code];
    }

    /**
     * [symbolPrice]
     * @param  [int] $price
     * @return [int]
     */
    public function symbolPrice($price)
    {
        $currencyModel = $this->_objectManager->create('\Magento\Framework\Pricing\PriceCurrencyInterface');
        $result = $currencyModel->format($price, true, 0);
        return $result;
    }

    /**
     * [getComissionByCampaignID]
     * @param  [int] $campaign_id
     * @return [collection]
     */
    public function getComissionByCampaignID($campaign_id)
    {
        $result = [];
        $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
        $model = $campaignModel->loadByAttribute('campaign_id', $campaign_id);
        $commissions = $model->getCommission();

        if (!empty($commissions)) {

            foreach ($commissions as $commission) {
                if ($commission['typeOrder'] == 0) {
                    $label = __('Percentage of current order');
                    $typeOrderValue = $commission['typeOrderValue'] . __('% for each order');
                    if ($commission['typeOrderNext'] == 0) {
                        $content = __(' number of total order greater than ');
                    } else {
                        $content = __(' price of total order greater than ');
                    }
                    $typeOrderValueNext = __(' if') . $content . $commission['typeOrderValueNext'] . '.';

                } else {
                    $label = __('Fixed amount');
                    $typeOrderValue = $this->symbolPrice($commission['typeOrderValue']) . __(' for each order');
                    if ($commission['typeOrderNext'] == 0) {
                        $content = __(' number of total order greater than ');
                    } else {
                        $content = __(' price of total order greater than ');
                    }
                    $typeOrderValueNext = __(' if') . $content . $commission['typeOrderValueNext'] . '.';
                }
                $result[] = [
                    'label' => $label,
                    'typeOrderValue' => $typeOrderValue,
                    'typeOrderValueNext' => $typeOrderValueNext,
                ];
            }
        }
        return $result;
    }

    /**
     * [getListCampaigns]
     * @return [array]
     */
    public function getTableDataCampaigns()
    {

        $rows = [];

        $customer_id = $this->session->getCustomerId();
        $group_id = $this->getGroupIdByCustomerSession($customer_id);
        if ($group_id > 0) {
            $currentDate = $this->_stdTimezone->date()->format('Y-m-d');
            $campaignModel = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate')->getCollection();
            $listCampaigns = $campaignModel->addFieldToFilter('is_active', 1)
                ->addStoreFilter($this->_storeManager->getStore())
                ->addFieldToFilter('from_date', ['or' => [
                    0 => ['date' => true, 'lteq' => $currentDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                    2 => ['date' => true, 'eq' => '']]
                ], 'left')
                ->addFieldToFilter('to_date', ['or' => [
                    0 => ['date' => true, 'gteq' => $currentDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                    2 => ['date' => true, 'eq' => '']]
                ], 'left');
            $listCampaigns->getSelect()
                ->joinLeft(
                    [
                        'cat' => $this->_resourceModel->getTableName('lof_affiliate_campaign_group')],
                    'cat.campaign_id = main_table.campaign_id',
                    [
                        'campaign_id' => 'campaign_id',
                    ]
                )
                ->where('cat.group_id = (?)', $group_id);

            $rows = $listCampaigns->getData();
        }


        return $rows;
    }


    /**
     * check tracking code
     */
    public function checkAffiliateCode()
    {
        $currentCode = $this->catalogSession->getAffiliateCode();
        $customer_id = $this->session->getCustomerId();

        $AccountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $AccountCollection->loadByAttribute('customer_id', $customer_id);

        $customerCode = $AccountCollection->getTrackingCode();
        if ($currentCode == $customerCode) {
            return true;
        }
        return false;
    }

    /**
     * check tracking code
     * @param string $currentCode
     */
    public function checkSalesAffiliateCode($currentCode = "")
    {
        $AccountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $AccountCollection->loadByAttribute('tracking_code', $currentCode);
        if ($AccountCollection->getId() != null) {
            return true;
        }
        return false;
    }

    /**
     * Get TrackingCode
     *
     * @param
     * @return customer_id or trackingCode
     */
    public function getTrackingCode()
    {
        $customer_id = $this->session->getCustomerId();
        $url_param_value = $this->getUrlParamValue();
        if ($url_param_value == 2) {
            return $customer_id;
        } else {
            $AccountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
            $AccountCollection->loadByAttribute('customer_id', $customer_id);
            $trackingCode = $AccountCollection->getTrackingCode();
            if (!empty($trackingCode)) {
                return $trackingCode;
            }
        }
        return $customer_id;
    }

    /**
     * [countedClick]
     * @param  [int] $banner_id
     * @return [boolean]
     */
    public function countedClickBanner($banner_id, $affiliate_code)
    {
        $bannerModel = $this->_objectManager->create('Lof\Affiliate\Model\BannerAffiliate')->load($banner_id);
        $banner = $bannerModel->getData();
        if ($banner['is_active'] == 1) {

            $base_currency_code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

            $is_unique = 0;
            $commission = $banner['click_raw_commission'];
            $click_unique = 0;

            $current_ip = $this->getIp();
            $ppcModel = $this->_objectManager->create('Lof\Affiliate\Model\PpcAffiliate')->getCollection();
            $ppcModel->addFieldToFilter('banner_id', $banner_id)
                ->addFieldToFilter('affiliate_code', $affiliate_code)
                ->addFieldToFilter('customer_ip', $current_ip);

            if (count($ppcModel) < 1) {
                $is_unique = 1;
                $commission = $banner['click_unique_commission'];
                $click_unique = 1;
            }

            $Model = $this->_objectManager->create('Lof\Affiliate\Model\PpcAffiliate');

            $Model->setBannerId($banner_id)
                ->setAffiliateCode($affiliate_code)
                ->setCustomerIp($current_ip)
                ->setIsUnique($is_unique)
                ->setCommission($commission)
                ->setBaseCurrencyCode($base_currency_code)
                ->save();

            $bannerModel->setClickRaw($banner['click_raw'] + 1)
                ->setClickUnique($banner['click_unique'] + $click_unique)
                ->setExpense($banner['expense'] + $commission)
                ->save();

            $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate')
                ->loadByAttribute('tracking_code', $affiliate_code);
            $account = $accountModel->getData();
            $account['balance'] = isset($account['balance']) ? (float)$account['balance'] : 0.00;
            $accountModel->setBalance($account['balance'] + $commission)
                ->save();
        }

        return true;
    }

    /**
     * [isLocalhost]
     * @return [boolean]
     */
    public function isLocalhost($remote_addr)
    {
        $whitelist = ['localhost', '127.0.0.1', '::1'];
        if (in_array($remote_addr, $whitelist)) {
            return true;
        }
        return false;
    }

    /**
     * [getAllBanners]
     * @return [collection]
     */
    public function getAllBanners()
    {
        $data = $this->_bannerCollectionFactory->create();
        $data->addFieldToFilter('is_active', 1);
        $banners = $data->getData();

        // get tracking code & param code
        $trackingcode = $this->getTrackingCode();
        $param_code = $this->getParamCode();

        // foreach ($banners as &$banner) {
        //     $banner['link'] = $banner['link'].'?'.$param_code.'='.$trackingcode.'&bannerid='.$banner['banner_id'];
        // }
        return $banners;
    }

    /**
     * [getCampaignParamCode]
     * @return [code]
     */
    public function getCampaignParamCode()
    {
        $code = $this->getConfig('general_settings/url_campaign_param');
        $code = $code ? $code : 'cam';
        return $code;
    }

    /**
     * [getParamCode]
     * @return [code]
     */
    public function getParamCode()
    {
        $code = $this->getConfig('general_settings/url_param');
        $code = $code ? $code : 'code';
        return $code;
    }

    public function getUrlParamValue()
    {
        $url_param_value = $this->getConfig('general_settings/url_param_value');
        return $url_param_value;
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    }

    public function getShortUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        $result = preg_replace("(^https?://)", "", $url);
        return $result;
    }

    public function getReplaceUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    }

    public function getAffiliateIdByCode($affiliateCode)
    {
        $AccountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $AccountCollection->loadByAttribute('tracking_code', $affiliateCode);
        $accountaffiliate_id = $AccountCollection->getAccountaffiliateId();
        return $accountaffiliate_id;
    }

    public function getAffiliateCommissionByCode($affiliateCode)
    {
        $AccountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $AccountCollection->loadByAttribute('tracking_code', $affiliateCode)
            ->getData();
        $group_id = $AccountCollection['group_id'];
        $GroupCollection = $this->_objectManager->create('Lof\Affiliate\Model\GroupAffiliate');
        $GroupCollection->load($group_id)->getData();
        $commission = $GroupCollection['commission'];
        return $commission;
    }

    /**
     * get email affilate by code
     *
     * @param string $affiliateCode
     * @return string
     */
    public function getEmailAffiliateByCode($affiliateCode)
    {
        $AccountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $AccountCollection->loadByAttribute('tracking_code', $affiliateCode);
        $email = $AccountCollection->getEmail();
        return $email;
    }

    /**
     * create affiliate account
     *
     * @param mixed $data
     * @param mixed $data
     * @return \Lof\Affiliate\Model\AccountAffiliate
     */
    public function createAffiliateAccount($data, $customerData)
    {
        $fullname = $customerData->getFirstname() . ' ' . $customerData->getLastname();

        /** @var \Lof\Affiliate\Model\AccountAffiliate|\Lof\Affiliate\Api\Data\AccountInterface */
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountModel->load($customerData->getEmail(), 'email');
        if (!$accountModel->getId()) {
            $tracking_code = $this->getAffiliateTrackingCode();
            $default_group_id = $this->getConfig("general_settings/default_affilate_group", null, 1);
            $auto_active_account = $this->getConfig("general_settings/auto_active_account", null, 0);


            if (!empty($data)) {
                $bank_account_name = $bank_account_number = $swift_code = $bank_name = $bank_branch_city = $bank_branch_country_code = $intermediary_bank_code = $intermediary_bank_name = $intermediary_bank_city = $intermediary_bank_country_code = "";

                if (isset($data['bank_account_name'])) {
                    $bank_account_name = $data['bank_account_name'];
                }
                if (isset($data['bank_account_number'])) {
                    $bank_account_number = $data['bank_account_number'];
                }
                if (isset($data['bank_name'])) {
                    $bank_name = $data['bank_name'];
                }
                if (isset($data['swift_code'])) {
                    $swift_code = $data['swift_code'];
                }
                if (isset($data['bank_branch_city'])) {
                    $bank_branch_city = $data['bank_branch_city'];
                }
                if (isset($data['bank_branch_country_code'])) {
                    $bank_branch_country_code = $data['bank_branch_country_code'];
                }
                if (isset($data['intermediary_bank_code'])) {
                    $intermediary_bank_code = $data['intermediary_bank_code'];
                }
                if (isset($data['intermediary_bank_name'])) {
                    $intermediary_bank_name = $data['intermediary_bank_name'];
                }
                if (isset($data['intermediary_bank_city'])) {
                    $intermediary_bank_city = $data['intermediary_bank_city'];
                }
                if (isset($data['intermediary_bank_country_code'])) {
                    $intermediary_bank_country_code = $data['intermediary_bank_country_code'];
                }
                $accountModel
                    ->setDefaultPaymentMethod(isset($data['payment']) ? $data['payment'] : "paypal_email")
                    ->setPaypalEmail($data['paypal_email'])
                    ->setSkrillEmail($data['skrill_email'])
                    ->setBankAccountName($bank_account_name)
                    ->setBankAccountNumber($bank_account_number)
                    ->setBankName($bank_name)
                    ->setSwiftCode($swift_code)
                    ->setBankBranchCity($bank_branch_city)
                    ->setBankBranchCountryCode($bank_branch_country_code)
                    ->setIntermediaryBankCode($intermediary_bank_code)
                    ->setIntermediaryBankName($intermediary_bank_name)
                    ->setIntermediaryBankCity($intermediary_bank_city)
                    ->setIntermediaryBankCountryCode($intermediary_bank_country_code)
                    ->setReferingWebsite(isset($data['refering_website']) ? $data['refering_website'] : "");
            }

            $accountModel->setFirstname($customerData->getFirstname())
                ->setLastname($customerData->getLastname())
                ->setCommissionPaid('0')
                ->setEmail($customerData->getEmail())
                ->setTrackingCode($tracking_code)
                ->setCustomerId($customerData->getId())
                ->setGroupId((int)$default_group_id)
                ->setIsActive((int)$auto_active_account)
                ->setFullname($fullname)
                ->setBalance('0')
                ->save();
        }
        return $accountModel;
    }

    /**
     * get loaded account info by logged in customer
     * @return \Lof\Affiliate\Model\AccountAffiliate|\Lof\Affiliate\Api\Data\AccountInterface
     */
    public function getLoadAccountInfo()
    {
        $customerId = $this->session->getCustomerId();
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountModel->loadByAttribute('customer_id', $customerId);
        return $accountModel;
    }

    /**
     * save withdrawl
     * @param mixed $request
     * @param \Magento\Customer\Model\Customer|mixed
     * @param string $currencyCode
     * @param string $paymentMethod
     * @return \Lof\Affiliate\Model\WithdrawAffiliate
     */
    public function saveWithdraw($request, $customer, $currencyCode, $paymentMethod)
    {
        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();
        //$createTimeNow = $this->_stdTimezone->date()->format('Y-m-d');
        $accountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountCollection->loadByAttribute('customer_id', $customerId);
        $accountId = $accountCollection->getId();
        $paypal_email = $accountCollection->getPaypalEmail();
        $bank_information = "";
        $other_payment = "";
        if ($paymentMethod == "banktransfer") {
            $bank_data = [];
            $bank_data['bank_account_name'] = $accountCollection->getBankAccountName();
            $bank_data['bank_account_number'] = $accountCollection->getBankAccountNumber();
            $bank_data['bank_name'] = $accountCollection->getBankName();
            $bank_data['swift_code'] = $accountCollection->getSwiftCode();
            $bank_data['bank_branch_city'] = $accountCollection->getBankBranchCity();
            $bank_data['bank_branch_country_code'] = $accountCollection->getBankBranchCountryCode();
            $bank_data['intermediary_bank_code'] = $accountCollection->getIntermediaryBankCode();
            $bank_data['intermediary_bank_name'] = $accountCollection->getIntermediaryBankName();
            $bank_data['intermediary_bank_city'] = $accountCollection->getIntermediaryBankCity();
            $bank_data['intermediary_bank_country_code'] = $accountCollection->getIntermediaryBankCountryCode();

            $bank_information = serialize($bank_data);
        }
        /** @var \Lof\Affiliate\Model\WithdrawAffiliate */
        $withdrawCollection = $this->_objectManager->create('Lof\Affiliate\Model\WithdrawAffiliate');
        $withdrawCollection->setWithdrawAmount($request)
            ->setCustomerId($customerId)
            ->setAccountId($accountId)
            ->setStatus('pending')
            ->setAffiliateEmail($customerEmail)
            ->setCurrencyCode($currencyCode)
            ->setPaymentMethod($paymentMethod)
            ->setPaypalEmail($paypal_email)
            ->setBanktransferData($bank_information)
            ->setOtherPaymentData($other_payment)
            ->save();
        return $withdrawCollection;
    }

    /**
     * send email
     *
     * @param mixed $emailFrom
     * @param mixed $emailTo
     * @param mixed $emailIdentifier
     * @param mixed $templateVar
     * @return void
     */
    public function sendMail($emailFrom, $emailTo, $emailIdentifier, $templateVar)
    {
        $this->inlineTranslation->suspend();
        $transport = $this->_transportBuilder->setTemplateIdentifier($emailIdentifier)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($templateVar)
            ->setFrom($emailFrom)
            ->addTo($emailTo)
            ->setReplyTo($emailTo)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * builder payment field
     *
     * @param mixed|array
     * @return string
     */
    public function buildPaymentField($payment = [])
    {
        $html = '';
        $html .= '<input type="text" name="' . $payment['name'] . '" id="' . $payment['name'] . '" title="' . __($payment['title']) . '" class="input-text" data-validate="' . $payment['validate'] . '" autocomplete="' . $payment['autocomplete'] . '" ' . ((isset($payment['style']) && $payment['style']) ? ' style="' . $payment['style'] . '"' : '') . '>';
        return $html;
    }

    /**
     * check pay per lead commission
     *
     * @param string $affiliateCode
     * @param string $campaignCode
     * @param mixed|array $customerData
     * @return bool
     */
    public function checkPPLCommission($affiliateCode, $campaignCode, $customerData)
    {
        if ($campaignCode != '' && $affiliateCode != '') {

            $campaignAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');
            $campaignAffiliate->loadByAttribute('tracking_code', $campaignCode);
            $campaignData = $campaignAffiliate->getData();

            $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
            $accountAffiliate->loadByAttribute('tracking_code', $affiliateCode);
            $accountData = $accountAffiliate->getData();

            if ($campaignData['enable_ppl'] == 1) {
                // Check Limit quantity of signup new account for each Affiliater
                $leadCollection = $this->_objectManager->create('Lof\Affiliate\Model\LeadAffiliate')->getCollection()
                    ->addFieldToFilter('account_id', $accountData['accountaffiliate_id']);

                $total_balance = $campaignData['signup_commission'];
                $account_number = 1;
                $current_ip = $this->getIp();
                foreach ($leadCollection as $value) {
                    $lead = $value->getData();
                    $total_balance += $lead['signup_commission'];
                    if ($current_ip == $lead['customer_ip']) {
                        $account_number += 1;
                    }
                }

                if ((count($leadCollection) + 1) <= $campaignData['limit_account'] && $total_balance <= $campaignData['limit_balance'] && $account_number <= $campaignData['limit_account_ip']) {

                    // Create Lead Affiliate
                    $leadCollection->addFieldToFilter('customer_email', $customerData->getEmail());

                    $lead_data = $leadCollection->getData();
                    if (empty($lead_data)) {
                        $LeadAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\LeadAffiliate');
                        $LeadAffiliate->setAccountId($accountData['accountaffiliate_id'])
                            ->setCustomerEmail($customerData->getEmail())
                            ->setCustomerIp($current_ip)
                            ->setSignupCommission($campaignData['signup_commission'])
                            ->setSignupNumber(1)
                            ->setBaseCurrencyCode($this->_storeManager->getStore()->getCurrentCurrency()->getCode());
                        $LeadAffiliate->save();

                        // Update balance
                        $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
                        $accountAffiliate->loadByAttribute('tracking_code', $accountData['tracking_code']);
                        $accountData = $accountAffiliate->getData();
                        $newBalance = (float)$accountData['balance'] + (float)$campaignData['signup_commission'];
                        $accountAffiliate->setBalance((string)$newBalance)->save();

                        $this->_eventManager->dispatch(
                            'affiliate_comission_complete_signup',
                            [
                            'commission' => $campaignData['signup_commission'],
                            'balance' => $accountAffiliate->getBalance(),
                            'affiliate_code' => $accountData['tracking_code'],
                            'affiliate_account' => $accountAffiliate
                            ]
                        );
                    }
                }
            }
        }
        return true;
    }

    /**
     * get client ip address
     * @return string
     */
    public function getIp()
    {
        return $this->_remoteAddress->getRemoteAddress();
    }

    /**
     * add refering customer
     *
     * @param string $affiliateCode
     * @param string $customerEmail
     * @param string $campaignCode
     * @return bool
     */
    public function addReferingCustomer($affiliateCode, $customerEmail, $campaignCode = "")
    {
        $referingcustomerAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\ReferingcustomerAffiliate');
        $referingcustomerAffiliate->loadByAttribute('refering_customer_email', $customerEmail);
        $currentDate = date("Y-m-d");
        $expiryDate = $this->getConfig('general_settings/expiry_date_commission_refer');
        $expiryDate = date('Y-m-d', strtotime($currentDate . ' + ' . $expiryDate . ' days'));
        $data = $referingcustomerAffiliate->getData();
        if (empty($data)) {
            $referingcustomerAffiliate->setReferingCustomerEmail($customerEmail)
                ->setAffiliateCode($affiliateCode)
                ->setCampaignCode($campaignCode)
                ->setExpiryDateCommission($expiryDate)
                ->save();
        }
        return true;
    }

    /**
     * get account data by customer email
     *
     * @param string $customerEmail
     * @return mixed|array
     */
    public function getAccountDataByCustomerEmail($customerEmail)
    {
        $referingCustomer = $this->_objectManager->create('Lof\Affiliate\Model\ReferingcustomerAffiliate')
            ->loadByAttribute('refering_customer_email', $customerEmail);
        $data = $referingCustomer->getData();
        if (!empty($data)) {
            $accountCollection = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate')
                ->load($data['affiliate_code'], 'tracking_code');
            $accountData = $accountCollection->getData();
            return $accountData;
        }
        return [];
    }

    /**
     * get banner detail
     *
     * @param int $bannerId
     * @param string $trackingCode
     * @return mixed|array
     */
    public function getBannerDetail($bannerId, $trackingCode)
    {
        $bannerDetail = $this->_objectManager->create('Lof\Affiliate\Model\PpcAffiliate')->getCollection();
        $bannerDetail->addFieldToFilter('banner_id', $bannerId)
            ->addFieldToFilter('affiliate_code', $tracking_code);
        $data = $bannerDetail->getData();
        $banner_data = [];
        $banner_data['income'] = 0;
        $banner_data['raw'] = count($bannerDetail);
        $banner_data['unique'] = 0;
        foreach ($data as $value) {
            $banner_data['income'] += $value['commission'];
            if ($value['is_unique'] == 1) {
                $banner_data['unique'] += 1;
            }
        }
        return $banner_data;
    }

    /**
     * get data referring customer by email
     *
     * @param string $customerEmail
     * @return mixed
     */
    public function getDataReferringCustomerByEmail($customerEmail)
    {
        $referringCustomerModel = $this->_referringCustomer->create();
        $referringCustomer = $referringCustomerModel->loadByAttribute('refering_customer_email', $customerEmail);
        return $referringCustomer->getData();
    }

    /**
     * get affiliate account by current logged inc ustomer
     *
     * @return \Lof\Affiliate\Model\AccountAffiliate|\Lof\Affiliate\Api\Data\AccountInterface
     */
    public function getAffiliateAccount()
    {
        $customer_id = $this->session->getCustomerId();
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountModel->loadByAttribute('customer_id', $customer_id);
        return $accountModel;
    }

    /**
     * get affiliate account by tracking code
     *
     * @param string $affilateCode
     * @return \Lof\Affiliate\Model\AccountAffiliate|\Lof\Affiliate\Api\Data\AccountInterface
     */
    public function getAffiliateAccountByTrackingCode($affiliateCode)
    {
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountModel->loadByAttribute('tracking_code', $affiliateCode);
        return $accountModel;
    }

    /**
     * get data customer by email
     *
     * @param string $email
     * @return \Magento\Customer\Model\Customer|bool
     */
    public function getDataCustomerByEmail($email)
    {
        $customer = $this->customer;
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * check spam
     * @param mixed $orderData
     * @return bool
     */
    public function checkSpam($orderData)
    {
        $customerIp = $orderData['customer_ip'];
        $customerEmail = $orderData['customer_email'];
        $transactionAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\TransactionAffiliate')->getCollection();
        $transactionAffiliate->addFieldToFilter('customer_ip', $customerIp)->addFieldToFilter('customer_email', $customerEmail);
        $data = $transactionAffiliate;
        if ($data->count() > 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get payment method
     *
     * @return mixed|array
     */
    public function getPaymentMethod()
    {
        return [
            ['value' => 'paypal', 'label' => __('Paypal')],
            ['value' => 'banktransfer', 'label' => __('Bank Transfer')],
        ];
    }
}

