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

namespace Lof\Affiliate\Block\Adminhtml\GroupAffiliate\Edit\Tab;

class Ppl extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Lof\Affiliate\Model\ResourceModel\Affiliate\Collection
     */
    protected $_groupCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Affiliate\Model\ResourceModel\GroupAffiliate\Collection $groupCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_groupCollection = $groupCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('affiliate_group');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_Affiliate::group_edit') || $this->_isAllowedAction('Lof_Affiliate::group_new')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $this->_eventManager->dispatch(
            'lof_check_license',
            ['obj' => $this,'ex'=>'Lof_Affiliate']
            );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('groupaffiliate_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Pay Per Lead Detail')]);

        if ($model->getId()) {
            $fieldset->addField('group_id', 'hidden', ['name' => 'group_id']);
            // $isElementDisabled = true;
        }

        $fieldset->addField(
            'commission_ppl_action',
            'select',
            [
                'name' => 'commission_ppl_action',
                'label' => __('Apply'),
                'title' => __('Apply'),
                'options' => $model->getCommissionAction(),
                'note' => __('Condition: Customer (who sign up new account under referal link) have 1 completed order.'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'commission_ppl',
            'text',
            [
                'name' => 'commission_ppl',
                'label' => __('Commission For Each Completed Order'),
                'title' => __('PPL Commission'),
                'required' => true,
                'class' => 'validate-number',
                'note' => __('Set value is 0 if you want to disable PAY PER LEAD.'),
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_affiliate_groupaffiliate_edit_tab_detail_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Group Affiliate Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Group Affiliate Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
