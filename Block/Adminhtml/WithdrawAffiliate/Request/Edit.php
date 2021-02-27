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
namespace Lof\Affiliate\Block\Adminhtml\WithdrawAffiliate\Request;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\Affiliate\Model\WithdrawAffiliate
     */
    protected $_withdrawModel;

    /**
     * @param \Magento\Backend\Block\Widget\Context
     * @param \Magento\Framework\Registry
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\WithdrawAffiliate $withdrawModel,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_withdrawModel = $withdrawModel;
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'withdraw_id';
        $this->_blockGroup = 'Lof_Affiliate';
        $this->_controller = 'adminhtml_withdrawAffiliate';

        parent::_construct();
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        // if ($this->_coreRegistry->registry('affiliate_withdraw')->getId()) {
            return __("Edit Account '%1'", $this->escapeHtml($this->_coreRegistry->registry('affiliate_account')->getTitle()));
        // } 
        // else {
        //     return __('New Payment Affiliate');
        // }
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

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $withdraw_id = $this->_coreRegistry->registry('affiliate_withdraw')->getId();
        $offlinepayUrl = $this->getUrl('*/*/offlinepay',["withdraw_id" => $withdraw_id,"type" => 'offlinepay']);
        $saverequestUrl = $this->getUrl('*/*/offlinepay',["withdraw_id" => $withdraw_id,"update_request" => 1]);
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
            require([
                'jquery',
                'mage/backend/form'
                ], function(){
                    jQuery('#offline_pay').click(function(){
                        var actionUrl = '".$offlinepayUrl."';
                        jQuery('#edit_form').attr('action', actionUrl);
                        jQuery('#edit_form').submit();
                    });
                    jQuery('#save_request').click(function(){
                        var actionUrl = '".$saverequestUrl."';
                        jQuery('#edit_form').attr('action', actionUrl);
                        jQuery('#edit_form').submit();
                    });
            });
        ";
        
        $model = $this->_withdrawModel->load($withdraw_id)->getData();
        $status = $model['status'];
        
        if ($withdraw_id) {
            if($model['payment_method'] == "banktransfer") {
                if($status == 0){
                    $addButtonProps = [
                        'id' => 'offline_pay',
                        'label' => __('Offline Pay'),
                        'class' => 'add',
                        'button_class' => ''
                    ];
                    $this->buttonList->add('offline_pay', $addButtonProps);
                }
                $updateButtonProps = [
                    'id' => 'save_request',
                    'label' => __('Update'),
                    'class' => 'add',
                    'button_class' => ''
                ];
                $this->buttonList->add('save_request', $updateButtonProps);
            } elseif($status == 0) {
                $addButtonProps = [
                    'id' => 'pay_request',
                    'label' => __('Pay Request'),
                    'class' => 'add',
                    'button_class' => '',
                    'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                    'options' => $this->_getAddProductButtonOptions(),
                ];
                $this->buttonList->add('add_new', $addButtonProps);
            }
        }

        return parent::_prepareLayout();
    }

    protected function _getAddProductButtonOptions()
    {
        $withdraw_id = $this->_coreRegistry->registry('affiliate_withdraw')->getId();
        $payUrl = $this->getUrl('*/*/payrequest',["withdraw_id" => $withdraw_id,"type" => 'paypal']);
        $skrillUrl = $this->getUrl('*/*/payrequest',["withdraw_id" => $withdraw_id,"type" => 'skrill']);
        $banktransferUrl = $this->getUrl('*/*/payrequest',["withdraw_id" => $withdraw_id,"type" => 'banktransfer']);
        // $brainTreeUrl = $this->getUrl('*/*/payrequest',["withdraw_id" => $withdraw_id,"type" => 'braintree']);
        $splitButtonOptions = [];
        $splitButtonOptions[0] = [
            'label' => __('Paypal'),
            'onclick' => "setLocation('" . $payUrl . "')",
            'default' => true,
            'id' => 'paypal',
            'data-fancybox-type'=> "iframe",
        ];

        // $splitButtonOptions[1] = [
        //     'label' => __('Bank Transfer'),
        //     'onclick' => "setLocation('" . $banktransferUrl . "')",
        //     'id' => 'banktransfer',
        //     'data-fancybox-type'=> "iframe",
        // ];
        // $splitButtonOptions[1] = [
        //     'label' => __('Skrill'),
        //     'onclick' => "setLocation('" . $skrillUrl . "')",
        //     'id' => 'skrill',
        //     'data-fancybox-type'=> "iframe",
        // ];
        /* Disabled Braintree Method

        $splitButtonOptions[2] = [
            'label' => __('BrainTree'),
            'onclick' => "setLocation('" . $brainTreeUrl . "')",
            'id' => 'braintree',
            'data-fancybox-type'=> "iframe",
        ];
        */

        return $splitButtonOptions;
    }
}
