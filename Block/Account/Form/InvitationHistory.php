<?php

namespace Lof\Affiliate\Block\Account\Form;

use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Lof\Affiliate\Model\ReferingcustomerAffiliateFactory;

class InvitationHistory extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $_collection;

    protected $referCustomer;

    protected $_objectManager;

    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        \Lof\Affiliate\Helper\Data $helper,
        PriceCurrencyInterface $priceCurrency,
        ReferingcustomerAffiliateFactory $referCustomer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->priceCurrency = $priceCurrency;
        $this->_helper = $helper;
        $this->referCustomer = $referCustomer;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    public function getCustomer()
    {
        $customer = $this->session->getCustomer();
        return $customer;
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getLayout()->getBlock('lof_toolbar');
        $collection = $this->getReferingCustomerCollection();
        $limit = $this->getLimit();
        if (!$limit) {
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
        $limit = ceil($size / $this->getLimit());
        return $limit;
    }

    public function getCurrentPage()
    {
        $p = (int)$this->getRequest()->getParam('p');
        $limit = (int)$this->getLimitPage();
        if ($p > $limit) {
            $p = $limit;
        }
        if (!$p) {
            $p = 1;
        }
        return $p;
    }

    public function getReferingCustomerCollection()
    {
        if (!$this->_collection) {
            $limit = $this->getLimit();
            if (!$limit) {
                $limit = 5;
            }
            $customerEmail = $this->getCustomer()->getEmail();
            $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
            $accountAffiliate->loadByAttribute('email', $customerEmail);

            $referCode = $accountAffiliate->getTrackingCode();
            $collection = $this->referCustomer->create()->getCollection()
                ->addFieldToFilter('affiliate_code', $referCode);
            $collection->setPageSize($limit)->setOrder('id', 'desc');
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

        if ($show_breadcrumbs && $breadcrumbsBlock) {
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
                    'link' => $baseUrl . 'affiliate'
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
