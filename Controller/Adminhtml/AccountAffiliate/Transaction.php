<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Controller\Adminhtml\AccountAffiliate;

class Transaction extends \Magento\Catalog\Controller\Adminhtml\Product
{
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder
     * @param \Magento\Framework\View\Result\LayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getparam('account_id');
        $account = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $account->load($id);
        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        $registry->register("affiliate_account", $account);
        $layout = $this->layoutFactory->create();

        $pagesGrid = $layout->createBlock(
            'Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Transaction',
            '',
            ['data' => []]
        );

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($pagesGrid->toHtml());

        return $resultRaw;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Affiliate::account_transaction');
    }
}