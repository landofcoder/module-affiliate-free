<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\Affiliate\Block\Account\Form;
/**
 * Customer edit form block
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */

use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
class Transaction extends \Magento\Framework\View\Element\Template
{
    /**
    * @var Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\Collection
    */
    protected $_transactionCollectionFactory; 

    /**
     * @var Session
     */
    protected $session;
    /**
     * \Magento\Framework\App\ResourceConnection
     * @var [type]
     */
    protected $_resource;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\CollectionFactory
     */
    protected $_collection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\CollectionFactory $transactionCollectionFactory,
        Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\Affiliate\Helper\Data $helper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
        ) {
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->session = $customerSession;
        $this->_resource = $resource;
        $this->priceCurrency = $priceCurrency;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getHelper() {
        return $this->_helper;
    }

    public function setCollection($collection){
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection(){
        return $this->_collection;

    }

    public function getCustomer()
    {
        $customer = $this->session->getCustomer();
        return $customer;
    }

    protected function _beforeToHtml()
    {
        $toolbar    = $this->getLayout()->getBlock('lof_toolbar');
        $collection = $this->getTransactionCollection();
        $limit      = $this->getLimit();
        if(!$limit) {
            $limit = 5;
        }
        if ($toolbar) {
            $toolbar->setData('_current_limit', $limit)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return $this;
    }

    public function getLimitPage()
    {
        $size = $this->getCollection()->getSize();
        $limit = ceil($size/$this->getLimit());
        return $limit;
    }

    public function getCurrentPage()
    {
        $p = (int) $this->getRequest()->getParam('p');
        $limit = (int) $this->getLimitPage();
        if ($p > $limit) {
            $p = $limit;
        }
        if (!$p) {
            $p = 1;
        }
        return $p;
    }

    public function getTransactionCollection()
    {
        if(!$this->_collection){
            $limit = $this->getLimit();
            if(!$limit) {
                $limit = 5;
            }
            $collection = $this->_transactionCollectionFactory->create()
                ->addFieldToFilter('email_aff', $this->getCustomer()->getEmail());
            $collection->setPageSize($limit)->setOrder('transaction_id', 'desc');
            $this->setCollection($collection);
        }
        return $this->getCollection();
    }

    public function formatCurrency(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->format($amount, $includeContainer, $precision);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Transaction'));
        $this->_addBreadcrumbs();
        return parent::_prepareLayout();
    }

    protected function _addBreadcrumbs()
    {

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = 'Transaction';
        $show_breadcrumbs = true;

        if($show_breadcrumbs && $breadcrumbsBlock){
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
             );
            $breadcrumbsBlock->addCrumb(
                'list',
                [
                    'label' => __('Affiliate'),
                    'title' => __('Return to Affiliate'),
                    'link' => $baseUrl.'affiliate'
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'view',
                [
                    'label' => $page_title,
                    'title' => $page_title,
                    'link' => ''
                ]
             );
        }
    }

}
