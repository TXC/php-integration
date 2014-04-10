<?php
namespace Svea;

require_once 'HostedAdminResponse.php'; // fix for class loader sequencing problem
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CreditTransactionResponse handles the credit transaction response
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditTransactionResponse extends HostedAdminResponse{

    /** string $customerrefno */
    public $customerrefno;

    function __construct($message,$countryCode,$config) {
        parent::__construct($message,$countryCode,$config);
    }

    /**
     * formatXml() parses the credit transaction response xml into an object, and
     * then sets the response attributes accordingly.
     * 
     * @param string $hostedAdminResponseXML  hostedAdminResponse as xml
     */
    protected function formatXml($hostedAdminResponseXML) {
                
        $hostedAdminResponse = new \SimpleXMLElement($hostedAdminResponseXML);
        
        if ((string)$hostedAdminResponse->statuscode == '0') {
            $this->accepted = 1;
            $this->resultcode = '0';
        } else {
            $this->accepted = 0;
            $this->setErrorParams( (string)$hostedAdminResponse->statuscode ); 
        }

        $this->customerrefno = (string)$hostedAdminResponse->transaction->customerrefno;
    }
}
