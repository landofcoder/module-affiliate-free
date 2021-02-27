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
class Lead extends \Magento\Framework\View\Element\Template
{
    /**
    * @var Lof\Affiliate\Model\ResourceModel\LeadAffiliate\Collection
    */
    protected $_leadCollectionFactory; 

    /**
    * @var Lof\Affiliate\Model\ResourceModel\AccountAffiliate\CollectionFactory
    */
    protected $_accountCollectionFactory; 

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

    protected $_collection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Model\ResourceModel\LeadAffiliate\Collection $leadCollectionFactory,
        \Lof\Affiliate\Model\AccountAffiliateFactory $accountCollectionFactory,
        Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\Affiliate\Helper\Data $helper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
        ) {
        $this->_leadCollectionFactory = $leadCollectionFactory;
        $this->_accountCollectionFactory = $accountCollectionFactory;
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
        $collection = $this->getLeadCollection();
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

    public function getLeadCollection()
    {
        if(!$this->_collection){

            $accountCollection = $this->_accountCollectionFactory->create();
            $accountCollection->load($this->getCustomer()->getId(), 'customer_id');

            $limit = $this->getLimit();
            if(!$limit) {
                $limit = 5;
            }
            $collection = $this->_leadCollectionFactory
                ->addFieldToFilter('account_id', $accountCollection->getId());
            $collection->setPageSize($limit)->setOrder('lead_id', 'desc');
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
        $this->pageConfig->getTitle()->set(__('Lead Details'));
        $this->_addBreadcrumbs();
        return parent::_prepareLayout();
    }

    protected function _addBreadcrumbs()
    {

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = 'Lead Details';
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
