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
namespace Lof\Affiliate\Model\ResourceModel\AccountAffiliate;

// use \Lof\Affiliate\Model\ResourceModel\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'accountaffiliate_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    // protected function _afterLoad()
    // {
    //     $this->performAfterLoad('lof_affiliate_store', 'accountaffiliate_id');

    //     return parent::_afterLoad();
    // }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Affiliate\Model\AccountAffiliate', 'Lof\Affiliate\Model\ResourceModel\AccountAffiliate');
        // $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('accountaffiliate_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    // public function addStoreFilter($store, $withAdmin = true)
    // {
    //     if (!$this->getFlag('store_filter_added')) {
    //         $this->performAddStoreFilter($store, $withAdmin);
    //     }
    //     return $this;
    // }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    // protected function _renderFiltersBefore()
    // {
    //     $this->joinStoreRelationTable('lof_affiliate_store', 'accountaffiliate_id');
    // }
}
