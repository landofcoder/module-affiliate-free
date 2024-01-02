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

    protected $_helper;

    protected $_accountAffiliateFactory;

    protected $groupAffiliate;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Model\ResourceModel\TransactionAffiliate\CollectionFactory $transactionCollectionFactory,
        Session $customerSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Lof\Affiliate\Model\AccountAffiliateFactory $accountAffiliateFactory,
        \Lof\Affiliate\Model\GroupAffiliateFactory $groupAffiliate,
        array $data = []
    ) {
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->session = $customerSession;
        $this->_helper = $helper;
        $this->_accountAffiliateFactory = $accountAffiliateFactory;
        $this->groupAffiliate = $groupAffiliate;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _toHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session')->getCustomer();

        $customerId = $customerSession->getCustomerId();
        $template = 'Lof_Affiliate::form/info.phtml';
        $fullname = $customerSession->getFirstname() . ' ' . $customerSession->getLastname();
        $this->assign('fullname', $fullname);
        $this->setTemplate($template);
        $transactionCollection = $this->_transactionCollectionFactory->create()
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('date_added', 'DESC');
        $this->setTransactionCollection($transactionCollection);
        return parent::_toHtml();
    }

    public function setTransactionCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getTransactionCollection()
    {
        return $this->_collection;

    }

    public function getPagerHtml()
    {
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

    public function getInfoPostLink()
    {
        $isSecure = $this->_storeManager->getStore()->isCurrentlySecure();
        if ($isSecure) {
            return $this->getUrl('affiliate/account/infopost', ['_secure' => true]);
        } else {
            return $this->getUrl('affiliate/account/infopost');
        }
    }

    /**
     * Get Default Helper
     *
     * @param
     * @return helper
     */
    public function getAffiliateHelper()
    {
        return $this->_helper;
    }

    public function checkExistAccount()
    {
        if (!isset($this->_is_exist_affiliate_account)) {
            $accountAffiliate = $this->getAffiliateAccount();
            if (!$accountAffiliate->getId()) {
                $this->_is_exist_affiliate_account = false;
            } else {
                if ($accountAffiliate->getIsActive()) {
                    $this->_is_exist_affiliate_account = true;
                } else {
                    $this->_is_exist_affiliate_account = false;
                }
            }
        }
        return $this->_is_exist_affiliate_account;
    }

    public function getAffiliateAccount()
    {
        if (!isset($this->_affiliate_account)) {
            $customerId = $this->session->getCustomerId();
            $model = $this->_accountAffiliateFactory->create();
            $this->_affiliate_account = $model->load($customerId, 'customer_id');
        }
        return $this->_affiliate_account;
    }

    public function getAffiliateGroup()
    {
        $collection = $this->groupAffiliate->create()->getCollection()->addFieldToFilter('is_active', 1);
        return $collection->getData();
    }

    public function getPaymentMedthod()
    {
        return $this->_helper->getPaymentMethod();
    }

}
