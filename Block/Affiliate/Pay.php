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

use Magento\Framework\Data\Form\FormKey;

class Pay extends \Magento\Framework\View\Element\Template
{

    const SSL_URL = 'https://www.paypal.com/cgi-bin/webscr';
    const SSL_SAND_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var FormKey
     */
    protected $formKey;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Magento\Framework\Registry                      $registry    
     * @param \Ves\Blog\Model\Post                             $postFactory 
     * @param \Ves\Blog\Helper\Data                            $blogHelper  
     * @param array                                            $data        
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        FormKey $formKey

        ) {
        $this->_objectManager = $objectManager;
        $this->formKey = $formKey;
        parent::__construct($context);

    }

    public function _toHtml()
    {   
        $id = $this->getRequest()->getParam('withdraw_id');
        $type = $this->getRequest()->getParam('type');
        if($type == 'paypal'){
            $data = $this->getPaypalData($id, $type);
        }elseif ($type =='skrill') {
            $data = $this->getSkrillData($id,$type);
        }elseif ($type =='banktransfer') {
            $data = $this->getBankData($id,$type);
        }

        $this->setData($data); 
        $this->setTemplate($data['template']);   
        return parent::_toHtml();
    }

    public function getPaypalData($id,$type){
        $model = $this->_objectManager->create('Lof\Affiliate\Model\WithdrawAffiliate');
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $model->load($id);
        $accountModel->loadByAttribute('customer_id', $model->getCustomerId());
        $callback = $this->getUrl('*/*/callback',["withdraw_id" => $id,"type" => $type]);
        $notifyurl = $this->getUrl('*/*/notifyurl',["withdraw_id" => $id]);
        $cancel = $this->getUrl('affiliate/withdrawaffiliate/edit',["withdraw_id" => $id]);
        $template = "Lof_Affiliate::form/payment/paypal.phtml";
        $data=array(
            'type' => $type,        
            'merchant_email'=>$accountModel->getPaypalEmail(),
            'product_name'=>'Payout for Affiliate',
            'amount'=>$model->getWithdrawAmount(),
            'currency_code'=> $model->getCurrencyCode(),
            'callback_url'=> $callback,
            'notify_url'=> $notifyurl,
            'cancel_url'=>$cancel,
            'paypal_mode'=>true,
            'template' => $template
        );

        if($data['paypal_mode']){
            $data['action'] = self::SSL_SAND_URL;
        }else{
            $data['action'] = self::SSL_URL;
        }
        return $data;

    }
    
    public function getSkrillData($id, $type){
        $model = $this->_objectManager->create('Lof\Affiliate\Model\WithdrawAffiliate');
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $model->load($id);
        $accountModel->loadByAttribute('customer_id', $model->getCustomerid());
        $callback = $this->getUrl('*/*/callback',["withdraw_id" => $id,"type" => $type]);
        $template = "Lof_Affiliate::form/payment/skrill.phtml";
        $data=array(
            'type' => $type,
            'pay_to_email'=>$accountModel->getSkrillEmail(),
            'amount' => $model->getWithdrawAmount(),
            'currency' => $model->getCurrencyCode(),
            'status_url' => $callback,
            'detail1_description' => 'Payout for Affiliate',
            'detail2_description' => 'Pay To Email',
            'detail2_text' => $accountModel->getSkrillEmail(),
            'detail3_description' => 'Affiliate Id',
            'detail3_text' => $accountModel->getAccountaffiliateId(),
            'amount2_description' => 'Commission Paid',
            'amount2' => $model->getWithdrawAmount(),
            'template' => $template
        );
        return $data;
    }
    /* Disbale Braintree Method
    public function getBrainTreeData($id, $type){
        $template = "Lof_Affiliate::form/payment/braintree.phtml";
        $model = $this->_objectManager->create('Lof\Affiliate\Model\WithdrawAffiliate');
        $accountModel = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $model->load($id);
        $accountModel->loadByAttribute('customer_id', $model->getCustomerid());
        // $callback = $this->getUrl('braintree',["withdraw_id" => $id,"type" => $type]);
        $data = array(
            'type' => $type,
            'amount' => $model->getWithdrawAmount(),
            'callback' => $callback,
            'template' => $template,
            'date_request' => $model->getDateRequest(),
            'status' => $model->getStatus(),
            'currency' => $model->getCurencyCode(),
            'withdraw_id' => $model->getWithdrawId()
            );
        return $data;
    }
    */

    public function _prepareLayout()
    {        
        return parent::_prepareLayout();
    }
}