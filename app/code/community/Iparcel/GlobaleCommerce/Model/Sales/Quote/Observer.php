<?php
/**
 * Sales_Quote observer class
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Sales_Quote_Observer
{
    /**
     * sales_quote_save_before event handler
     */
    public function before_save($observer)
    {
        $quote = $observer->getQuote();
        /* var $quote Mage_Sales_Model_Quote */
        $_post = Mage::app()->getFrontController()->getRequest()->getPost();
        // adding CPF field to quote
        if (isset($_post['shipping']) && isset($_post['shipping']['cpf'])) {
            $_cpf = $_post['shipping']['cpf'];
        } elseif (isset($_post['billing']) && isset($_post['billing']['cpf'])) {
            $_cpf = $_post['billing']['cpf'];
        } else {
            return;
        }
        $quote->setCpf($_cpf);
    }

    /**
     * sales_quote_save_after event handler
     */
    public function after_save($observer)
    {
        $quote = $observer->getQuote();
        /* var $quote Mage_Sales_Model_Quote */
        // creating CPF for quote
        $cpf = Mage::getModel('ipglobalecommerce/cpf_quote')->loadByQuoteId($quote->getId());
        /* var $cpf Iparcel_GlobaleCommerce_Model_Cpf_Quote */
        if ($quote->getCpf() !== null) {
            $cpf->setQuoteId($quote->getId());
            $cpf->setValue($quote->getCpf());
            $cpf->save();
        } elseif ($cpf->getId()) {
            $cpf->delete();
        }
    }

    /**
     * sales_quote_load_after event handler
     */
    public function after_load($observer)
    {
        $quote = $observer->getQuote();
        /* var $quote Mage_Sales_Model_Quote */
        // appending existing cpf to loaded quote
        $cpf = Mage::getModel('ipglobalecommerce/cpf_quote')->loadByQuoteId($quote->getId());
        /* var $cpf Iparcel_GlobaleCommerce_Model_Cpf_Quote */
        if ($cpf->getId()) {
            $quote->setCpf($cpf->getValue());
        }
    }
}
