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
namespace Lof\Affiliate\Block\Affiliate;
use Magento\Customer\Model\Session;

use Magento\SalesRule\Model\RuleFactory as RuleFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

class Home extends \Magento\Framework\View\Element\Template
{
    protected $session;
    protected $_helper;
    protected $_helperImage;
    protected $_stdTimezone;


    protected $ruleFactory;
    protected $categoryCollectionFactory;
    protected $groupFactory;
    protected $websiteFactory;

    protected $objectManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Magento\Framework\Registry                      $registry    
     * @param \Ves\Blog\Model\Post                             $postFactory 
     * @param \Ves\Blog\Helper\Data                            $blogHelper  
     * @param array                                            $data        
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Lof\Affiliate\Helper\Image $helperImage,
        \Magento\Framework\Stdlib\DateTime\Timezone $_stdTimezone,


        RuleFactory $ruleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,


        array $data = []
        ) {

        $this->session = $customerSession;
        $this->_helper = $helper;
        $this->_helperImage = $helperImage;
        $this->_stdTimezone = $_stdTimezone;

        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->groupFactory = $groupFactory;
        $this->websiteFactory = $websiteFactory;

        $this->objectManager= $objectManager;


        parent::__construct($context);

    }

    public function _prepareLayout()
    {
        $page_title = 'Affiliate';
        $meta_description = 'Affiliate';
        $meta_keywords = 'affilicate';

        $this->_addBreadcrumbs();

        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
       
        return parent::_prepareLayout();
    }

    /**
     * Get Default Helper
     *
     * @param
     * @return helper
     */
    public function getAffiliateHelper() {
        return $this->_helper;
    }

    public function checkExistAccount() {
        $customerId = $this->session->getCustomerId();
        $model = $this->objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountAffiliate = $model->load($customerId, 'customer_id')->getData();
        if (empty($accountAffiliate)) {
            return false;
        } else {
            return true;
        }
    }

    public function getAffiliateInfo () {
        $customerId = $this->session->getCustomerId();
        $model = $this->objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $accountAffiliate = $model->load($customerId, 'customer_id')->getData();
        $groupModel = $this->objectManager->create('Lof\Affiliate\Model\GroupAffiliate');
        if (!empty($accountAffiliate) && $accountAffiliate['group_id']) {
            $groupModel->load($accountAffiliate['group_id']);
        } else {
            $groupModel->load(1);
        }
        $group = $groupModel->getData();
        return $group;
    }

    /**
     * Get Default Helper
     *
     * @param
     * @return helper
     */
    public function getAffiliateHelperImage() {
        return $this->_helperImage;
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $brand
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = 'Databroad';
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