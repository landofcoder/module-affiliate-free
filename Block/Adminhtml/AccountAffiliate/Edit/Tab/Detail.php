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

class Detail extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
    protected $_accountCollection;

    protected $_objectManager;

    protected $_dataHelper;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\Collection $accountCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\Affiliate\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_objectManager= $objectManager;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_accountCollection = $accountCollection;
        $this->_dataHelper = $dataHelper;
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
        $model = $this->_coreRegistry->registry('affiliate_account');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_Affiliate::account_edit') || $this->_isAllowedAction('Lof_Affiliate::account_new')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $this->_eventManager->dispatch(
        'lof_check_license',
        ['obj' => $this,'ex'=>'Lof_Affiliate']
        );
        if(!$this->getData('is_valid') && !$this->getData('local_valid')){
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('accountaffiliate_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('accountaffiliate_id', 'hidden', ['name' => 'accountaffiliate_id']);
        }
        $tracking_code = $this->_dataHelper->getAffiliateTrackingCode();
        $fieldset->addField(
            'group_id',
            'select',
            [
                'name' => 'group_id',
                'label' => __('Group Account'),
                'title' => __('Group Account'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $model->getGroupType(),
            ]
        );
        /*
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        */

        $fieldset->addField(
            'firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'bold' => true,
                // 'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'bold' => true,
                // 'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        if ($model->getId()) {

            $fieldset->addField(
                'fullname',
                'link',
                [
                    'name' => 'fullname',
                    'label' => __('Customer Account'),
                    'title' => __('Customer Acount'),
                    // 'bold' => true,
                    'href' => $this->getUrl('customer/index/edit', ['id' => $model->getCustomerid()]),
                    // 'required' => true,
                    'disabled' => $isElementDisabled
                ]
            );
            $fieldset->addField(
                'email',
                'label',
                [
                    'name' => 'email',
                    'label' => __('Email Address'),
                    'title' => __('EMail Address'),
                    // 'required' => true,
                    'disabled' => $isElementDisabled
                ]
            );
            
            $fieldset->addField(
                'tracking_code',
                'note',
                [
                    'name' => 'tracking_code',
                    'label' => __('Tracking Code'),
                    'title' => __('Tracking Code'),
                    'bold' => true,
                    'text' => $model->getTrackingCode(),
                    'disabled' => $isElementDisabled
                ]
            );

        } else {
            $fieldset->addType('customer','Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Form\Customer');
            $fieldset->addField(
                'customer',
                'customer',
                [
                    'name' => 'customer',
                    'label' => __('Choose Customer'),
                    'value' => array(),
                    'required' => false,
                    'disabled' => $isElementDisabled,
                    'note' => __('AutoComplete Customer Email that you want choose.')
                ]
            );
            $fieldset->addField(
                'email',
                'text',
                [
                    'name' => 'email',
                    'label' => __('Email Address'),
                    'title' => __('EMail Address'),
                    'readonly' => true,
                    'disabled' => $isElementDisabled
                ]
            );
            
            $fieldset->addField(
                'tracking_code',
                'text',
                [
                    'name' => 'tracking_code',
                    'label' => __('Tracking Code'),
                    'title' => __('Tracking Code'),
                    'bold' => true,
                    'value' => $tracking_code,
                    'text' => $model->getTrackingCode(),
                    'disabled' => $isElementDisabled,
                    'readonly' => true,
                    'description' => 'Tracking code is random code.'
                ]
            );
        }

        $fieldset->addField(
            'refering_website',
            'text',
            [
                'name' => 'refering_website',
                'label' => __('Refering Website'),
                'title' => __('Refering Website'),
                'bold' => true,
                // 'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        

        if (!$model->getId()) {
            $model->setData('tracking_code',$tracking_code);
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $this->_eventManager->dispatch('adminhtml_affiliate_accountaffiliate_edit_tab_detail_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getAccountCollection(){
        $model = $this->_coreRegistry->registry('affiliate_account');
        $collection = $this->_accountCollection
            ->addFieldToFilter('accountaffiliate_id', array('neq' => $model->getId()));
        return $collection;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Account Affiliate Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Account Affiliate Information');
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
