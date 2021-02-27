<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Model\ResourceModel;

class CampaignAffiliate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\Affiliate\Helper\Data $dataHelper,
        $connectionName = null
        ) {
        $this->_dataHelper = $dataHelper;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_affiliate_campaign', 'campaign_id');
    }

    /**
     * Process block data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Cms\Model\ResourceModel\Page
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['campaign_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lof_affiliate_campaign'), $condition);
        return parent::_beforeDelete($object);
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
            $table_name = $this->getTable('lof_affiliate_campaign');
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

    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if($stores = $object->getStores()){
            $table = $this->getTable('lof_affiliate_store');
            $where = ['campaign_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($stores) {
                $data = [];
                foreach ($stores as $storeId) {
                    $data[] = ['campaign_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
                }
                try{
                    $this->getConnection()->insertMultiple($table, $data);
                }catch(\Exception $e){
                    die($e->getMessage());
                }
            }
        }
        $postData = $object->getData();
        if(isset($postData['groups'])){
            $newGroups = (array)$postData['groups'];
            $table = $this->getTable('lof_affiliate_campaign_group');
            $where = ['campaign_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            $data = [];

            if (!empty($newGroups)) {
                foreach ($newGroups as $group) {
                    $data[] = [
                        'campaign_id' => (int)$object->getId(),
                        'group_id' => (int)$group,
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        } 

        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
            $groups = $this->lookupGroupIds($object->getId());
            $object->setData('group_id', $groups);
            $object->setData('groups', $groups);
        }
        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lof_affiliate_campaign'))
            ->where(
                'campaign_id = '.(int)$id
                );
            $posts = $connection->fetchAll($select);
            $object->setData('posts', $posts);
            $object->setData('commission', unserialize($object->getData('commission')));
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Block $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), \Magento\Store\Model\Store::DEFAULT_STORE_ID];

            $select->join(
                ['cbs' => $this->getTable('lof_affiliate_store')],
                $this->getMainTable() . '.campaign_id = cbs.campaign_id',
                ['store_id']
                )->where(
                'is_active = ?',
                1
                )->where(
                'cbs.store_id in (?)',
                $stores
                )->order(
                'store_id DESC'
                )->limit(
                1
            );
        }

        return $select;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->getConnection()->select()->from(
            ['cb' => $this->getMainTable()]
            )->join(
            ['cbs' => $this->getTable('lof_affiliate_store')],
            'cb.campaign_id = cbs.campaign_id',
            []
            )->where(
            'cb.identifier = ?',
            $object->getData('identifier')
            )->where(
            'cbs.store_id IN (?)',
            $stores
            );

            if ($object->getId()) {
                $select->where('cb.campaign_id <> ?', $object->getId());
            }

            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;
        }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_affiliate_store'),
            'store_id'
            )->where(
            'campaign_id = :campaign_id'
            );

            $binds = [':campaign_id' => (int)$id];

            return $connection->fetchCol($select, $binds);
        }



    public function lookupGroupIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_affiliate_campaign_group'),
            'group_id'
            )->where(
            'campaign_id = :campaign_id'
            );

            $binds = [':campaign_id' => (int)$id];

            return $connection->fetchCol($select, $binds);
        }
    }
;