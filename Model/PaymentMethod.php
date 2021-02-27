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
    
     const CAPTURE_ON_INVOICE        = 'invoice';
    const CAPTURE_ON_SHIPMENT       = 'shipment';
    const CHANNEL_NAME              = 'Magento';
    const METHOD_CODE               = 'braintree';
    const REGISTER_NAME             = 'braintree_save_card';
    const CONFIG_MASKED_FIELDS      = 'masked_fields';

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
    protected $_code                    = self::METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isGateway               = true;

    /**
     * @var bool
     */
    protected $_canAuthorize            = true;

    /**
     * @var bool
     */
    protected $_canCapture              = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial       = true;

    /**
     * @var bool
     */
    protected $_canRefund               = true;

    /**
     * @var bool
     */
    protected $_canVoid                 = true;

    /**
     * @var bool
     */
    protected $_canUseInternal          = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout          = true;

    /**
     * @var bool
     */
    protected $_canSaveCc               = false;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var string
     */
    protected $merchantAccountId       = '';

    /**
     * @var bool
     */
    protected $allowDuplicates         = true;

    /**
     * @var array|null
     */
    protected $requestMaskedFields     = null;

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

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Braintree\Model\Config\Cc $config
     * @param BraintreeTransaction $braintreeTransaction
     * @param BraintreeCreditCard $braintreeCreditCard
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Braintree\Helper\Data $braintreeHelper
     * @param \Magento\Braintree\Helper\Error $errorHelper
     * @param TransactionCollectionFactory $salesTransactionCollectionFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetaData
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
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
        //\Magento\Braintree\Model\Config\Cc $config,
        //Vault $vault,
        //BraintreeTransaction $braintreeTransaction,
        //BraintreeCreditCard $braintreeCreditCard,
        \Magento\Framework\App\RequestInterface $request,
        //\Magento\Braintree\Helper\Data $braintreeHelper,
        //\Magento\Braintree\Helper\Error $errorHelper,
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
        //s$this->config = $config;
        $this->vault = $vault;
        //$this->braintreeTransaction = $braintreeTransaction;
        //$this->braintreeCreditCard = $braintreeCreditCard;
        $this->request = $request;
        //$this->braintreeHelper = $braintreeHelper;
        $this->errorHelper = $errorHelper;
        $this->salesTransactionCollectionFactory = $salesTransactionCollectionFactory;
        $this->productMetaData = $productMetaData;
        $this->regionFactory = $regionFactory;
        $this->orderRepository = $orderRepository;
    }
}