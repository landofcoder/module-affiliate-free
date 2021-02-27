<?php
/**
 * landofcoder
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
 * @category   landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Controller\Adminhtml\GroupAffiliate;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Affiliate::group_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('group_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            if ($id != 1) {
                $title = "";
                try {
                    // init model and delete
                    $model = $this->_objectManager->create('Lof\Affiliate\Model\GroupAffiliate');
                    $model->load($id);
                    $title = $model->getTitle();
                    $model->delete();
                    // display success message
                    $this->messageManager->addSuccess(__('The Group Affiliate has been deleted.'));
                    // go to grid
                    $this->_eventManager->dispatch(
                        'adminhtml_cmspage_on_delete',
                        ['title' => $title, 'status' => 'success']
                    );
                    return $resultRedirect->setPath('*/*/');
                } catch (\Exception $e) {
                    $this->_eventManager->dispatch(
                        'adminhtml_cmspage_on_delete',
                        ['title' => $title, 'status' => 'fail']
                    );
                    // display error message
                    $this->messageManager->addError($e->getMessage());
                    // go back to edit form
                    return $resultRedirect->setPath('*/*/edit', ['group_id' => $id]);
                }
            }
            // display error message
            $this->messageManager->addError(__('We can\'t delete this group.'));
        } else {
            // display error message
            $this->messageManager->addError(__('We can\'t find a group affiliate to delete.'));
        }
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
