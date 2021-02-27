<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\Affiliate\Block\Account\Form;
/**
 * Customer edit form block
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */

class ListPayments extends \Magento\Framework\View\Element\Template
{
    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Affiliate\Helper\Data $affiliateHelper,
        array $data = []
        ) {
        $this->_helper = $affiliateHelper;
        parent::__construct($context, $data);
    }

    public function getDefaultPayments() {
        $payments = [];
        $payments["paypal"] = ['name'=>'paypal_email', 
                                'title'=>'Paypal',
                                'label'=>'PayPal Email Account',
                                'enabled'=>true,
                                'validate'=>"{required:false, 'validate-email':true}",
                                'autocomplete'=>'off',
                                'checked'=>true,
                                'style'=>'',
                                'html' => ''
                                ];

        $payments["skrill"] = ['name'=>'skrill_email',
                                'title'=>'Skrill',
                                'label'=>'Skrill Email Account',
                                'enabled'=>true,
                                'validate'=>"{required:false, 'validate-email':true}",
                                'autocomplete'=>'off',
                                'style'=>'',
                                'html'=> ''
                                ];

        $payments["paypal"]['html'] = $this->_helper->buildPaymentField($payments['paypal']);
        $payments["skrill"]['html'] = $this->_helper->buildPaymentField($payments['skrill']);      
        return $payments;
    }

    public function getPayments() {
        $payments = array();
        //Get list payments at here
        
        return $payments;
    }
}
