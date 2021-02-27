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

class Lead extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_lead;

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
        \Lof\Affiliate\Model\LeadAffiliate $lead,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_lead = $lead;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('lead_id');
        $this->setDefaultSort('lead_id', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $model = $this->_coreRegistry->registry('affiliate_account');
        $collection = $this->_lead->getCollection();
        $collection->addFieldToFilter('account_id', $model->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'lead_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'lead_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'customer_email', 
            [
                'header' => __('Customer Email'), 
                'width' => '100', 
                'index' => 'customer_email'
                ]
        );
        $this->addColumn('customer_ip', ['header' => __('Customer Ip'), 'index' => 'customer_ip']);
        $this->addColumn(
            'signup_commission',
            [
                'header' => __('Signup Commission'),
                'index' => 'signup_commission',
                'type' => 'currency',
                'currency' => 'base_currency_code'
            ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return  $this->getUrl(
            'affiliate/*/lead/account_id/'.$this->_coreRegistry->registry('affiliate_account')->getId(),
            ['_current' => true]
        );
    }
}
