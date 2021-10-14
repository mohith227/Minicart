<?php

namespace Mohith\MiniCart\Block\Cart;

use Mohith\MiniCart\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\SessionFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Json;

class Sidebar extends Template
{

    /**
     * @var Data
     */

    private $helper;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var array
     */
    protected $jsLayout;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    protected $localeCurrency;
    /**
     * @var SessionFactory
     */
    protected $checkoutSession;

    /**
     * @param Template\Context $context
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CurrencyInterface $localeCurrency
     * @param SessionFactory $checkoutSession
     * @param array $data
     */

    public function __construct(
        Template\Context      $context,
        Data                  $helper,
        ScopeConfigInterface  $scopeConfig,
        StoreManagerInterface $storeManager,
        CurrencyInterface     $localeCurrency,
        SessionFactory        $checkoutSession,
        array                 $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->localecurrency = $localeCurrency;
        $this->helper = $helper;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return float|mixed|void
     */
    public function getRemainingCartTotal()
    {
        $freeShippingSubtotal = $this->getFreeShippingSubtotal();
        $subTotal = $this->getSubtotal();
        if (isset($freeShippingSubtotal) && $freeShippingSubtotal > $subTotal) {
            return $freeShippingSubtotal - $subTotal;
        }
    }

    /**
     * @return mixed
     */
    public function getFreeShippingSubtotal()
    {
        return $this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal',
            ScopeInterface::SCOPE_STORE);

    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->getActiveQuoteAddress()->getBaseSubtotal(); // or any other type of subtotal like subtotal incl tax etc.
    }

    /**
     * @return Address
     */
    protected function getActiveQuoteAddress()
    {
        /** @var Quote $quote */
        $quote = $this->checkoutSession->create()->getQuote();
        if ($quote->isVirtual()) {
            return $quote->getBillingAddress();
        }
        return $quote->getShippingAddress();
    }

    /**
     * @return string
     */

    public function getJsLayout()
    {
        return Zend_Json::encode($this
            ->jsLayout);

    }

    public function getFreeShippingStatus()
    {
        return $this->scopeConfig->getValue('carriers/freeshipping/active',
            ScopeInterface::SCOPE_STORE);

    }


    public function getStoreCurrency()
    {
        $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();

    }

    /**
     * Get current store currency code
     *
     * @return string
     */

    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }


    public function getConfigForShippingBar()
    {
        return $this->helper->getPriceForShippingBar();
    }

}
