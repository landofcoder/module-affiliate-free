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

use Magento\Customer\Model\Session;

class AccountAffiliate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Blog's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;


    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_accountlHelper;

    protected $_resource;

    protected $_accountFactory;

    protected $_resourceModel;

    /**
     * @var Session
     */
    protected $session;
    /**
     * Page cache tag
     */
    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Ves\Blog\Model\ResourceModel\Blog|null                      $resource                 
     * @param \Ves\Blog\Model\ResourceModel\Blog\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Ves\Blog\Helper\Data                                    $brandHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\Collection $resourceCollection = null,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\CollectionFactory $accountFactory,
        \Magento\Framework\UrlInterface $url,
        \Lof\Affiliate\Helper\Data $accountlHelper,
        Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resourceModel,
        array $data = []
        ) {
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->_accountlHelper = $accountlHelper;
        $this->_accountFactory = $accountFactory;
        $this->_resourceModel = $resourceModel;
        $this->session = $customerSession;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Affiliate\Model\ResourceModel\AccountAffiliate');
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'accountaffiliate_id="' . $this->getId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that category content does not reference the block itself.')
            );
    }

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

        return $data;
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

    public function getGroupID($customer_id){
        $data = array();

        $account = $this->load($customer_id, 'customer_id');

        $data = $account->getData();

        if(!empty($data)){
            return $data['group_id'];
        } else {
            return -1;
        }
    }

    public function checkExistAffiliate($customer_id){
        
        $data = array();

        $account = $this->load($customer_id, 'customer_id');

        $data = $account->getData();

        return $data;
    }

    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    public function updateAffiliateInformation($customer, $customerData)
    {
        $customer_id = $customer->getId();

        $collection = $this->loadByAttribute('customer_id', $customer_id);

        // trackingcode
        $check = $this->checkExistAffiliate($customer_id);
        if(empty($check)) {
            $tracking_code = $this->_accountlHelper->getAffiliateTrackingCode();
            $collection->setTrackingCode($tracking_code)->setGroupId('1');
        }

        $fullname = $customerData['firstname'].' '.$customerData['lastname'];
        $email = isset($customerData['email']) ? $customerData['email'] : $customer->getEmail();

        $bank_account_name = $bank_account_number = $bank_name = $swift_code = $bank_branch_city = $bank_branch_country_code = $intermediary_bank_code = $intermediary_bank_name = $intermediary_bank_city = $intermediary_bank_country_code = "";

        if(isset($customerData['bank_account_name'])){
            $bank_account_name = $customerData['bank_account_name'];
        }
        if(isset($customerData['bank_account_number'])){
            $bank_account_number = $customerData['bank_account_number'];
        }
        if(isset($customerData['bank_name'])){
            $bank_name = $customerData['bank_name'];
        }
        if(isset($customerData['swift_code'])){
            $swift_code = $customerData['swift_code'];
        }
        if(isset($customerData['bank_branch_city'])){
            $bank_branch_city = $customerData['bank_branch_city'];
        }
        if(isset($customerData['bank_branch_country_code'])){
            $bank_branch_country_code = $customerData['bank_branch_country_code'];
        }
        if(isset($customerData['intermediary_bank_code'])){
            $intermediary_bank_code = $customerData['intermediary_bank_code'];
        }
        if(isset($customerData['intermediary_bank_name'])){
            $intermediary_bank_name = $customerData['intermediary_bank_name'];
        }
        if(isset($customerData['intermediary_bank_city'])){
            $intermediary_bank_city = $customerData['intermediary_bank_city'];
        }
        if(isset($customerData['intermediary_bank_country_code'])){
            $intermediary_bank_country_code = $customerData['intermediary_bank_country_code'];
        }
        $collection->setPaypalEmail($customerData['paypal_email'])
                        // ->setSkrillEmail($customerData['skrill_email'])
                        ->setReferingWebsite($customerData['refering_website'])
                        ->setFirstname($customerData['firstname'])
                        ->setLastname($customerData['lastname'])
                        ->setFullname($fullname)
                        ->setEmail($email) 
                        ->setCustomerId($customer->getId())
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
                        ->setIsActive('1');                
        $collection->save();
        return $collection;
    }


    public function updateBalance($amount, $customer)
    {
        $customer_id = $customer->getId();
        $collection = $this->loadByAttribute('customer_id', $customer_id); 
        $balance_old = $collection->getBalance();        
        $commission_paid_old = $collection->getCommissionPaid();
        // if($command == 'withdraw_request'){
            $balance = $balance_old - $amount;
            $commission_paid = $commission_paid_old + $amount;

        // }elseif ($command == 'cancel_request') {
        //     $balance = $balance_old + $amount;
        //     $commission_paid = $commission_paid_old - $amount;
        // }
        $collection->setBalance($balance)
                    ->setCommissionPaid($commission_paid)
                    ->save();
        return $collection;
    }
    public function checkAccountExist($email){
        $select = $this->getCollection();
        $select->addFieldToFilter('email',$email);
        return (int)count($select);
    }

    public function checkAccExist($email)
    {
        $table_name = $this->_resourceModel->getTableName('customer_entity');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
            $table_name
            )->where(
            'email = ?',
            $email
            );
        $row = $connection->fetchRow($select);
        return $row;
    }

    public function getAllCustomers()
    {
        $table_name = $this->_resourceModel->getTableName('customer_entity');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from( $table_name )->where( 'is_active = 1' );
        $rows = $connection->fetchAll($select);

        $customers = array();
        foreach ($rows as $row) {
            $customers[] = array(
                'value' => $row['entity_id'],
                'label' => $row['email']
            );
        }
        return $customers;
    }
}
