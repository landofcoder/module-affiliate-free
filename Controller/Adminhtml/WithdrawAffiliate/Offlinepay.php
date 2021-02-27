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

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Offlinepay extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;


    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Stdlib\DateTime\Timezone $_stdTimezone
    ) {
        $this->_coreRegistry = $registry;
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->_stdTimezone = $_stdTimezone;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Affiliate::transaction_pay');
    }
    
    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
       // 1. Get ID and create model
        $id = $this->getRequest()->getParam('withdraw_id');
        $type = $this->getRequest()->getParam('type');
        $update_request = $this->getRequest()->getParam('update_request');
        $data = $this->getRequest()->getPostValue(); 
        $dateTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
        $model = $this->_objectManager->create('Lof\Affiliate\Model\WithdrawAffiliate');
        $resultRedirect = $this->resultRedirectFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Affiliate Withdrawl Request no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
        ->getDirectoryRead(DirectoryList::MEDIA);
        $mediaFolder = 'lof/affiliate/attachments/';
        $path = $mediaDirectory->getAbsolutePath($mediaFolder);

        // Delete, Upload Image
        $imagePath = $mediaDirectory->getAbsolutePath($model->getAttachment());
        if(isset($data['attachment']['delete']) && file_exists($imagePath)){
            unlink($imagePath);
            $data['attachment'] = '';
        } else {
            if(isset($data['attachment']) && is_array($data['attachment'])){
                unset($data['attachment']);
            }
            if($image = $this->uploadImage('attachment')){
                $data['attachment'] = $image;
            }
        }

        $data['transaction_data'] = isset($data['transaction_data'])?$data['transaction_data']:'';
        $model->setData('transaction_data', $data['transaction_data']);
        $model->setData('attachment', $data['attachment']);
        $model->setAttachment($data['attachment']);
        //3. check if update data or pay
        if($update_request && !$type) {
            $bank_data = [];
            $bank_data['bank_account_name'] = $data['bank_account_name'];
            $bank_data['bank_account_number'] = $data['bank_account_number'];
            $bank_data['swift_code'] = $data['swift_code'];
            $bank_data['bank_name'] = $data['bank_name'];
            $bank_data['bank_branch_city'] = $data['bank_branch_city'];
            $bank_data['bank_branch_country_code'] = $data['bank_branch_country_code'];
            $bank_data['intermediary_bank_code'] = $data['intermediary_bank_code'];
            $bank_data['intermediary_bank_name'] = $data['intermediary_bank_name'];
            $bank_data['intermediary_bank_city'] = $data['intermediary_bank_city'];
            $bank_data['intermediary_bank_country_code'] = $data['intermediary_bank_country_code'];

            $bank_data_info = serialize($bank_data);
            $model->setData('banktransfer_data', $bank_data_info);
            $model->setData('date_update', $dateTimeNow);

        } 
        if($type=="offlinepay"){
            $model->setData('date_paid', $dateTimeNow);
            $model->setData('status', 1);
        }
        try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this withdrawl Data.'));
                if($type=="offlinepay"){
                    $this->messageManager->addSuccess(__('You paid offline the withdraw successfully.'));
                }
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                return $resultRedirect->setPath('*/*/edit', ['withdraw_id' => $model->getId(), '_current' => true]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the withdrawl.'));
                $this->messageManager->addError($e->getMessage());
            }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'attachment')
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );

            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/affiliate/attachments/';
            try {
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                    );
                return $mediaFolder.$result['name'];
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['withdraw_id' => $this->getRequest()->getParam('withdraw_id')]);
            }
        }
        return;
    }

}
