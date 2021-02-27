<?php
namespace Lof\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
// use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\Timezone;

class CreditmemoRefund implements ObserverInterface
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

        $creditmemo = $observer->getEvent ()->getCreditmemo ();
        $order = $creditmemo->getOrder ();
        $priceOrder = $order->getBaseSubtotal();
        $orderStt = $order->getStatus();
        $orderId = $order->getEntityId();

        $closed_order_status = 'closed';

        if($orderStt == $closed_order_status){
            # get orderData in transaction table
            $orderCancel = $this->_helper->getDataOrderAffiliate($order->getIncrementId());
            if (!empty($orderCancel)) {
                foreach ($orderCancel as $orderData) {
                    $affiliate_code = $orderData['affiliate_code'];
                    # 1 - update status or order in table transaction
                    $dataCancel = array(
                        'order_id' => $orderId,
                        'increment_id' => $order->getIncrementId(),
                        'transaction_stt' => $closed_order_status,
                        'order_status' => $closed_order_status,
                        'is_active' => 1
                    );
                    $this->_helper->updateTransactionOrder($dataCancel);

                    $this->_helper->cancelBalanceAffiliate($affiliate_code, $orderData['commission_total']);
                    
                    $this->_helper->saveDataCommissionCancel($affiliate_code, $priceOrder, $orderData['commission_total']);

//                    try {
//                        # 5 - send mail report  user affiliate
//                        $emailidentifier = self::EMAIL_IDENTIFIER_CANCEL;
//                        $emailTo = $orderData['email_aff'];
//                        $emailFrom = $this->_helper->getConfig('general_settings/sender_email_identity');
//                        $templateVar = array(
//                            'name' => $orderData['customer_email']
//                        );
//                        $emailidentifier = self::EMAIL_IDENTIFIER_CANCEL;
//                        $this->_helper->sendMail($emailFrom,$emailTo,$emailidentifier,$templateVar);
//                    } catch (Exception $e) {
//                        $this->messageManager->addException($e, __('You can\'t send a request.'));
//                    }
                }
            }
        }
        // end
    }
}
