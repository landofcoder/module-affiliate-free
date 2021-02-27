<?php


namespace Lof\Affiliate\Model\ResourceModel\ReferingcustomerAffiliate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Lof\Affiliate\Model\ReferingcustomerAffiliate', 'Lof\Affiliate\Model\ResourceModel\ReferingcustomerAffiliate');
    }
}
