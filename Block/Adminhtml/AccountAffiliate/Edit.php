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
namespace Lof\Affiliate\Block\Adminhtml\AccountAffiliate;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context
     * @param \Magento\Framework\Registry
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'accountaffiliate_id';
        $this->_blockGroup = 'Lof_Affiliate';
        $this->_controller = 'adminhtml_accountAffiliate';

        parent::_construct();

        if ($this->_isAllowedAction('Lof_Affiliate::account_save')) {
            $this->buttonList->update('save', 'label', __('Save Account'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Lof_Affiliate::account_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Account'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('affiliate_account')->getId()) {
            return __("Edit Account '%1'", $this->escapeHtml($this->_coreRegistry->registry('affiliate_account')->getTitle()));
        } else {
            return __('New Account Affiliate');
        }
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
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('cms/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {

        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $customers = $objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        // $customers = $customers->getAllCustomers();


        // $html = 'var source = [';
        //     $i=0; $count = count($customers);
        //     foreach ($customers as $customer) { $i++;
        //         $html .= '{ "label": "'.$customer['label'].'", "value": "'.$customer['label'].'" },';
        //         if ($i == $count) {
        //             $html .= '{ "label": "'.$customer['label'].'", "value": "'.$customer['label'].'" }';
        //         }
        //     }
        // $html .= '];';

        // require([ 'jquery', 'jquery/ui' ], function($){
        //         $( '#text-accountaffiliate_customer' ).autocomplete({
        //             source: source,
        //             minLength: 0
        //         });
        //     });

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";

        return parent::_prepareLayout();
    }
}
