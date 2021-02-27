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
namespace Lof\Affiliate\Controller\Adminhtml\WithdrawAffiliate;
use Magento\Backend\App\Action\Context;

class Callback extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\Affiliate\Model\WithdrawAffiliate
     */
    protected $_withdrawModel;

    /**
     * @param Context
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\WithdrawAffiliate $withdrawModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_withdrawModel = $withdrawModel;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Affiliate::withdraw');
    }

    public function execute()
    {
        $request = $this->getRequest()->getParams();
        $withdrawId = $this->getRequest()->getParam('withdraw_id');
        $model = $this->_withdrawModel->load($withdrawId);
        $model->setData('status', 1);

        try {
            $transaction_data = "";
            if(is_array($request)){
                $transaction_data = serialize($request);
                $model->setData("transaction_data", $transaction_data);
            }
            
            $model->save();
            $this->messageManager->addSuccess(
                    __('You payout success.')
                );            
            
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('You can\'t payout.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/withdrawaffiliate/index');
        return $resultRedirect;
    }
    
}