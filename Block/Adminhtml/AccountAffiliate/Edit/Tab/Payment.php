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

class Payment extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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

    protected $_priceHelper;


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
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\Collection $accountCollection,
        \Lof\Affiliate\Helper\Price $price,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_accountCollection = $accountCollection;
        $this->_objectManager= $objectManager;
        $this->_priceHelper = $price;
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Payment Details')]);

        if ($model->getId()) {
            $fieldset->addField('accountaffiliate_id', 'hidden', ['name' => 'accountaffiliate_id']);
            $account = $model->getData();
            $groupModel = $this->_objectManager->create('Lof\Affiliate\Model\GroupAffiliate');
            $group = $groupModel->load($account['group_id']);
            $commission = $group->getCommission();

            $fieldset->addField(
                'commission',
                'note',
                [
                    'name' => 'commission',
                    'label' => __('Commission (%)'),
                    'title' => __('Commission'),
                    'disabled' => true,
                    'text' => $commission
                ]
            );        
        }

        $fieldset->addField(
            'paypal_email',
            'text',
            [
                'name' => 'paypal_email',
                'label' => __('Paypal Email'),
                'title' => __('Paypal Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'skrill_email',
            'text',
            [
                'name' => 'skrill_email',
                'label' => __('Skrill Email'),
                'title' => __('skrill Email'),
                'disabled' => $isElementDisabled
            ]
        );
        if ($model->getId()) {
            $fieldset->addField(
                'balance',
                'note',
                [
                    'name' => 'balance',
                    'label' => __('Balance'),
                    'title' => __('Balance'),
                    'bold' => true,
                    'text' =>  $this->_priceHelper->formatPrice($model->getBalance()),
                    // 'required' => true,
                    'disabled' => $isElementDisabled
                ]
            );
            $fieldset->addField(
                'commission_paid',
                'note',
                [
                    'name' => 'commission_paid',
                    'label' => __('Commission Paid'),
                    'title' => __('Commission Paid'),
                    'bold' => true,
                    'text' => $this->_priceHelper->formatPrice($model->getCommissionPaid()),
                    // 'required' => true,
                    'disabled' => $isElementDisabled
                ]
            );
        }
        $this->_eventManager->dispatch('adminhtml_affiliate_accountaffiliate_edit_tab_payment_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getAccountCollection(){
        $model = $this->_coreRegistry->registry('affiliate_account');
        $collection = $this->_accountCollection
            ->addFieldToFilter('accountaffiliate_id', array('neq' => $model->getId()));
            // ->setOrder('cat_position');
        return $collection;
    }

    public function getAccounts($accounts, $acc = [], $level = 0){
        foreach ($acc as $k => $v) {

        }
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
