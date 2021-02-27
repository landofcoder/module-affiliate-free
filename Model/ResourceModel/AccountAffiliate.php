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
namespace Lof\Affiliate\Model\ResourceModel;

class AccountAffiliate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_resource;

    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Lof\Affiliate\Helper\Data $dataHelper,
        $connectionName = null
        ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_affiliate_account', 'accountaffiliate_id');
    }

    /**
     * Perform operations before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        for($i=1;$i<=10;$i++) {
            if($this->checkTrackingCodeExists($object->getTrackingCode())){
                $tracking_code = $this->_dataHelper->getAffiliateTrackingCode();
                $object->setTrackingCode($tracking_code);
            } else {
                break;
            }
        }
        return $this;
    }
    
    public function checkTrackingCodeExists($tracking_code = ''){
        if($tracking_code) {
            $table_name = $this->getTable('lof_affiliate_account');
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $table_name
                )->where(
                'tracking_code = :tracking_code'
                );

            $binds = [':tracking_code' => $tracking_code];
            $resultData = $connection->fetchRow($select, $binds);
            if($resultData) {
                return true;
            }
        }
        return false;
    }

    public function checkAccountExist($email){
        $table_name = $this->getTable('customer_entity');
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $table_name
            )->where(
            'email = :email'
            );

        $binds = [':email' => $email];
        $collection = $connection->fetchCol($select, $binds);
        // die()
        return $collection;
    }
};