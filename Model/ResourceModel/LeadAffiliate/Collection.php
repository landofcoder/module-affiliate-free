<?php


namespace Lof\Affiliate\Model\ResourceModel\LeadAffiliate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Lof\Affiliate\Model\LeadAffiliate', 'Lof\Affiliate\Model\ResourceModel\LeadAffiliate');
    }
}
