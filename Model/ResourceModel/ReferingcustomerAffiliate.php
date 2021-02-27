<?php


namespace Lof\Affiliate\Model\ResourceModel;

class ReferingcustomerAffiliate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context
     * @param string|null
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_affiliate_referingcustomer', 'id');
    }
}
