<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Model;

//use Magento\Braintree\Model\Adapter\BraintreeCreditCard;
//use Magento\Braintree\Model\Adapter\BraintreeTransaction;
//use \Braintree_Exception;
//use \Braintree_Transaction;
//suse \Braintree_Result_Successful;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Payment\Model\InfoInterface;

/**
 *
 */
class PaymentMethod extends \Magento\Payment\Model\Method\Cc
{

    const CAPTURE_ON_INVOICE = 'invoice';
    const CAPTURE_ON_SHIPMENT = 'shipment';
    const CHANNEL_NAME = 'Magento';
    const METHOD_CODE = 'braintree';
    const REGISTER_NAME = 'braintree_save_card';
    const CONFIG_MASKED_FIELDS = 'masked_fields';

    /**
     * @var string
     */
    protected $_formBlockType = 'Magento\Braintree\Block\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento\Braintree\Block\Info';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * @var bool
     */
    protected $_canSaveCc = false;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var string
     */
    protected $merchantAccountId = '';

    /**
     * @var bool
     */
    protected $allowDuplicates = true;

    /**
     * @var array|null
     */
    protected $requestMaskedFields = null;

    /**
     * @var \Magento\Braintree\Model\Config\Cc
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Braintree\Helper\Data
     */
    protected $braintreeHelper;

    /**
     * @var \Magento\Braintree\Helper\Error
     */
    protected $errorHelper;

    /**
     * @var TransactionCollectionFactory
     */
    protected $salesTransactionCollectionFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetaData;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var BraintreeTransaction
     */
    protected $braintreeTransaction;

    /**
     * @var BraintreeCreditCard
     */
    protected $braintreeCreditCard;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\App\RequestInterface $request,
        TransactionCollectionFactory $salesTransactionCollectionFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
        //$this->vault = $vault;
        $this->request = $request;
        //$this->errorHelper = $errorHelper;
        $this->salesTransactionCollectionFactory = $salesTransactionCollectionFactory;
        $this->productMetaData = $productMetaData;
        $this->regionFactory = $regionFactory;
        $this->orderRepository = $orderRepository;
    }
}
