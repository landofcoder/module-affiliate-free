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

namespace Lof\Affiliate\Block\Adminhtml\WithdrawAffiliate\Request\Edit\Tab;

use Magento\Framework\UrlInterface;

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
    protected $_withdrawtCollection;

    protected $_dataHelper;
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Affiliate\Model\ResourceModel\WithdrawAffiliate\Collection $withdrawCollection,
        \Lof\Affiliate\Helper\Data $dataHelper,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_withdrawCollection = $withdrawCollection;
        $this->_dataHelper = $dataHelper;
        $this->_urlBuilder = $urlBuilder;
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
        $model = $this->_coreRegistry->registry('affiliate_withdraw');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_Affiliate::withdraw')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $payment_method = "";
        if ($model->getId()) {
            $payment_method = $model->getPaymentMethod();
        } else {
            $model->setData('tracking_code', $tracking_code);
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('withdrawaffiliate_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Withdrawal Information')]);

        if ($model->getId()) {
            $fieldset->addField('withdraw_id', 'hidden', ['name' => 'withdraw_id']);
        }

        $tracking_code = $this->_dataHelper->getAffiliateTrackingCode();

        $fieldset->addField(
            'affiliate_email',
            'label',
            [
                'name' => 'affiliate_email',
                'label' => __('Email Affiliate'),
                'title' => __('EMail Affiliate'),
                'disabled' => $isElementDisabled
            ]
        );

        if ($payment_method == "paypal") {
            $fieldset->addField(
                'paypal_email',
                'label',
                [
                    'name' => 'paypal_email',
                    'label' => __('Paypal Email'),
                    'title' => __('Paypal Email'),
                    'bold' => true,
                    'disabled' => $isElementDisabled
                ]
            );
        }
        $fieldset->addField(
            'withdraw_amount',
            'label',
            [
                'name' => 'withdraw_amount',
                'label' => __('Withdraw Amount'),
                'title' => __('Withdraw Amount'),
                'bold' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'payment_method',
            'label',
            [
                'name' => 'payment_method',
                'label' => __('Payment Method'),
                'title' => __('Payment Method'),
                'disabled' => true
            ]
        );
        $fieldset->addField(
            'date_request',
            'label',
            [
                'name' => 'date_request',
                'label' => __('Date Request'),
                'title' => __('date_request'),
                'disabled' => true
            ]
        );

        $payment_status = __('Pending');
        $status = $model->getStatus();
        if ($status == 1) {
            $payment_status = __('Completed');
        }
        $fieldset->addField(
            'payment',
            'note',
            [
                'name' => 'payment',
                'label' => __('Status'),
                'title' => __('Status'),
                'text' => __($payment_status),
                'disabled' => true
            ]
        );

        if ($payment_method == "banktransfer") {
            $bank_data = $model->getBanktransferData();
            $bank_data_array = unserialize($bank_data);
            if ($bank_data_array) {
                foreach ($bank_data_array as $key => $value) {
                    $model->setData($key, $value);
                }
                $fieldset->addField(
                    'bank_account_name',
                    'text',
                    [
                        'name' => 'bank_account_name',
                        'label' => __("Bank Account Holder's Name"),
                        'title' => __("Bank Account Holder's Name"),
                        'required' => true,
                        'note' => __("Your full name that appears on your bank account statement")
                    ]
                );
                $fieldset->addField(
                    'bank_account_number',
                    'text',
                    [
                        'name' => 'bank_account_number',
                        'label' => __("Bank Account Number/IBAN"),
                        'title' => __("Bank Account Number/IBAN"),
                        'required' => true,
                        'note' => __("Up to 34 letters and numbers. Australian account numbers should include the BSB number.")
                    ]
                );
                $fieldset->addField(
                    'swift_code',
                    'text',
                    [
                        'name' => 'swift_code',
                        'label' => __("SWIFT Code"),
                        'title' => __("SWIFT Code"),
                        'required' => true,
                        'note' => __("either 8 or 11 characters e.g. ABNAUS33 or 1234567891")
                    ]
                );
                $fieldset->addField(
                    'bank_name',
                    'text',
                    [
                        'name' => 'bank_name',
                        'label' => __("Bank Name in Full"),
                        'title' => __("Bank Name in Full"),
                        'required' => true,
                        'note' => __("Up to 30 letters, numbers or spaces.")
                    ]
                );
                $fieldset->addField(
                    'bank_branch_city',
                    'text',
                    [
                        'name' => 'bank_branch_city',
                        'label' => __("Bank Branch City"),
                        'title' => __("Bank Branch City"),
                        'required' => true,
                        'note' => __("Up to 12 letters, numbers or spaces.")
                    ]
                );
                $fieldset->addField(
                    'bank_branch_country_code',
                    'text',
                    [
                        'name' => 'bank_branch_country_code',
                        'label' => __("Bank Branch Country"),
                        'title' => __("Bank Branch Country"),
                        'required' => true,
                        'note' => __("Country code")
                    ]
                );
                $fieldset->addField(
                    'intermediary_bank_code',
                    'text',
                    [
                        'name' => 'intermediary_bank_code',
                        'label' => __("Intermediary Bank - Bank Code"),
                        'title' => __("Intermediary Bank - Bank Code"),
                        'required' => false,
                        'note' => __("either 8 or 11 characters e.g. ABNAUS33 or 1234567891")
                    ]
                );
                $fieldset->addField(
                    'intermediary_bank_name',
                    'text',
                    [
                        'name' => 'intermediary_bank_name',
                        'label' => __("Intermediary Bank - Name"),
                        'title' => __("Intermediary Bank - Name"),
                        'required' => false,
                        'note' => __("e.g. Citibank")
                    ]
                );
                $fieldset->addField(
                    'intermediary_bank_city',
                    'text',
                    [
                        'name' => 'intermediary_bank_city',
                        'label' => __("Intermediary Bank - City"),
                        'title' => __("Intermediary Bank - City"),
                        'required' => false,
                        'note' => __("Up to 12 letters, numbers or spaces.")
                    ]
                );
                $fieldset->addField(
                    'intermediary_bank_country_code',
                    'text',
                    [
                        'name' => 'intermediary_bank_country_code',
                        'label' => __("Intermediary Bank - Country"),
                        'title' => __("Intermediary Bank - Country"),
                        'required' => false,
                        'note' => __("Bank Country Code")
                    ]
                );

                $fieldset->addField(
                    'transaction_data',
                    'textarea',
                    [
                        'name' => 'transaction_data',
                        'label' => __("Offline Transaction Note"),
                        'title' => __("Offline Transaction Note"),
                        'required' => false,
                        'note' => __("Input the transaction id, payed date time,...")
                    ]
                );

                $fieldset->addField(
                    'attachment',
                    'image',
                    [
                        'name' => 'attachment',
                        'label' => __('Attachments'),
                        'required' => false,
                        'note' => __("Attach document file. File type: JPG, JPEG, PNG, GIF")
                    ]
                );
            }
        }


        $this->_eventManager->dispatch('adminhtml_affiliate_accountaffiliate_edit_tab_detail_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getWithdrawCollection()
    {
        $model = $this->_coreRegistry->registry('affiliate_withdraw');
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
        return __('Withdrawal Affiliate Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Withdrawal Affiliate Information');
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
