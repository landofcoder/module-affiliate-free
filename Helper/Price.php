<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Affiliate\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Price extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        PriceCurrencyInterface $priceFormatter
    ) {
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context);
    }

    /**
     * format price
     *
     * @param float|int $string $price
     * @return string
     */
    public function formatPrice($price)
    {
        $formatPrice = $this->priceFormatter->format(
            $price,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            null
        );
        return $formatPrice;
    }
}
