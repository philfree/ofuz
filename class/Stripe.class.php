<?php
/**
  * Custom class for Stripe.com Payment Gateway
  * 
  * The class will process the the payment request to the Stirpe Payment Gateway
  * 
  * @author SQLFusion's Dream Team <info@sqlfusion.com>
*/
class StripeGateWay extends BaseObject
{
	/* Stripe API Key*/
	private $stripe_api_key; 
	
	/**
      * Constructor for setting the stipe key
    */
    public function __construct($test = false,$stripe_api_key){ 
		$this->stripe_api_key = $stripe_api_key;
    }

    /**
      * Public function to set the API key
      * @param string mode 
    */
    public function setAPIKey(){
		$this->stripe_api_key = Stripe::setApiKey($this->stripe_api_key);
	}

       
    /**
      * Function to charge new customer on Stripe Sever
      * @param array $token
      * @param integer $name
      * @param string $user_id
      * @param mode Test or live mode
    */
    public function CreateCustomer($token,$name,$amount,$email="",$description=""){
		$this->setAPIKey();	
		$customer = Stripe_Customer::create(array("card" => $token,"description" => $email));
        $input = array("amount"=>$amount,"customer"=>$customer->id,"currency"=>"USD","description"=>$description);
        $result = Stripe_Charge::create($input);
        
		if($result['paid'] === true){
			$result_array = array("success"=>"1","customer_id"=>$customer->id);	
			return $result_array;
		} else {
			return $result;
		}
    }
    
    /**
     * Function to charge the customer already existed with stripe token id
     * @param Tokne id users strip token id 
     * @param user id 
     * @param amount to charge
     * @param description 
    */
    public function ChargeExsistingCustomer($customerId,$amount){
		$this->setAPIKey();
		$result = Stripe_Charge::create(array("amount" => "$amount","currency" => "usd","customer" => "$customerId"));
		if($result['paid'] === true){
			$result_array = array("success"=>"1");
			return $result_array;
		} else {
			return $result;
		}
	}
	
	
	/**
     * Function to update the customerinformation with customer id
     * Cases when card expired or new card. 
     * @param Tokne id users strip token id 
     * @param user id 
     * @param amount to charge
     * @param description 
    */
    public function UpdateExistingCustomer($customerId,$token,$name,$amount,$email="",$description=""){
		$this->setAPIKey();
		$cu = Stripe_Customer::retrieve($customerId);
		if(!empty($description)){
		$cu->description = $description;
		}
		$cu->card = $token; 
		$cu->save();
		$result = Stripe_Charge::create(array("amount" => "$amount","currency" => "usd","customer" => "$customerId"));
		if($result['paid'] === true){
			$result_array = array("success"=>"1");
			return $result_array;
		} else {
			return $result;
		}
	}
}
?>
