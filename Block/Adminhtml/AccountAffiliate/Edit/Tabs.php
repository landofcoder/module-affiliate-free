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
namespace Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('accountaffiliate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Account Affiliate Information'));

        $this->addTab(
                'detail_section',
                [
                    'label' => __('Affiliate Detail'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Detail')->toHtml()
                ]
            );
         $this->addTab(
                'payment_section',
                [
                    'label' => __('Payment Details'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Payment')->toHtml()
                ]
            );

         $this->addTab(
                'transaction_section',
                [
                    'label' => __('History Transaction'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Transaction')->toHtml()
                ]
            );

         $this->addTab(
                'lead_section',
                [
                    'label' => __('History PAY PER LEAD Balance'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Lead')->toHtml()
                ]
            );
         $this->addTab(
                'withdraw_section',
                [
                    'label' => __('History Withdrawal'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Withdraw')->toHtml()
                ]
            );
    }
}
