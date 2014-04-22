<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Confirms a Card transaction. The 
 * 
 * @author Kristian Grossman-Madsen
 */
class ConfirmTransaction extends HostedRequest {

    protected $transactionId;
    protected $captureDate;
    
    function __construct($config) {
        $this->method = "confirm";
        parent::__construct($config);
    }
    
    /**
     * Set the transaction id, which must have status AUTHORIZED at Svea. After
     * the request, the transaction will have status CONFIRMED. 
     * 
     * Required.
     * 
     * @param string $transactionId  
     * @return $this
     */
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    /**
     * Set the date that the transaction will be captured (settled).
     * 
     * Required. 
     * 
     * @param string $captureDate  ISO-8601 extended date format (YYYY-MM-DD)
     * @return $this
     */
    function setCaptureDate( $captureDate ) {
        $this->captureDate = $captureDate;
        return $this;
    }
    
    /**
     * validates presence of and prepares elements used in the request to Svea
     */
    public function prepareRequest() {
        $this->validateRequest();
        
        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the confirm request
        $messageContents = array(
            "transactionid" => $this->transactionId,
            "capturedate" => $this->captureDate
        ); 
        $message = $xmlBuilder->getConfirmTransactionXML( $messageContents );        

        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);
        
        // encode the request elements
        $request_fields = array( 
            'merchantid' => urlencode($merchantId),
            'message' => urlencode(base64_encode($message)),
            'mac' => urlencode($mac)
        );
        return $request_fields;
    }

    public function validate($self) {
        $errors = array();
        $errors = $this->validateTransactionId($self, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response."; // TODO check if the createOrder response sets transactionId or SveaOrderId and update error string accordingly
        }
        return $errors;
    }      
    
}