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

class Withdraw extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_withdraw;

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
        \Lof\Affiliate\Model\WithdrawAffiliate $withdraw,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_withdraw = $withdraw;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('withdraw_id');
        $this->setDefaultSort('withdraw_id', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $model = $this->_coreRegistry->registry('affiliate_account');
        $collection = $this->_withdraw->getCollection();
        $collection->addFieldToFilter('account_id', $model->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'withdraw_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'withdraw_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'withdraw_amount',
            [
                'header' => __('Withdraw Amount'),
                'sortable'         => true,
                'index' => 'withdraw_amount',
                'type' => 'currency',
                'currency' => 'base_currency_code'
            ]
        );
        $this->addColumn('date_request', ['header' => __('Date Request'), 'index' => 'date_request']);
        $this->addColumn('payment_method', ['header' => __('Payment Method'), 'index' => 'payment_method']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status']);
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return  $this->getUrl(
            'affiliate/*/withdraw/account_id/'.$this->_coreRegistry->registry('affiliate_account')->getId(),
            ['_current' => true]
        );
    }

    public function getRowUrl($row)
    {
        $model = $this->_coreRegistry->registry('affiliate_account');
        return $this->getUrl('affiliate/transactionaffiliate/edit', ['transaction_id' => $row->getId()]);
    }
}
