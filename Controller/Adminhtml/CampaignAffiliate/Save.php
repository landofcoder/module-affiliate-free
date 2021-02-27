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
namespace Lof\Affiliate\Controller\Adminhtml\CampaignAffiliate;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    
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

    protected $_dataHelper;
    /**
     * Serializer.
     *
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Stdlib\DateTime\Timezone $_stdTimezone,
        \Lof\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Serialize\SerializerInterface $serializer
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->_stdTimezone = $_stdTimezone;
        $this->_dataHelper = $dataHelper;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Affiliate::campaign_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $data = $this->getRequest()->getPostValue(); 

        $id = $this->getRequest()->getParam('campaign_id');

        $dateTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Lof\Affiliate\Model\CampaignAffiliate');

            
            if ($id) {
                $model->load($id);
            } else {
                $data['tracking_code'] = $this->_dataHelper->getAffiliateTrackingCode();
            }

            $data['commission'] = '';
            if(isset($data['tier_price'])){
                $value_arr = [];
                foreach ($data['tier_price'] as $value) {
                    if ($value['is_deleted'] != 1) {
                        array_push($value_arr,$value);
                    }
                }
                if (!empty($value_arr)) {
                    $data['commission'] = serialize($value_arr);
                }
                unset($data['tier_price']);
            }

            if(isset($data['rule'])){
                $data['conditions'] = $data['rule']['conditions'];
                $data['conditions'] = $this->serializer->serialize($data['conditions']);
                unset($data['rule']);
            }
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $data['create_at'] = $dateTimeNow;

            $model->setData($data);
            try { 
                $model->save();

                $this->messageManager->addSuccess(__('You saved this Campaign Affiliate.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['campaign_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the testimonial.'));
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['campaign_id' => $this->getRequest()->getParam('campaign_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

}