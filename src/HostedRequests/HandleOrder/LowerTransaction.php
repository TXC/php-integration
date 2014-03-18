<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Lowers the amount of a Card transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class LowerTransaction {

    private $config;
    private $countryCode;

    private $transactionId;
    private $amountToLower;
    
    function __construct($config) {
        $this->config = $config;
    }
    
    function setCountryCode( $countryCode ) {
        $this->countryCode = $countryCode;
        return $this;
    }

    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    function setAmountToLower( $transactionId ) {
        $this->amountToLower = $transactionId;
        return $this;
    }
    
    /**
     * prepares the elements used in the request to svea
     */
    public function prepareRequest() {

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the confirm request
        $messageContents = array(
            "transactionid" => $this->transactionId,
            "amounttolower" => $this->amountToLower
        ); 
        $message = $xmlBuilder->getLowerTransactionXML( $messageContents );     // TODO inject method into HostedXMLBuilder instead

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

    /**
     * Do request using cURL
     * 
     * tested via LowerTransactionIntegrationTest
     * 
     * @return HostedAdminResponse
     */
    public function doRequest(){
        $fields = $this->prepareRequest();
        
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint(SveaConfigurationProvider::HOSTED_ADMIN_TYPE). "loweramount");
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        $responseXML = curl_exec($ch);
        curl_close($ch);
        
        // create SveaResponse to handle confirm response
        $responseObj = new \SimpleXMLElement($responseXML);        
        $sveaResponse = new \SveaResponse($responseObj, $this->countryCode, $this->config);

        return $sveaResponse->response; 
    }
}