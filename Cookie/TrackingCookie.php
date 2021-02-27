<?php

namespace Lof\Affiliate\Cookie;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Lof\Affiliate\Helper\AffiliateHelper;

/**
 * Class TrackingCookie
 *
 * @package Lof\Affiliate\Cookie
 */
class TrackingCookie
{
    const COOKIE_LIFETIME_MP  = AffiliateHelper::CONFIG_COOKIE_LIFETIME_MP;


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;


    /**
     * TrackingCookie constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface     $scopeConfig
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        ScopeConfigInterface   $scopeConfig,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory  $cookieMetadataFactory
    ) {
        $this->_scopeConfig           = $scopeConfig;
        $this->_cookieManager         = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function get($cookie_name)
    {
        return $this->_cookieManager->getCookie($cookie_name);
    }

    /**
     * @param string $tracking
     *
     * @return void
     */
    public function set($tracking, $cookie_name)
    {
        $lifetime = (int) $this->_scopeConfig->getValue(AffiliateHelper::CONFIG_COOKIE_LIFETIME);
        $lifetimeMin = AffiliateHelper::CONFIG_COOKIE_LIFETIME_MIN;
        $lifetimeMax = AffiliateHelper::CONFIG_COOKIE_LIFETIME_MAX;
        if ( ! is_numeric($lifetime) || $lifetime < $lifetimeMin) {
            $lifetime = $lifetimeMin;
        } elseif ($lifetime > $lifetimeMax) {
            $lifetime = $lifetimeMax;
        }
        $lifetime = (int) $lifetime * self::COOKIE_LIFETIME_MP;

        $metadata = $this
            ->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($lifetime)
            ->setPath('/')
        ;

        $this->_cookieManager->setPublicCookie($cookie_name, $tracking, $metadata);
    }

    /**
     * @return void
     */
    public function delete($cookie_name)
    {
        $this->_cookieManager->deleteCookie(
            $cookie_name,
            $this->_cookieMetadataFactory->createCookieMetadata()
        );
    }
}
