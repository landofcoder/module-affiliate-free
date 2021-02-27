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
namespace Lof\Affiliate\Block\Adminhtml;

class AccountAffiliate extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_withdrawtaffiliate';
        $this->_blockGroup = 'Lof_Affiliate';
        $this->_headerText = __('Manage Withdrawls Affiliate');

        parent::_construct();

        // if ($this->_isAllowedAction('Lof_Affiliate::save')) {
        //     $this->buttonList->update('add', 'label', __('Add New Withdraw'));
        // } else {
        //     $this->buttonList->remove('add');
        // }
        // $this->buttonList->add(
        //     'apply_rules',
        //     [
        //         'label' => __('Apply Rules'),
        //         'onclick' => "location.href='" . $this->getUrl('catalog_rule/*/applyRules') . "'",
        //         'class' => 'add'
        //     ]
        // );
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}