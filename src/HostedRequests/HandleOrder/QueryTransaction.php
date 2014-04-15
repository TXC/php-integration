<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Query a Card or Directbank transaction. Only supports querytransactionid request
 * 
 * @author Kristian Grossman-Madsen
 */
class QueryTransaction extends HostedRequest {

    protected $transactionId;
    
    function __construct($config) {
        $this->method = "querytransactionid";
        parent::__construct($config);
    }

    /**
     * @param string $transactionId
     * @return $this
     */
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    /**
     * prepares the elements used in the request to svea
     */
    public function prepareRequest() {

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);      // TODO HOSTED_ADMIN_TYPE?!
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the credit request
        $messageContents = array(
            "transactionid" => $this->transactionId
        ); 
        $message = $xmlBuilder->getQueryTransactionXML( $messageContents );        
        
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
}