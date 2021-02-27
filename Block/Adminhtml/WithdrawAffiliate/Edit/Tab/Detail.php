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
namespace Lof\Affiliate\Block\Adminhtml\WithdrawAffiliate\Edit\Tab;

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

    protected $_dataHelper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\Collection $accountCollection,
        \Lof\Affiliate\Helper\Data $dataHelper,
        array $data = []
    ) {
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
        if ($this->_isAllowedAction('Lof_Affiliate::withdraw_edit') || $this->_isAllowedAction('Lof_Affiliate::withdraw_new')) {
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Withdrawal Information')]);
        $fieldset1 = $form->addFieldset('second_fieldset', ['legend' => __('Payment Method Information')]);

        if ($model->getId()) {
            $fieldset->addField('accountaffiliate_id', 'hidden', ['name' => 'accountaffiliate_id']);
        }
        $tracking_code = $this->_dataHelper->getAffiliateTrackingCode();
        $fieldset->addField(
            'email',
            'link',
            [
                'name' => 'email',
                'label' => __('Email Address'),
                'title' => __('EMail Address'),
                'href' => $this->getUrl('affiliate/AccountAffiliate/edit', ['id' => $model->getId()]),
                // 'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

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
        
        // $fieldset->addField(
        //     'refering_website',
        //     'text',
        //     [
        //         'name' => 'refering_website',
        //         'label' => __('Refering Website'),
        //         'title' => __('Refering Website'),
        //         'bold' => true,
        //         // 'required' => true,
        //         'disabled' => $isElementDisabled
        //     ]
        // );
        $fieldset->addField(
            'balance',
            'label',
            [
                'name' => 'balance',
                'label' => __('Balance'),
                'title' => __('Balance'),
                'bold' => true,
                // 'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'withdraw_amount',
            'text',
            [
                'name' => 'withdraw_amount',
                'label' => __('Withdraw Amount'),
                'title' => __('Withdraw Amount'),
                'bold' => true,
                // 'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset1->addField(
            'paypal_email',
            'text',
            [
                'name' => 'paypal_email',
                'label' => __('Paypal Email'),
                'title' => __('Paypal Email'),
                'bold' => true,
                // 'required' => true,
                'disabled' => true
            ]
        );
        $fieldset1->addField(
            'tracking_code',
            'text',
            [
                'name' => 'tracking_code',
                'label' => __('Tracking Code'),
                'title' => __('Tracking Code'),
                'bold' => true,
                // 'required' => true,
                'disabled' => true
            ]
        );

        if (!$model->getId()) {
            $model->setData('tracking_code', $tracking_code);
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
