<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea WebService GetAddresses request response. 
 * (Note that this maps to the legacy Web Service, not the Europe Web Service GetAddresses request.)
 *
 * For attribute descriptions, see the formatObject() method documentation
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class GetAddressesResponse extends WebServiceResponse{

    /** @var string $resultcode */
    public $resultcode;
    
    /** @var GetAddressIdentity [] array of GetAddressIdentity */
    public $customerIdentity = array();
    
    /**
     *  formatObject sets the following attributes:
     * 
     *  $response->accepted                 // true iff request was accepted by the service 
     *  $response->errormessage             // may be set if accepted above is false
     *
     *  $response->resultcode               // one of {Error, Accepted, NoSuchEntity}
     * 
     *  $response->$customerIdentity[0..n] // array of Svea\GetAddressIdentity
     */
    protected function formatObject($message) {
        
        // was request accepted?
        if( $message->GetAddressesResult->RejectionCode == "Error" ) {
            $this->accepted = 0;
        }
        else {
            $this->accepted = $message->GetAddressesResult->Accepted;
        }
        $this->errormessage = isset($message->GetAddressesResult->ErrorMessage) ? $message->GetAddressesResult->ErrorMessage : "";        

        // set response resultcode
        $this->resultcode = $message->GetAddressesResult->RejectionCode;

        // set response attributes
        if (property_exists($message->GetAddressesResult, "Addresses") && $this->accepted == 1) {
            $this->formatCustomerIdentity($message->GetAddressesResult->Addresses);
        }
    }

    public function formatCustomerIdentity($customers) {

        is_array($customers->CustomerAddress) ? $loopValue = $customers->CustomerAddress : $loopValue = $customers;
        
        foreach ($loopValue as $customer) {
            $temp = new GetAddressIdentity( $customer );
            
            array_push($this->customerIdentity, $temp);
        }
    }
}