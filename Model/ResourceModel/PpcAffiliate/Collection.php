<?php


namespace Lof\Affiliate\Model\ResourceModel\PpcAffiliate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Lof\Affiliate\Model\PpcAffiliate', 'Lof\Affiliate\Model\ResourceModel\PpcAffiliate');
    }
}
