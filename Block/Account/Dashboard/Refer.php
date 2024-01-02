<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Block\Account\Dashboard;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Dashboard Customer Info
 */
class Refer extends \Magento\Framework\View\Element\Template
{
    /**
     * Cached subscription object
     *
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscription;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /** @var \Magento\Customer\Helper\View */
    protected $_helperView;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Helper\View $helperView
     * @param \Lof\Affiliate\Model\LeadAffiliateFactory $leadAffiliateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $helperView,
        \Lof\Affiliate\Model\LeadAffiliateFactory $leadAffiliateFactory,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_helperView = $helperView;
        $this->_leadAffiliateFactory = $leadAffiliateFactory;
        parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            if(!isset($this->_currentCustomer)){
                $this->_currentCustomer =  $this->currentCustomer->getCustomer();
            }
            return $this->_currentCustomer;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get the full name of a customer
     *
     * @return string full name
     */
    public function getName()
    {
        return $this->_helperView->getCustomerName($this->getCustomer());
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }

    public function getAffiliateInfo(){
        $customer_email = $this->getCustomer()->getEmail();
        $affiliate = null;
        if($customer_email){
            $collection = $this->_leadAffiliateFactory->create()->getCollection()
                                ->addFieldToFilter("customer_email", $customer_email)
                                ->addFieldToFilter("signup_number", ["gteq" => 1]);

            $collection->getSelect()->join(
                    ['af' => $collection->getTable("lof_affiliate_account")],
                    'main_table.account_id = af.accountaffiliate_id',
                    ["affiliate_fullname" => "af.fullname", "affiliate_email" => "af.email", "tracking_code"=>"af.tracking_code"]
                )->group(
                    'main_table.account_id'
                );
            if($collection->count()){
                $affiliate = $collection->getFirstItem();
            }
        }
        return $affiliate;
    }

}
