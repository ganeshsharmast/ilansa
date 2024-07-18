<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\BalanceTransaction;
use Stripe\EphemeralKey;
use Stripe\Exception\ApiErrorException;
use Stripe\Issuing\Transaction;
use App\Models\Payment;
use App\Models\User;
use App\Models\Status;
use App\Http\Controllers\OrderController;


class PaymentController extends Controller
{
    public function __construct()
    {
        // Set your secret key. Remember to switch to your live secret key in production!
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    
    public function createPaymentIntent(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
          $data = $req->all(); 
        }
        if(empty($data)){
          $data = $_POST; 
        }
        
        if(!isset($data['order_id']) && empty($data['order_id'])) {
            echo json_encode(['status'=>'Error', 'message'=>'Order-Id is missing.']);
            die; 
        }
        $orderDetails = (new OrderController())->orderProductDetails($req,1);
        if(empty($orderDetails)) {
            echo json_encode(['status'=>'Error', 'message'=>'Order details not found.']);
            die; 
        }
        
        $arr = [
            'amount' => (int)($orderDetails['net_amount']*100), // amount in cents
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ];
    // $paymentIntent = PaymentIntent::all();

    if(1==2 && !empty($paymentIntent) && !empty($paymentIntent->data))
    {
        $paymentIntent = $paymentIntent->data[0];
    } 
    else {
        // Create a PaymentIntent with the order amount and currency
        $paymentIntent = PaymentIntent::create($arr);    
    }

        echo json_encode(['status'=>'success',
            'clientSecret' => $paymentIntent->client_secret
        ]);
        die;
    }
    
    
    public function savePaymentDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data = $req->all(); 
        }
         if(!empty($data)){
             $content = json_encode($data);
             $id = (new Payment())->insertGetId(['content'=>$content]);
             echo json_encode(['status'=>'Success', 'message'=>'Payment details saved successfully.','data'=>$id]);
            die;
         }
             echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
    }    
    
    public function getAllTransactions()
    {
        $transactions = BalanceTransaction::all(['limit' => 100]); // Adjust the limit as needed
        return $transactions;
    }
    
    public function createCustomer($userId)
    {
        $user = (new User())->where(['id'=>$userId])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error', 'message'=>'User details missing.']);
            die;
        }
        $user = $user->toArray();
        
        $customerData = [
            'email' => $user['email'],
            'name' => $user['name'],
        ];

        
        if ($paymentMethodId) {
            $customerData['payment_method'] = $paymentMethodId;
            $customerData['invoice_settings'] = ['default_payment_method' => $paymentMethodId];
        }

        return Customer::create($customerData);
    }
    
    
    public function createEphemeralKey($customerId, $apiVersion)
    {
        try {
            $ephemeralKey = EphemeralKey::create(
                ['customer' => $customerId],
                ['stripe_version' => $apiVersion]
            );

            return $ephemeralKey->secret;
        } catch (ApiErrorException $e) {
            // Handle error
            return null;
        
        }
    }
    
    
    public function getTransactions(Request $req)
    {
       return Charge::all(['limit' => $limit]);
       
    }
    
    public function getTransaction(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
          $data = $req->all(); 
        }
        if(!isset($data['transaction_id']) || empty($data['transaction_id'])) {
            echo json_encode(['status'=>'Error', 'message'=>'Transaction Id is missing.']);
            die; 
        }
        
       try {
            // Retrieve the transaction
            $transaction = BalanceTransaction::retrieve($data['transaction_id']);
            // Transaction::retrieve($data['transaction_id']);
            return response()->json($transaction);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    
    public function getStripeTransaction(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
          $data = $req->all(); 
        }
        if(!isset($data['transaction_id']) || empty($data['transaction_id'])) {
            echo json_encode(['status'=>'Error', 'message'=>'Transaction Id is missing.']);
            die; 
        }

        try {
            // Retrieve the issuing transaction by ID
            $transaction = Transaction::retrieve($data['transaction_id']);
            return response()->json(['status'=>'Success', 'message'=>'Payment details fetched successfully.','data'=>$transaction]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    
}