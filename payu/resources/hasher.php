<?php
include('base/payubase.php');

/**
 * Below functions are used to create hashes & to validate hashes
 */

class Hasher
{

function __construct()
{
  $creds = base :: getInstance();
  #$creds->set($key,$salt);
  $Array_var = (array) $creds;
  global $key;
  global $salt;
  $key = $Array_var["key"];
  $salt = $Array_var["salt"];
}

#function generate_hash($txnid, $amount, $productinfo, $firstname, $email, $user_credentials, $udf1, $udf2, $udf3, $udf4, $udf5,$offerKey,$cardBin)
function generate_hash($params)
{
     global $key;
     global $salt;

      // $firstname, $email can be "", i.e empty string if needed. Same should be sent to PayU server (in request params) also.

       $txnid = $params["txnid"];
       $amount = $params["amount"];
       $productinfo = $params["productinfo"];
       $firstname = $params["firstname"];
       $email = $params["email"];
       $udf1 = $params["udf1"];
       $udf2 = $params["udf2"];
       $udf3 = $params["udf3"];
       $udf4 = $params["udf4"];
       $udf5 = $params["udf5"];

       $additional_charge = $params["additional_charge"];
         $payhash_str = $key . '|' . $this->checkNull($txnid) . '|' . $this->checkNull($amount)  . '|' . $this->checkNull($productinfo)  . '|' . $this->checkNull($firstname) . '|' . $this->checkNull($email) . '|' . $this->checkNull($udf1) . '|' . $this->checkNull($udf2) . '|' . $this->checkNull($udf3) . '|' . $this->checkNull($udf4) . '|' . $this->checkNull($udf5) . '||||||' . $salt;

      if ($params["additional_charge"] != '')
      {
        $payhash_str = $additional_charge .'|'. $key . '|' . checkNull($txnid) . '|' .checkNull($amount)  . '|' .checkNull($productinfo)  . '|' . checkNull($firstname) . '|' . checkNull($email) . '|' . checkNull($udf1) . '|' . checkNull($udf2) . '|' . checkNull($udf3) . '|' . checkNull($udf4) . '|' . checkNull($udf5) . '||||||' . $salt;
      }

      $paymentHash = strtolower(hash('sha512', $payhash_str));
      $arr['payment_hash'] = $paymentHash;


    return $paymentHash;
}

/* Response received from Payment Gateway at this page.
It is absolutely mandatory that the hash (or checksum) is computed again after you receive response from PayU and compare it with request and post back parameters. This will protect you from any tampering by the user and help in ensuring a safe and secure transaction experience. It is mandate that you secure your integration with PayU by implementing Verify webservice and Webhook/callback as a secondary confirmation of transaction response.

Hash string without Additional Charges -
hash = sha512(SALT|status||||||udf5|||||email|firstname|productinfo|amount|txnid|key)

With additional charges - 
hash = sha512(additionalCharges|SALT|status||||||udf5|||||email|firstname|productinfo|amount|txnid|key)

*/
function validate_hash($params)
{
      global $key;
      global $salt;

      // $firstname, $email can be "", i.e empty string if needed. Same should be sent to PayU server (in request params) also.
      $txnid = $params["txnid"];
      $amount = $params["amount"];
      $productinfo = $params["productinfo"];
      $firstname = $params["firstname"];
      $email = $params["email"];
      $status = $params['status'];
      $udf1 = $params["udf1"];
      $udf2 = $params["udf2"];
      $udf3 = $params["udf3"];
      $udf4 = $params["udf4"];
      $udf5 = $params["udf5"];
      $additional_charge = $params["additional_charge"];
      $payhash_str = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;

      if ($params["additional_charge"] != '')
      {
        $payhash_str = $additional_charge.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;

      }
      // echo $additional_charge;
      $reverse_hash = strtolower(hash('sha512', $payhash_str));
      $arr['payment_hash'] = $paymentHash;
    return $reverse_hash;
}

function checkNull($value) {
            if ($value == null) {
                  return '';
            } else {
                  return $value;
            }
          }
}


?>
