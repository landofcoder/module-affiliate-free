<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Affiliate\Block\Adminhtml\WithdrawAffiliate\Request\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('withdrawaffiliate_request_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Withdrawal Request'));

        $this->addTab(
            'payment_section',
            [
                'label' => __('Withdrawal Information'),
                'content' => $this->getLayout()->createBlock('Lof\Affiliate\Block\Adminhtml\WithdrawAffiliate\Request\Edit\Tab\Detail')->toHtml()
            ]
        );
    }
}
