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
namespace Lof\Affiliate\Block\Adminhtml\CampaignAffiliate\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('campaignaffiliate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Campaign Affiliate Information'));

        $this->addTab(
                'detail_section',
                [
                    'label' => __('Campaign Information'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\CampaignAffiliate\Edit\Tab\Detail')->toHtml()
                ]
            );
        $this->addTab(
                'discounts_section',
                [
                    'label' => __('Base Commission'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\CampaignAffiliate\Edit\Tab\Discounts')->toHtml()
                ]
            );
        $this->addTab(
                'ppl_section',
                [
                    'label' => __('Pay Per Lead'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\CampaignAffiliate\Edit\Tab\Ppl')->toHtml()
                ]
            );
        $this->addTab(
                'conditions_section',
                [
                    'label' => __('Conditions Info'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\CampaignAffiliate\Edit\Tab\Conditions')->toHtml()
                ]
            );
        $this->addTab(
                'commissions_section',
                [
                    'label' => __('Advantage Commission'),
                    'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\CampaignAffiliate\Edit\Tab\Commissions')->toHtml()
                ]
            );
    }
}
