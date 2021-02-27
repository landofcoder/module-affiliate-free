<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
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

    public function formatPrice($price){
    	$formatPrice = $this->priceFormatter->format(
    		$price,
    		false,
    		null,
    		null,
    		null
    		);
    	return $formatPrice;
    }	
}