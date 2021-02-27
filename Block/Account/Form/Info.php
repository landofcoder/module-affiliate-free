<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\Affiliate\Block\Account\Form;
/**
 * Customer edit form block
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */

use Magento\Customer\Model\Session;
class Info extends \Magento\Framework\View\Element\Template
{
    /**
    * @var Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\Collection
    */
    protected $_transactionCollectionFactory; 

    /**
     * @var Session
     */
    protected $session;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\CollectionFactory $transactionCollectionFactory,
        Session $customerSession,
        array $data = []
        ) {
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    public function _toHtml()
    {
        $customerId= $this->session->getCustomerId();
        $template = 'Lof_Affiliate::form/info.phtml';
        $fullname = $this->session->getCustomer()->getFirstname().' '.$this->session->getCustomer()->getLastname();
        $this->assign('fullname', $fullname);
        $this->setTemplate($template);
        $transactionCollection = $this->_transactionCollectionFactory->create()
        ->addFieldToFilter('is_active', '1')
        ->addFieldToFilter('customer_id',$customerId)
        ->setOrder('date_added', 'DESC');
        $this->setTransactionCollection($transactionCollection);
        return parent::_toHtml();
    }

    public function setTransactionCollection($collection){
        $this->_collection = $collection;
        return $this;
    }

    public function getTransactionCollection(){
        return $this->_collection;

    }

    public function getPagerHtml()
    {
        // $numberItem = (int)$this->getConfig('number_item');
        // $item_per_page = (int)$this->getConfig('item_per_page');
        $item_per_page = '5';
        $name = 'lof.affiliate.transaction' . time() . uniqid();
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    $name
                );
                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(false)
                    ->setShowPerPage(false)
                    ->setLimit($item_per_page)
                    ->setCollection($this->getTransactionCollection());
            }
            if ($this->_pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->_pager->toHtml();
            }
    }

    public function getInfoPostLink(){
        $isSecure = $this->_storeManager->getStore()->isCurrentlySecure();
        if($isSecure) {
            return $this->getUrl('affiliate/account/infopost', array('_secure'=>true));
        } else {
            return $this->getUrl('affiliate/account/infopost');
        }
    }

}
