<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Autosearch
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Controller\Adminhtml\Report;

use Magento\Framework\App\Action\Action;
use Magento\Store\Model\StoreManagerInterface;

class Filter extends Action
{
    // protected $catalogSearchData;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
    * @var \Lof\Affiliate\Model\TransactionAffiliate
    */
    protected $_transaction;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        StoreManagerInterface $storeManager,
        \Lof\Affiliate\Model\TransactionAffiliate $transaction
        ){
        $this->_storeManager     = $storeManager;
        $this->_transaction  = $transaction;
        parent::__construct($context);
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $date_from = $data['date_from'];
        $date_to = $data['date_to'];
        $email_aff = $data['account'];
        $report_by = $data['report_by'];

        $month = array();
        $data = array();
        $order_data = array();
        $order_aff_data = array();

        $collection = $this->_transaction->getAllSalesByDate($date_from,$date_to);
        
        foreach ($collection as $key) {
            
            $date = date_parse_from_format("Y-m-d", $key['created_at']);
            $date_month = date('M', strtotime($key['created_at']));
            $date_month1 = date('m', strtotime($key['created_at']));
            
            if(!in_array($date_month,$month)){
                if($report_by == 'sales'){
                    $sales_month = $this->_transaction->getCountOrderOfMonth($date_month1);
                    $sales_aff_month = $this->_transaction->getCountOrderAffOfMonth($date_month1, $email_aff); 
                }else{
                    $sales_month = $this->_transaction->getCountMoneyOfMonth($date_month1);
                    $sales_aff_month = $this->_transaction->getCountMoneyAffOfMonth($date_month1, $email_aff);
                }
                
                array_push($month, $date_month);
                array_push($order_data, $sales_month);
                array_push($order_aff_data,$sales_aff_month);

            }
        } 
        $sales_base_data = array("name"=>"All","data"=>$order_data);
        $sales_aff_data = array("name" => "Affiliate" , "data" => $order_aff_data);
        array_push($data, $sales_base_data);
        array_push($data,$sales_aff_data);

        $month_json = json_encode($month); 
        $data_json = json_encode($data);
        $json          = [];
        $json['categories'] = $month_json;
        $json['data'] = $data_json;

            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($json)
                );
            return;
    } 
}