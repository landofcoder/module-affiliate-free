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
namespace Lof\Affiliate\Block\Adminhtml\TransactionAffiliate\Edit\Tab;

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
    protected $_transactionCollection;

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
        \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\Collection $transactionCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_transactionCollection = $transactionCollection;
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
        $model = $this->_coreRegistry->registry('affiliate_transaction');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_Affiliate::transaction_edit') || $this->_isAllowedAction('Lof_Affiliate::transaction_new')) {
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

        $form->setHtmlIdPrefix('transactionaffiliate_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('transaction_id', 'hidden', ['name' => 'transaction_id']);
            $isElementDisabled = true;
        }

        $fieldset->addField(
            'affiliate_code',
            'label',
            [
                'name' => 'affiliate_code',
                'label' => __('Affiliate Code'),
                'title' => __('Affiliate Code'),
                'bold' => true,
                'disabled' => 'disabled'
            ]
        );
        $fieldset->addField(
            'increment_id',
            'link',
            [
                'name' => 'increment_id',
                'label' => __('Order Id'),
                'title' => __('Order Id'),
                'bold' => true,
                'href' => $this->getUrl('sales/order/view', ['order_id' => $model->getOrderId()]),
                'disabled' => 'disabled'
            ]
        );
        $fieldset->addField(
            'order_total',
            'label',
            [
                'name' => 'order_total',
                'label' => __('Order Total'),
                'title' => __('Order Total'),
                'bold' => true,
                'disabled' => 'disabled'
                // 'value' => $model->priceFormat($model->getOrderTotal())
            ]
        );
        $fieldset->addField(
            'commission_total',
            'label',
            [
                'name' => 'commission_total',
                'label' => __('Commision Total'),
                'title' => __('Commision Total'),
                'bold' => true,
                // 'required' => true,
                'disabled' => $isElementDisabled
                // 'value' => $model->priceFormat($model->getOrderTotal())
            ]
        );
        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'transaction_stt',
            'label',
            [
                'label'    => __('Transaction Status'),
                'title'    => __('Transaction Status'),
                'name'     => 'transaction_stt',
                // 'value'  => $model->getAvailableStatuses()
            ]
        );

        //  if (!$model->getId()) {
        //     $model->setData('transaction_stt', $isElementDisabled ? '0' : '1');
        // }

        $this->_eventManager->dispatch('adminhtml_affiliate_transactionaffiliate_edit_tab_detail_prepare_form', ['form' => $form]);
        // var_dump($model->getData());die($this->getUrl('sales/order/view', ['order_id' => $model->getOrderId()]));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }


    public function getAccountCollection(){
        $model = $this->_coreRegistry->registry('affiliate_transaction');
        $collection = $this->_transactionCollection
            ->addFieldToFilter('transactionaffiliate_id', array('neq' => $model->getId()));
            // ->setOrder('cat_position');
        return $collection;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Transaction Affiliate Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Transaction Affiliate Information');
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
