<?php

namespace Lof\Affiliate\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Lof\Affiliate\Cookie\TrackingCookie;

/**
 * Class AffiliateHelper
 *
 * @package Lof\Affiliate\Helper
 */
class AffiliateHelper extends AbstractHelper implements AffiliateHelperInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Lof\Affiliate\Cookie\TrackingCookie
     */
    protected $_trackingCookie;

    /**
     * @var \Magento\Customer\Model\Session $_customerSession
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session $_checkoutSession
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $_customerRepository
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface $_quoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_salesOrderRepository
     */
    protected $_orderRepository;


    /**
     * AffiliateHelper constructor.
     *
     * @param \Magento\Framework\App\Helper\Context             $context
     * @param \Lof\Affiliate\Cookie\TrackingCookie           $trackingCookie
     * @param \Magento\Customer\Model\Session                   $customerSession
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface        $quoteRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface       $orderRepository
     */
    public function __construct(
        Context                     $context,
        TrackingCookie              $trackingCookie,
        CustomerSession             $customerSession,
        CheckoutSession             $checkoutSession,
        CustomerRepositoryInterface $customerRepository,
        CartRepositoryInterface     $quoteRepository,
        OrderRepositoryInterface    $orderRepository
    ) {
        parent::__construct($context);

        $this->_scopeConfig        = $context->getScopeConfig();
        $this->_trackingCookie     = $trackingCookie;
        $this->_customerSession    = $customerSession;
        $this->_checkoutSession    = $checkoutSession;
        $this->_customerRepository = $customerRepository;
        $this->_quoteRepository    = $quoteRepository;
        $this->_orderRepository    = $orderRepository;
    }

    /**
     * @param string $config
     * @param string $scope
     *
     * @return mixed
     */
    protected function getConfig($config, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($config, $scope);
    }

    /**
     * @return bool
     */
    public function getConfigActive()
    {
        return (bool) $this->getConfig(self::CONFIG_ACTIVE);
    }

    /**
     * @return mixed
     */
    public function getConfigCookieLifetime()
    {
        return $this->getConfig(self::CONFIG_COOKIE_LIFETIME);
    }

    /**
     * @return string|null
     */
    public function getTrackingRequest()
    {
        $tracking = $this->_request->getParam($cookie_name);
        if (1 !== preg_match(self::ATTRIBUTE_TRACKING_REGEX, $tracking)) {
            return null;
        }

        return $tracking;
    }

    /**
     * @return bool
     */
    public function checkTrackingRequest()
    {
        $tracking = $this->getTrackingRequest();
        if (null === $tracking) {
            return false;
        }

        if ($this->getConfigActive()) {
            $this->setTracking($tracking);
        }

        return true;
    }

    /**
     * @param string   $tracking
     */
    public function setTracking($tracking, $cookie_name)
    {
        $this->_trackingCookie->set($tracking, $cookie_name);
    }

    public function getTracking($cookie_name)
    {
        $tracking = $this->_trackingCookie->get($cookie_name);
        return $tracking;
    }
}
