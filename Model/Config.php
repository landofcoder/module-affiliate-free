<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\Affiliate\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const KEY_ENVIRONMENT = 'environment';
    const KEY_MERCHANT_ACCOUNT_ID = 'merchant_account_id';
    const KEY_ALLOW_SPECIFIC = 'allowspecific';
    const KEY_SPECIFIC_COUNTRY = 'specificcountry';
    const KEY_ACTIVE = 'active';
    const KEY_MERCHANT_ID = 'merchant_id';
    const KEY_PUBLIC_KEY = 'public_key';
    const KEY_PRIVATE_KEY = 'private_key';
    const KEY_DEBUG = 'debug';

    /**
     * @var string
     */
    protected $methodCode = 'braintree';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $merchantAccountId = '';

    /**
     * @var string
     */
    protected $clientToken;

    /**
     * @var int
     */
    protected $storeId = null;
    protected $region;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $braintreeSharedConfigFields = [
        'environment' => true,
        'merchant_account_id' => true,
        'merchant_id' => false,
        'public_key' => true,
        'private_key' => false,
    ];


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Braintree\Model\System\Config\Source\Country $sourceCountry
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $region,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->region = $region;
        $this->_objectManager = $objectManager;
    }

}
