<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Block\Adminhtml\AccountAffiliate\Edit\Tab\Form;

class Customer extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    public function getElementHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customers = $objectManager->create('Lof\Affiliate\Model\AccountAffiliate');
        $customers = $customers->getAllCustomers();

        $last = count($customers);
        $index = 0;

        $script = 'var source = [';
            foreach ($customers as $customer) { 
                $index++;
                if($index == $last){
                    $script .= '{ "label": "'.$customer['label'].'", "value": "'.$customer['value'].'" }';
                } else {
                    $script .= '{ "label": "'.$customer['label'].'", "value": "'.$customer['value'].'" }, ';
                }

            }
        $script .= '];';

        $html = '';
        $elementId = $this->getHtmlId();
       

        $html .= '<input type="text" id="text-'.$elementId.'" name="customer_name" value="" />';
        $html .= '<input type="hidden" id="hidden-'.$elementId.'" name="customer_id" value="" />';
        $html .= '<div id="list-accountaffiliate_customer"></div>';

        $html .= "<script>".$script."

        require([ 'jquery', 'jquery/ui' ], function($){
                $( '#text-accountaffiliate_customer' ).autocomplete({
                    source: source,
                    minLength: 0,
                    appendTo: '#list-accountaffiliate_customer',
                    select: function(event, ui) {
                        event.preventDefault();
                        $('#accountaffiliate_email').val(ui.item.label);
                        $('#text-".$elementId."').val(ui.item.label);

                        $('#hidden-".$elementId."').val(ui.item.value);
                    },
                });
            });

        </script>";
        
        return $html;
    }
}