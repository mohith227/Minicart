<?php

namespace Mohith\MiniCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    const PRICE_SHIPPING_BAR = 'carriers/freeshipping/free_shipping_subtotal';

    /**

     * Return if maximum price for shipping bar

     * @return int

     */

    public function getPriceForShippingBar()
    {
        return $this->scopeConfig->getValue(
            self::PRICE_SHIPPING_BAR,
            ScopeInterface::SCOPE_STORE
        );

    }

}
