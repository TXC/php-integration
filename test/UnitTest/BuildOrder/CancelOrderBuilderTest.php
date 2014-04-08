<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $cancelOrderObject;
    
    function setUp() {
        $this->cancelOrderObject = \WebPay::cancelOrder(SveaConfig::getDefaultConfig());  
    }
    
    public function test_CancelOrderBuilder_class_exists() {
        $config = SveaConfig::getDefaultConfig();
        $this->cancelOrderObject = \WebPay::cancelOrder($config);        
        
        $this->assertInstanceOf("Svea\CancelOrderBuilder", $this->cancelOrderObject);
    }
    
    public function test_CancelOrderBuilder_setOrderId() {
        $orderId = "123456";
        
        $this->cancelOrderObject->setOrderId($orderId);
        
        $this->assertEquals($orderId, $this->cancelOrderObject->orderId);        
    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_INVOICE_returns_CloseOrder_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::INVOICE;
        
        $closeOrderObject = $this->cancelOrderObject->setOrderId($orderId)->usePaymentMethod($paymentMethod);
        
        $this->assertInstanceOf("Svea\CloseOrder", $closeOrderObject);
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $closeOrderObject->orderBuilder->orderType);

    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_PAYMENTPLAN_returns_CloseOrder_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::PAYMENTPLAN;
        
        $closeOrderObject = $this->cancelOrderObject->setOrderId($orderId)->usePaymentMethod($paymentMethod);
        
        $this->assertInstanceOf("Svea\CloseOrder", $closeOrderObject);
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $closeOrderObject->orderBuilder->orderType);
    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_KORTCERT_returns_CloseOrder() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::KORTCERT;
        
        $annulTransactionObject = $this->cancelOrderObject->setOrderId($orderId)->usePaymentMethod($paymentMethod);
        
        $this->assertInstanceOf("Svea\AnnulTransaction", $annulTransactionObject);
    }
    
}
