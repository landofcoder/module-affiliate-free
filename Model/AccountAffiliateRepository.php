<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Model;

use Lof\Affiliate\Api\AccountRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AccountRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AccountAffiliateRepository implements AccountRepositoryInterface
{
    protected $helper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_collection;

    protected $_quote;

    protected $_campaignAffiliate;

    protected $_campaignAffiliateFactory;

    protected $transactionAffiliate;

    /**
     * @var AccountAffiliateFactory
     */
    protected $accountAffiliateFactory;

    /**
     * @param \Lof\Affiliate\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Lof\Affiliate\Model\CampaignAffiliate $campaignAffiliate
     * @param TransactionAffiliateFactory $transactionAffiliate
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Lof\Affiliate\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\Collection $collection,
        \Magento\Quote\Model\Quote $quote,
        \Lof\Affiliate\Model\CampaignAffiliate $campaignAffiliate,
        \Lof\Affiliate\Model\CampaignAffiliateFactory $campaignAffiliateFactory,
        TransactionAffiliateFactory $transactionAffiliate,
        AccountAffiliateFactory $accountAffiliateFactory
    ) {
        $this->helper = $helper;
        $this->accountAffiliateFactory = $accountAffiliateFactory;
        $this->_collection = $collection;
        $this->_quote = $quote;
        $this->_campaignAffiliate = $campaignAffiliate;
        $this->_campaignAffiliateFactory = $campaignAffiliateFactory;
        $this->transactionAffiliate = $transactionAffiliate;
    }

    /**
     * Use Refer Code
     *
     * @param mixed $data
     * @return bool true on success
     * @throws CouldNotSaveException
     */
    public function useReferCode($data)
    {
        if (!$this->helper->getConfig('general_settings/enable')) {
            return false;
        }

        //check customer exist
        $customerData = $this->helper->getDataCustomerByEmail($data['customer_email']);
        if (!$customerData) {
            return false;
        }

        if ($this->helper->getConfig('general_settings/auto_create')) {
            $dataEmail = [
                'paypal_email' => $data['customer_email'],
                'skrill_email' => $data['customer_email'],
                'refering_website' => '',
            ];
            $this->helper->createAffiliateAccount($dataEmail, $customerData);
        }
        if (isset($data['refer_code'])) {
            $affiliateCode = $data['refer_code'];
            $affiliateAccount = $this->helper->getAffiliateAccountByTrackingCode($affiliateCode);
            if ($affiliateAccount->getId()) {
                $campaignCode = $affiliateAccount->getCampaignCode();
                // Pay Per Lead
                if ($campaignCode) {
                    $this->helper->checkPPLCommission($affiliateCode, $campaignCode, $customerData);
                }
                // Add Refering Customer
                if ($affiliateCode) {
                    $this->helper->addReferingCustomer($affiliateCode, $data['customer_email'], $campaignCode);
                }
            }
            return true;
        }
        return false;
    }

    public function createTransaction($data)
    {
        if (!$this->helper->getConfig('general_settings/enable')) {
            return false;
        }

        // check all order id and compare with conditions config
        $orderIds = $data['orderIds'];
        if (!$orderIds || !is_array($orderIds)) {
            return $this;
        }
        $this->_collection->addFieldToFilter('entity_id', ['in' => $orderIds]);
        foreach ($this->_collection as $order) {
            //check email and id match the order
            $email = $order->getCustomerEmail();
            $customer_id = $order->getCustomerId();
            if ($email != $data['customer_email'] || $customer_id != $data['customer_id']) {
                return false;
            }

            //check complete order status
            $orderStatus = $order->getStatus();
            $complete_order_status = $this->helper->getConfig('general_settings/order_status');
            $complete_order_status = $complete_order_status ? $complete_order_status : 'complete';
            if ($orderStatus == $complete_order_status) {
                return false;
            }

            //check exist Affiliate Transaction
            $collectionTransactionAffiliate = $this->transactionAffiliate->create()->getCollection();
            $ordersData = $collectionTransactionAffiliate->addFieldToFilter('order_id', $order->getId())->getData();
            if (!empty($ordersData)) {
                return false;
            }

            $dataReferringCustomer = $this->helper->getDataReferringCustomerByEmail($order->getCustomerEmail());
            $order_id = $order->getEntityId();
            $orderSubTotal = $order->getSubtotal();
            $incrementId = $order->getIncrementId();
            $account_data = $this->helper->getAccountDataByCustomerEmail($order->getCustomerEmail());
            if (!empty($account_data)) {
                $group = $this->helper->getGroupDataByAffiliateId($account_data['tracking_code']);
                if ($group['is_active'] == 1 && $group['commission_ppl'] > 0) {
                    if ($group['commission_ppl_action'] == 1) {
                        $order_commission = $orderSubTotal * $group['commission_ppl'] / 100;
                    } else {
                        $order_commission = $group['commission_ppl'];
                    }
                    $data['account_id'] = $account_data['accountaffiliate_id'];
                    $data['commission_total'] = $order_commission;
                    $data['description'] = __('ppl commision for order #') . $incrementId;
                    $data['affiliate_code'] = $account_data['tracking_code'];
                    $data['email_aff'] = $account_data['email'];
                    $data['campaign_code'] = '';
                    $data['order_id'] = $order_id;
                    $data['order_total'] = $orderSubTotal;
                    $data['order_status'] = $order->getStatus();
                    $data['transaction_stt'] = $order->getStatus();
                    $data['increment_id'] = $incrementId;
                    $data['base_currency_code'] = $order->getBaseCurrencyCode();
                    $data['customer_email'] = $order->getCustomerEmail();
                    $this->helper->saveHistoryOrderAffiliate($data);
                } elseif (isset($dataReferringCustomer)) {
                    $tracking_code = $this->helper->checkValidCampaign($dataReferringCustomer['campaign_code']);
                    $campaign = $this->helper->getCampaignByTrackingCode($tracking_code);
                    $discount_action = $campaign->getDiscountAction();
                    $discount_amount = $campaign->getDiscountAmount();
                    if ($discount_action == 'by_percent') {
                        $orderCommission = $orderSubTotal * $discount_amount / 100;
                    } else {
                        $orderCommission = $discount_amount;
                    }
                    $quote_id = $order->getQuoteId();
                    $quote = $this->_quote->load($quote_id);
                    $dataAddress = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
                    $campaignCollection = $this->_campaignAffiliate->getCollection()->addFieldToFilter('tracking_code', $tracking_code);
                    foreach ($campaignCollection as $item) {
                        if ($item->validate($dataAddress)) {
                            $incrementId = $order->getIncrementId();
                            $data['account_id'] = $account_data['accountaffiliate_id'];
                            $data['order_id'] = $order_id;
                            $data['commission_total'] = $orderCommission;
                            $data['description'] = __('commission for order #') . $incrementId . ' from customer: ' . $order->getCustomerEmail();

                            $data['order_total'] = $orderSubTotal;
                            $data['order_status'] = $order->getStatus();
                            $data['customer_email'] = $order->getCustomerEmail();
                            $data['base_currency_code'] = $order->getBaseCurrencyCode();

                            $data['transaction_stt'] = $order->getStatus();
                            $data['affiliate_code'] = $account_data['tracking_code'];
                            $data['campaign_code'] = $tracking_code;

                            $data['increment_id'] = $incrementId;
                            $data['email_aff'] = $account_data['email'];
                            $this->helper->saveHistoryOrderAffiliate($data);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * complete order.
     *
     * @param mixed $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function completeOrder($data)
    {
        if (!$this->helper->getConfig('general_settings/enable')) {
            return false;
        }

        $priceOrder = $data['order_base_subtotal'];
        $orderStt = $data['order_status'];
        $orderId = $data['order_entity_id'];
        $complete_order_status = $this->helper->getConfig('general_settings/order_status');
        $complete_order_status = $complete_order_status ? $complete_order_status : 'complete';

        $dataTransaction = [
            'order_id' => $orderId,
            'increment_id' => $data['order_increment_id'],
            'transaction_stt' => $complete_order_status,
            'order_status' => $complete_order_status,
            'is_active' => 1
        ];
        $currentStatus = $this->_collection
            ->addFieldToFilter('entity_id', $orderId)
            ->getFirstItem()
            ->getStatus();

        //check order status
        if ($orderStt == 'complete' && $currentStatus != $complete_order_status) {
            # get orderData in transaction table
            $collectionTransactionAffiliate = $this->transactionAffiliate->create()->getCollection();
            $ordersData = $collectionTransactionAffiliate->addFieldToFilter('increment_id', $data['order_increment_id'])->getData();
            if (!empty($ordersData)) {
                foreach ($ordersData as $orderData) {
                    $affiliate_code = $orderData['affiliate_code'];
                    $commission_total = $orderData['commission_total'];
                    $this->helper->updateTransactionOrder($dataTransaction);
                    $this->helper->saveDataCommissionComplete($affiliate_code, $priceOrder, $commission_total);
                }
                return true;
            }
        }
    }

    /**
     * Load Refer Code data by given Account Identity
     *
     * @param string $pageId
     * @return AccountAffiliate
     * @throws NoSuchEntityException
     */
    public function getReferCode($customerId)
    {
        $accountAffiliate = $this->accountAffiliateFactory->create()->getCollection()->addFieldToFilter('customer_id', $customerId);
        if (!$accountAffiliate->getData()) {
            throw new NoSuchEntityException(__('The Refer Code with the "%1" ID doesn\'t exist.', $customerId));
        }
        return $accountAffiliate->getData()[0]['tracking_code'];
    }
    /**
     * Load Page data by given Page Identity
     *
     * @param string $campaginId
     * @return CampaignAffiliate
     * @throws NoSuchEntityException
     */
    public function getById($campaignId)
    {
        $campaign = $this->_campaignAffiliateFactory->create();
        $campaign->load($campaignId);
        if (!$campaign->getId()) {
            throw new NoSuchEntityException(__('The Campaign with the "%1" ID doesn\'t exist.', $campaignId));
        }

        return $campaign;
    }

    /**
     * Save Page data
     *
     * @param \Lof\Affiliate\Api\Data\CampaignInterface $campaign
     * @return CampaignAffiliate
     * @throws CouldNotSaveException
     */
    public function save(\Lof\Affiliate\Api\Data\CampaignInterface $campaign)
    {
        $campaignAffiliate = $this->_campaignAffiliateFactory->create()->setData((array)$campaign->getData())->save();
        return $campaignAffiliate;
    }
}
