<?php
namespace Svea;

require_once 'OrderBuilder.php'; 
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CreateOrderBuilder collects and prepares order data to be sent using one of Svea's payment methods.
 * 
 * Set all required order attributes in a CreateOrderBuilder instance by using the 
 * OrderBuilder setAttribute() methods. Instance methods can be chained together, as 
 * they return the instance itself in a fluent manner.
 * 
 * Finish setting order attributes by chosing a payment method using one of the
 * usePaymentMethod() methods below. 
 * 
 * You can then go on specifying any payment method specific settings, using methods provided by the 
 * returned payment request class.
 *
 * The Invoice and Payment plan payment methods will perform a synchronous payment request and immediately return a response.  
 * The Card, Direct bank, and hosted methods via PayPage are asynchronous. These will return an html form containing a 
 * formatted message to send to Svea, which in turn sends a response to a given return url.
 * 
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderBuilder extends OrderBuilder {

    /**
     * Use useInvoicePayment to initiate an invoice payment.
     * 
     * Set additional attributes using InvoicePayment methods.
     * 
     * @return WebService\InvoicePayment
     */
    public function useInvoicePayment() {
        return new WebService\InvoicePayment($this);
    }

    /**
     * Use usePaymentPlanPayment to initate a payment plan payment. 
     * 
     * You can use WebPay::getPaymentPlanParams() to get available campaign codes (payment plans).
     * 
     * Set additional attributes using PaymentPlanPayment methods.
     * 
     * @see \WebPay::getPaymentPlanParams() WebPay::getPaymentPlanParams()
     * 
     * @param string $campaignCodeAsString  
     * @param boolean $sendAutomaticGiroPaymentFormAsBool  (Optional)
     * @return WebService\PaymentPlanPayment
     */
    public function usePaymentPlanPayment($campaignCodeAsString, $sendAutomaticGiroPaymentFormAsBool = 0) {
        $this->campaignCode = $campaignCodeAsString;
        $this->sendAutomaticGiroPaymentForm = $sendAutomaticGiroPaymentFormAsBool;
        return new WebService\PaymentPlanPayment($this);
    }
    
    /**
     * Use usePaymentMethod to initate a payment bypassing the PayPage completely, going straight to the payment method specified. 
     * This is the preferred way to perform a payment, as it cuts down on the number of payment steps in the end user checkout flow.
     * 
     * You can use WebPay::getPaymentMethods() to get available payment methods. See also the PaymentMethod class constants.
     * 
     * Set additional attributes using PaymentMethodPayment methods.
     * 
     * @see \WebPay::getPaymentMethods() WebPay::getPaymentMethods()
     * @see \PaymentMethod PaymentMethod
     * 
     * @param string $paymentMethodAsConst  i.e. PaymentMethod::SEB_SE et al
     * @return HostedService\PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst) {
        return new HostedService\PaymentMethodPayment($this, $paymentMethodAsConst);
    }
  
    /**
     * Use usePayPageCardOnly to initate a card payment via PayPage, showing only the available card payment methods. 
     * 
     * Set additional attributes using CardPayment methods.
     * @return HostedService\CardPayment
     */
    public function usePayPageCardOnly() {
        return new HostedService\CardPayment($this);
    }

    /**
     * Use usePayPageDirectBankOnly to initate a direct bank payment via PayPage, showing only the available direct bank payment methods. 
     * 
     * Set additional attributes using DirectPayment methods.
     * @return HostedService\DirectPayment
     */
    public function usePayPageDirectBankOnly() {
        return new HostedService\DirectPayment($this);
    }

    /**
     * Use usePayPage to initate a payment via PayPage, showing all available payment methods to the user. 
     * 
     * Set additional attributes using PayPagePayment methods.
     * @return HostedService\PayPagePayment
     */
    public function usePayPage() {
        $paypagepayment = new HostedService\PayPagePayment($this);
        return $paypagepayment;
    }

    /**  
     * @param \ConfigurationProvider $config 
     */
    public function __construct($config) {
        parent::__construct($config);
    }

   /**
     * @internal for testfunctions
     * @param type $func
     * @return $this
     */
    public function run($func) {
        $func($this);
        return $this;
    }
}
