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
class Withdraw extends \Magento\Framework\View\Element\Template
{
    /**
    * @var Lof\Affiliate\Model\ResourceModel\WithdrawAffiliate\Collection
    */
    protected $_withdrawFactory; 

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Session
     */
    protected $session;

    protected $_currency;
    protected $_storeManager;
    protected $_collection;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Model\ResourceModel\WithdrawAffiliate\CollectionFactory $withdrawFactory,
        Session $customerSession,
        \Magento\Directory\Model\Currency $currency,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
        ) {
        $this->_withdrawFactory = $withdrawFactory;
        $this->session = $customerSession;
        $this->_storeManager = $context->getStoreManager();
        $this->_currency = $currency;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function setCollection($collection){
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection(){
        return $this->_collection;
    }

    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }
    
    public function paymentFormLink()
    {
        $isSecure = $this->_storeManager->getStore()->isCurrentlySecure();
        if($isSecure) {
            return $this->getUrl('affiliate/account/paymentform', array('_secure'=>true));
        } else {
            return $this->getUrl('affiliate/account/paymentform');
        }
    }
    public function formatCurrency(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->format($amount, $includeContainer, $precision);
    }

    public function getCustomer()
    {
        $customer = $this->session->getCustomer();
        return $customer;
    }

    protected function _beforeToHtml()
    {
        $toolbar    = $this->getLayout()->getBlock('lof_toolbar');
        $collection = $this->getWithdrawCollection();
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

        public function getWithdrawCollection()
    {
        if(!$this->_collection){
            $limit = $this->getLimit();
            if(!$limit) {
                $limit = 5;
            }
            $collection = $this->_withdrawFactory->create()
                ->addFieldToFilter('affiliate_email', $this->getCustomer()->getEmail());
            $collection->setPageSize($limit)->setOrder('withdraw_id', 'desc');
            $this->setCollection($collection);
        }
        return $this->getCollection();
    }
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Withdraw'));
        $this->_addBreadcrumbs();
        return parent::_prepareLayout();
    }

    protected function _addBreadcrumbs()
    {

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = 'Withdraw';
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
