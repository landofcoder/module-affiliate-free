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
use Magento\Framework\Stdlib\DateTime\Timezone;

class InfoPost extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $session;

    protected $accountAffiliateFactory;
    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    protected $_dataHelper;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Affiliate\Model\ResourceModel\AccountAffiliate\CollectionFactory $accountAffiliateFactory,
        Session $customerSession,
        Timezone $stdTimezone,
        \Lof\Affiliate\Helper\Data $dataHelper
        ){
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;
        $this->accountAffiliateFactory = $accountAffiliateFactory;
        $this->_stdTimezone = $stdTimezone;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    } 
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        
        $resultRedirect = $this->resultRedirectFactory->create();
        if($this->session->isLoggedIn()  && $this->checkAccountExit() != 1){
            $data=$this->getRequest()->getParams();
            $model = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
            $customerData = $this->session->getCustomer();
            $customerId = $this->session->getCustomerId();
            $createTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
            $tracking_code = $this->_dataHelper->getAffiliateTrackingCode();
            $data['customer_id'] = $customerId;
            $data['lastname'] = $customerData->getLastname();
            $data['firstname'] = $customerData->getFirstname();
            $data['is_active'] = '1';            
            $data['commission'] = '0';
            $data['tracking_code'] =  $tracking_code;
            $data['create_at'] = $createTimeNow;
            // print_r($data);die('minh');
            $model->setData($data);
            $model->save();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }else{
            $resultRedirect->setPath('affiliate/affiliate/home');
            return $resultRedirect;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Your Information'));
        return $resultPage;
    }
    public function checkAccountExit()
    {
        $customerId = $this->session->getCustomerId();
        $collection = $this->accountAffiliateFactory->create()
        ->addFieldToFilter('customer_id',$customerId);
        return count($collection);
    }
}