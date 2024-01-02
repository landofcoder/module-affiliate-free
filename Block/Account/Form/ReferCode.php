<?php

namespace Lof\Affiliate\Block\Account\Form;
/**
 * Customer edit form block
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */

use Magento\Customer\Model\Session;

class ReferCode extends \Magento\Framework\View\Element\Template
{

    protected $session;
    protected $_helper;
    protected $objectManager;
    protected $_accountAffiliateFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $customerSession,
        \Lof\Affiliate\Model\AccountAffiliateFactory $accountAffiliateFactory,
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->_helper = $helper;
        $this->objectManager = $objectManager;
        $this->_accountAffiliateFactory = $accountAffiliateFactory;

        $template = 'Lof_Affiliate::form/refercode.phtml';
        if ($this->hasData("template") && $this->getData("template")) {
            $template = $this->getData("template");
        }
        if (isset($data['template']) && $data['template']) {
            $template = $data['template'];
        }
        $this->setTemplate($template);
        parent::__construct($context, $data);
    }

    public function getAffiliateAccount()
    {
        if(!isset($this->_affiliate_account)){
            $customerId = $this->session->getCustomerId();
            $model = $this->_accountAffiliateFactory->create();
            $this->_affiliate_account = $model->load($customerId, 'customer_id');
        }
        return $this->_affiliate_account;
    }

    public function checkExistAccount()
    {
        if(!isset($this->_is_exist_affiliate_account)){
            $accountAffiliate = $this->getAffiliateAccount();
            if (!$accountAffiliate->getId()) {
                $this->_is_exist_affiliate_account = false;
            } else {
                if($accountAffiliate->getIsActive()) {
                    $this->_is_exist_affiliate_account = true;
                }else{
                    $this->_is_exist_affiliate_account = false;
                }
            }
        }
        return $this->_is_exist_affiliate_account;
    }

    /**
     * Get Default Helper
     *
     * @param
     * @return helper
     */
    public function getAffiliateHelper()
    {
        return $this->_helper;
    }

    public function getSiteUrl()
    {
        $isSecure = $this->_storeManager->getStore()->isCurrentlySecure();
        if ($isSecure) {
            return $this->getUrl('', array('_secure' => true));
        } else {
            return $this->getUrl('');
        }
    }

    /**
     * Prepare Layout
     *
     * @param no param
     * @return void
     */
    public function _prepareLayout()
    {
        $page_title = __('Refer Code');
        $meta_description = __('Refer Code');
        $meta_keywords = __('Refer Code');
        $this->_addBreadcrumbs();
        if ($page_title) {
            $this->pageConfig->getTitle()->set($page_title);
        }
        if ($meta_keywords) {
            $this->pageConfig->setKeywords($meta_keywords);
        }
        if ($meta_description) {
            $this->pageConfig->setDescription($meta_description);
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $brand
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = __('Refer Code');
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
