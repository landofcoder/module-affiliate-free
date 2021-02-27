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
namespace Lof\Affiliate\Controller\Account; 

use Magento\Customer\Model\Session;

class Campaign extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_bannerModel;

    /**
     * @var Session
     */
    protected $session;
    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context      $context           
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory 
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Affiliate\Model\BannerAffiliate $bannerModel,
        Session $customerSession
        ){
        $this->resultPageFactory = $resultPageFactory;
        $this->_bannerModel = $bannerModel;
        $this->session = $customerSession;
        parent::__construct($context);
    } 
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        //echo "<pre>"; print_r('controller/campaign/'); die;
        if (!$this->session->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('affiliate/account/login');
            return $resultRedirect;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Commission Campagins'));
        // die(count($this->_modelAccount->checkAccountExist()));
        // die($this->_modelAccount->checkAccountExist());
        return $resultPage;
    }
}