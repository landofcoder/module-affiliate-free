<?php

namespace Lof\Affiliate\Helper;

/**
 * Interface AffiliateHelperInterface
 *
 * @package Lof\Affiliate\Helper
 */
interface AffiliateHelperInterface
{
    const ATTRIBUTE_TRACKING_REGEX = '/^[0-9a-zA-Z_-]{1,35}$/';

    /**
     * Configuration
     */
    const CONFIG_ACTIVE = 'lofaffiliate/general_settings/enable';
    const CONFIG_COOKIE_LIFETIME = 'lofaffiliate/general_settings/cookie_lifetime';
    const CONFIG_COOKIE_LIFETIME_MIN = 4;
    const CONFIG_COOKIE_LIFETIME_MAX = 6240;
    const CONFIG_COOKIE_LIFETIME_MP = 604800;

    /**
     * @return bool
     */
    public function getConfigActive();

    /**
     * @return mixed
     */
    public function getConfigCookieLifetime();

    /**
     * @return string|null
     */
    public function getTrackingRequest();

    /**
     * @return bool
     */
    public function checkTrackingRequest();

    public function setTracking($tracking, $cookie_name);

    public function getTracking($cookie_name);
}
