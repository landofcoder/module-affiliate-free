<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Model;

class CampaignAffiliate extends \Magento\Rule\Model\AbstractModel
{

    /**
     * Store rule combine conditions model
     *
     * @var \Magento\Rule\Model\Condition\Combine
     */
    protected $_conditions;

    /**
     * Store rule actions model
     *
     * @var \Magento\Rule\Model\Action\Collection
     */
    protected $_actions;

    /**
     * Store rule form instance
     *
     * @var \Magento\Framework\Data\Form
     */
    protected $_form;

    /**
     * Is model can be deleted flag
     *
     * @var bool
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var bool
     */
    protected $_isReadonly = false;

    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * URL Model instance
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_campaignlHelper;
    protected $_campaignFactory;

    protected $_resource;
    protected $_resourceModel;

    protected $session;



    /**
     * Rule type actions
     */
    const TO_PERCENT_ACTION = 'to_percent';

    const BY_PERCENT_ACTION = 'by_percent';

    const TO_FIXED_ACTION = 'to_fixed';

    const BY_FIXED_ACTION = 'by_fixed';

    const CART_FIXED_ACTION = 'cart_fixed';

    const BUY_X_GET_Y_ACTION = 'buy_x_get_y';


    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';
    

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_condCombineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_condProdCombineF;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = [];


    /**
     * Form factory
     *
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Timezone instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,

        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,

        \Lof\Affiliate\Model\ResourceModel\CampaignAffiliate $resource = null,
        \Magento\Framework\App\ResourceConnection $resourceModel,

        \Lof\Affiliate\Model\ResourceModel\CampaignAffiliate\Collection $resourceCollection = null,
        \Lof\Affiliate\Model\ResourceModel\CampaignAffiliate\CollectionFactory $campaignFactory,
        \Lof\Affiliate\Helper\Data $campaignlHelper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,

        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,

        \Magento\Customer\Model\Session $customerSession,

        array $data = []
        ) {
        $this->_resource = $resource;
        $this->_resourceModel = $resourceModel;

        // $this->_formFactory = $formFactory;
        // $this->_localeDate = $localeDate;

        $this->_campaignlHelper = $campaignlHelper;
        $this->_campaignFactory = $campaignFactory;

        $this->_storeManager = $storeManager;
        $this->_url = $url;

        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;

        $this->session = $customerSession;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Lof\Affiliate\Model\ResourceModel\CampaignAffiliate');
        $this->setIdFieldName('campaign_id');
    }

    

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }
    /**
     * Get sales rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Get Rule label by specified store
     *
     * @param \Magento\Store\Model\Store|int|bool|null $store
     * @return string|bool
     */
    public function getStoreLabel($store = null)
    {
        $storeId = $this->_storeManager->getStore($store)->getId();
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * Set if not yet and retrieve rule store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }
    /**
     * @return string
     */
    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    /**
     * @return string
     */
    public function getToDate()
    {
        return $this->getData('to_date');
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param Address $address
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }



    //--My FUNCTION ================================================================================



    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'campaign_id="' . $this->getId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that category content does not reference the block itself.')
            );
    }

    /**
     * [loadByAttribute]
     * @param  [type] $attribute
     * @param  [type] $value    
     * @return [type]           
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    public function loadListByAttribute($display_is_guest, $group_id = 0){

        $rows = array();
        $table_name = $this->_resourceModel->getTableName('lof_affiliate_campaign');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from( ['ca' => $table_name] );

        if($display_is_guest == '1') {
            // Allow Guest see
            $select->where('ca.display = ?', $display_is_guest);
        } else {
            $select->where('ca.display = ?', $display_is_guest)->where( 'ca.group_id = ?', $group_id );
        }

        $rows = $connection->fetchAll($select);

        //echo "<pre>"; print_r($rows); die;

        return $rows;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * [getYesNoField]
     * @return [array]
     */
    public function getYesNoField(){
        return [self::STATUS_ENABLED => __('Yes'), self::STATUS_DISABLED => __('No')];
    }

    /**
     * [getDiscountField]
     * @return [array]
     */
    public function getDiscountField(){
        return ['by_percent' => __('Percent of current cart total'), 'cart_fixed' => __('Fixed Amount Commission For Whole Cart')];
    }

    /**
     * [getDisplayField]
     * @return [array]
     */
    public function getDisplayField(){
        return [self::STATUS_ENABLED => __('Allow Guest'), self::STATUS_DISABLED => __('Affiliate Member Only')];
    }

    /**
     * [getGroupType]
     * @return [array]
     */
    public function getGroupType()
    {

        $data = array();

        $table_name = $this->_resourceModel->getTableName('lof_affiliate_group');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
                ['ce' => $table_name],
                ['group_id', 'name']
            );
        $rows = $connection->fetchAll($select);

        foreach ($rows as $key => $result) {
            $data[$result['group_id']] = $result['name'];
        }
        if (empty($data)) {
            return [0 => 'Default'];
        }

        return $data;
    }

    /**
     * [getListCampaigns]
     * @param  [int] $group_id
     * @return [array]          
     */
    public function getListCampaigns($group_id){

        $table_name = $this->_resourceModel->getTableName('lof_affiliate_campaign');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
                ['ca' => $table_name]
            );
        $select->where('ca.group_id = ?', $group_id)
        ->where('ca.is_active=?',1);

        $rows = $connection->fetchAll($select);

        //echo "<pre>"; print_r($rows); die;

        return $rows;
    }

    public function getListCampaignsByDate($campaign_code, $currentDate){

        $table_name = $this->_resourceModel->getTableName('lof_affiliate_campaign');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
                ['ca' => $table_name]
            );
        $select->where('ca.to_date >= "'.$currentDate.'"');
        $select->where('ca.from_date < "'.$currentDate.'"');
        $select->where('ca.tracking_code = ?', $campaign_code);

        $rows = $connection->fetchRow($select);

        return $rows;
    }
}
