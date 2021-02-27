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

class TransactionAffiliate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Blog's Statuses
     */
    const STATUS_COMPLETE = 1;
    const STATUS_PENDING = 0;
    const STATUS_CANCEL = 2;

    

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

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

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceHelper;

    protected $_resourceModel;
    /**
     * Page cache tag
     */
    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                            
     * @param \Ves\Blog\Model\ResourceModel\Blog|null                      $resource                 
     * @param \Ves\Blog\Model\ResourceModel\Blog\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory            
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Ves\Blog\Helper\Data                                    $brandHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\Collection $resourceCollection = null,
        \Magento\Framework\UrlInterface $url,
        \Lof\Affiliate\Helper\Data $accountlHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
         \Magento\Framework\App\ResourceConnection $resourceModel,
        array $data = []
        ) {
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->_accountlHelper = $accountlHelper;
        $this->_resourceModel = $resourceModel;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Affiliate\Model\ResourceModel\TransactionAffiliate');
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'transaction_id="' . $this->getId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that category content does not reference the block itself.')
            );
    }
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    public function priceFormat($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
    public function getCountOrderOfMonth($month){
        $table_name = $this->_resourceModel->getTableName('sales_order');
        $connection = $this->_resourceModel->getConnection();
        $select = $connection->select()->from(
            $table_name,
            'COUNT(*)'
            )->where(
            'MONTH(created_at) = ?',
            $month
            );
        return (int)$connection->fetchOne($select);
    }

    public function getCountMoneyOfMonth($month)
    {
        $table_name = $this->_resourceModel->getTableName('sales_order');
        $connection = $this->_resourceModel->getConnection();
        $select = $connection->select()->from(
            $table_name,
            'SUM(base_subtotal)'
            )->where(
            'MONTH(created_at) = ?',
            $month
            );
        return (int)$connection->fetchOne($select);
    }

    public function getCountMoneyAffOfMonth($month, $aff_email )
    {
        $table_name = $this->_resourceModel->getTableName('lof_affiliate_transaction');
        $connection = $this->_resourceModel->getConnection();
        $select = $connection->select()->from(
            $table_name,
            'SUM(order_total)'
            )->where(
            'MONTH(date_added) = ?',
            $month
            )->where(
            'email_aff = ?',
            $aff_email
            );
        return (int)$connection->fetchOne($select);
    }
    public function getCountOrderAffOfMonth($month, $aff_email)
    {
        $table_name = $this->_resourceModel->getTableName('lof_affiliate_transaction');
        $connection = $this->_resourceModel->getConnection();
        $select = $connection->select()->from(
            $table_name,
            'COUNT(*)'
            )->where(
            'MONTH(date_added) = ?',
            $month
            )->where(
            'email_aff = ?',
            $aff_email
            );
        return (int)$connection->fetchOne($select);
    }

    public function getAllSalesByDate($date_from, $date_to)
    {
        $table_name = $this->_resourceModel->getTableName('sales_order');
        $from = date( "Y-m-d", strtotime($date_from) );
        $to = date( "Y-m-d", strtotime($date_to) );
        $connection = $this->_resourceModel->getConnection();
        $select = $connection->select()->from(
            $table_name
            )->where(
            'created_at >= ?',
            $from
            )->where(
            'created_at <= ?',
            $to
            );
        return $connection->fetchAll($select);
    }

}
