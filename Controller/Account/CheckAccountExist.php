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



class CheckAccountExist extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Lof\Affiliate\Model\AccountAffiliate
     */
    protected $_accountAff;

    protected $_resourceAccount;
    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context      $context           
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory 
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\Affiliate\Model\AccountAffiliate $accountAffiliate
        ){
        $this->resultPageFactory = $resultPageFactory;
        $this->_accountAff = $accountAffiliate;
        parent::__construct($context);
    } 
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $email_address = $this->getRequest()->getParam('email_address');
        $message = '';
        $account = $this->_accountAff->checkAccExist($email_address);
        if(!empty($account)){
            $message .= '<div class=\'mage-success\' style="color:red">You can\'t use this email address.</div><input type="hidden" id="is_valid_email" name="is_valid_email" value="0"/>';
        }else{
            $message .= '<div class=\'mage-success\' style="color:blue">You can use this email address.</div><input type="hidden" id="is_valid_email" name="is_valid_email" value="1"/>';
        }
        echo $message;
    }
}