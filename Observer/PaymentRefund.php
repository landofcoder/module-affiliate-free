<?php
namespace Lof\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
// use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\Timezone;

class PaymentRefund implements ObserverInterface
{      

    CONST EMAIL_IDENTIFIER_CANCEL = 'sent_mail_after_order_cancel';

    protected $_resource;
    protected $_request;
    protected $logger;
    protected $_objectManager;

    // @param Lof\Affiliate\Helper\Data
    protected $_helper;
    // @var \Magento\Framework\Stdlib\DateTime\Timezone
    protected $_stdTimezone;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Lof\Affiliate\Helper\Data $helper,
        Timezone $stdTimezone
    ){
        $this->_request = $context->getRequest();
        $this->logger = $logger;
        $this->_resource = $resource;
        $this->_objectManager= $context->getObjectManager();
        $this->_helper = $helper;
        $this->_stdTimezone = $stdTimezone;
    }
    
    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->_helper->getConfig('general_settings/enable'))
            return;
        $order = $observer->getEvent()->getOrder();
        $orderStt = $order->getStatus();
        $orderId = $order->getEntityId();

        $closed_order_status = 'closed';

        if($orderStt == 'closed'){
            # get orderData in transaction table
            $orderData = $this->_helper->getDataOrderAffiliate($orderId);
            if (!empty($orderData)) {
                $campaign_code = $orderData['campaign_code'];
                $affiliate_code = $orderData['affiliate_code'];
                # 1 - update status or order in table transaction
                $dataCancel = array(
                    'order_id' => $orderId,
                    'transaction_stt' => $closed_order_status,
                    'order_status' => $closed_order_status,
                    'is_active' => 1
                );
                $this->_helper->updateTransactionOrder($dataCancel);

                # 2 - cancel balance affiliate
                $this->_helper->cancelBalanceAffiliate($affiliate_code, $orderData['commission_total']);
                if ($campaign_code != '') {
                    # 3 - cacluate commission for affiliate
                    $currentDate = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
                    $campaign = $this->_helper->getListCampaignByCurrentDate($campaign_code, $currentDate);
                    # 4 - check datenow >= start date && <= end date
                    if (isset($campaign) || !empty($campaign)) {
                        $this->_helper->saveDataCommissionCancel($affiliate_code, $campaign_code, $priceOrder);
                    }
                }

                try {
                    # 5 - send mail report  user affiliate
                    $emailidentifier = self::EMAIL_IDENTIFIER_CANCEL;
                    $emailTo = $orderData['email_aff'];
                    $emailFrom = $this->_helper->getConfig('general_settings/sender_email_identity');
                    $templateVar = array(
                        'name' => $orderData['customer_email']
                    );
                    $emailidentifier = self::EMAIL_IDENTIFIER_CANCEL;
                    $this->_helper->sendMail($emailFrom,$emailTo,$emailidentifier,$templateVar);  
                } catch (Exception $e) {
                    $this->messageManager->addException($e, __('You can\'t send a request.'));
                }
            }
        }
        // end
    }
}
