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
namespace Lof\Affiliate\Block\Adminhtml\GroupAffiliate\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('groupaffiliate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Group Affiliate Information'));

        $this->addTab(
            'detail_section',
            [
                'label' => __('Group Detail'),
                'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\GroupAffiliate\Edit\Tab\Detail')->toHtml()
            ]
        );
        $this->addTab(
            'pps',
            [
                'label' => __('Pay Per Sale Detail'),
                'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\GroupAffiliate\Edit\Tab\Pps')->toHtml()
            ]
        ); 
        $this->addTab(
            'ppl',
            [
                'label' => __('Pay Per Lead Detail'),
                'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\GroupAffiliate\Edit\Tab\Ppl')->toHtml()
            ]
        );  
    }
}
