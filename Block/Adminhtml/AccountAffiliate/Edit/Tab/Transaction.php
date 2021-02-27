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
namespace Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab;

class Transaction extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_transaction;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Lof\Affiliate\Model\TransactionAffiliate $transaction,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_transaction = $transaction;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('transaction_id');
        $this->setDefaultSort('transaction_id', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $model = $this->_coreRegistry->registry('affiliate_account');
        $collection = $this->_transaction->getCollection();
        $collection->addFieldToFilter('account_id', $model->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', ['header' => __('Id'), 'width' => '100', 'index' => 'transaction_id']);
        $this->addColumn('increment_id', ['header' => __('Order'), 'width' => '100', 'index' => 'increment_id']);
        $this->addColumn(
            'customer_email', 
            [
                'header' => __('Customer Email'), 
                'width' => '100', 
                'index' => 'customer_email'
                ]
        );
        $this->addColumn(
            'order_total',
            [
                'header' => __('Order Total'),
                'index' => 'order_total',
                'type' => 'currency',
                'currency' => 'base_currency_code'
            ]
        );
        $this->addColumn(
            'commission_total',
            [
                'header' => __('Commission Total'),
                'index' => 'commission_total',
                'type' => 'currency',
                'currency' => 'base_currency_code'
            ]
        );
        $this->addColumn('order_status', ['header' => __('Order Status'), 'index' => 'order_status']);
        $this->addColumn('transaction_stt', ['header' => __('Transaction Status'), 'index' => 'transaction_stt']);
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return  $this->getUrl(
            'affiliate/*/transaction/account_id/'.$this->_coreRegistry->registry('affiliate_account')->getId(),
            ['_current' => true]
        );
    }

    public function getRowUrl($row)
    {
        $model = $this->_coreRegistry->registry('affiliate_account');
        return $this->getUrl('affiliate/transactionaffiliate/edit', ['transaction_id' => $row->getId()]);
    }
}
