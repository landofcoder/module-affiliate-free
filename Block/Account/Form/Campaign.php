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
class Campaign extends \Magento\Framework\View\Element\Template
{

    protected $_commissionCollectionFactory; 
    protected $session;
    protected $_resource;
    protected $priceCurrency;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Model\ResourceModel\CommissionAffiliate\CollectionFactory $commissionCollectionFactory,
        Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\Affiliate\Helper\Data $helper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
        ) {
        $this->_commissionCollectionFactory = $commissionCollectionFactory;
        $this->session = $customerSession;
        $this->_resource = $resource;
        $this->priceCurrency = $priceCurrency;

        $this->_helper = $helper;

        parent::__construct($context, $data);
    }

    public function getHelper() {
        return $this->_helper;
    }

    public function _toHtml()
    {
        $affiliate_code = $this->_helper->getTrackingCode();

        $campaign_code = $this->getRequest()->getParam('code');

        $template = 'Lof_Affiliate::form/campaign.phtml';
        
        $this->setTemplate($template);

        $commissionCollection = $this->_commissionCollectionFactory->create()
        ->addFieldToFilter('affiliate_code', $affiliate_code);
        
        if (!empty($campaign_code)) {
            $commissionCollection->addFieldToFilter('campaign_code', $campaign_code);
        }
        
        $this->setCommissionCollection($commissionCollection);

        return parent::_toHtml();
    }

    public function setCommissionCollection($collection){
        $this->_collection = $collection;
        return $this;
    }

    public function getCommissionCollection(){
        return $this->_collection;

    }

    public function getPagerHtml()
    {
        // $numberItem = (int)$this->getConfig('number_item');
        // $item_per_page = (int)$this->getConfig('item_per_page');
        $item_per_page = '5';
        $name = 'lof.affiliate.campaign' . time() . uniqid();
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    $name
                );
                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(false)
                    ->setShowPerPage(false)
                    ->setLimit($item_per_page)
                    ->setCollection($this->getCommissionCollection());
            }
            if ($this->_pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->_pager->toHtml();
            }
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
        $this->pageConfig->getTitle()->set(__('Campaign'));
        $this->_addBreadcrumbs();
        return parent::_prepareLayout();
    }

    protected function _addBreadcrumbs()
    {

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = 'Campaign';
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
