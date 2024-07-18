<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Products;
use App\Models\User;
use App\Models\Orders;
use App\Models\OrderRequest;
use App\Models\OrderProducts;
use App\Models\CartProducts;
use App\Models\SubServices;
use App\Models\ServiceProducts;
use App\Http\Helpers\Helper;

class CartController extends Controller
{
    public function __construct()
    {
        //die("-=-="); 
    }

    public function addCartProducts(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $products = $Data['product'];
        if(!is_array($products)){
            $products = json_decode($products,true);    
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        if(!isset($Data['user_id']) || empty($Data['user_id'])){
            echo json_encode(['status'=>'Error','message'=>'Either user_id not set or empty','data'=>$Data]);
            die;
        }
        
        $user_id = $Data['user_id'];
        $cartDetails=$this->getUserActiveCartDetails($user_id);
       
        if(!empty($cartDetails)){

            $cart_id=$cartDetails['id'];
            } 
            else {
            $cartId = (new Cart())->insertGetId(['user_id'=>$user_id,'status'=>1,'created_at'=>date('Y-m-d h:i:s')]);
            
            $cart_id=$cartId;
            }
            $this->insertCartProducts($cart_id,$products);
            $this->getUserCartDetails($req);
        
    }
    
        public function updateCartProducts(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $products = $Data['product'];
        if(!is_array($products)){
            $products = json_decode($products,true);    
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails=$this->getUserActiveCartDetails($user_id);
        $cart_id=$cartDetails['id'];
        foreach($products as $product){
            $productId = $product['id'];
            $qty = $product['quantity'];
            $content='';
            if(isset($product['content'])){
                $content=$product['content'];
            }
            DB::table('cart_products')->where(['cart_id'=>$cart_id,'product_id'=>$productId])->update(['quantity'=>$qty,'content'=>$content]);
        } 
            $this->getUserCartDetails($req);
        
    }
    
    public function updateCartSchedule(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(!isset($Data['user_id']) || empty($Data['user_id'])){
            echo json_encode(['status'=>'Error','message'=>'Either User-Id not set or empty.','data'=>$Data]);
            die;
        }
        if(!isset($Data['service_schedule_start']) || empty($Data['service_schedule_start'])){
            echo json_encode(['status'=>'Error','message'=>'Either Service Schedule start not set or empty.','data'=>$Data]);
            die;
        }
        if(!isset($Data['service_schedule_end']) || empty($Data['service_schedule_end'])){
            echo json_encode(['status'=>'Error','message'=>'Either Service Schedule end not set or empty.','data'=>$Data]);
            die;
        }
        $user_id = str_replace("'","",str_replace('"',"",$Data['user_id']));
        $service_schedule_start = str_replace("'","",str_replace('"',"",$Data['service_schedule_start']));
        $service_schedule_end = str_replace("'","",str_replace('"',"",$Data['service_schedule_end']));
        $cartDetails=$this->getUserActiveCartDetails($user_id);
        $cart_id=$cartDetails['id'];
        
           if(DB::table('carts')->where(['user_id'=>$user_id])->update(['service_schedule_start'=>$service_schedule_start,
                'service_schedule_end'=>$service_schedule_end]))
                {
               echo json_encode(['status'=>'Success','message'=>'Service Schedule updated successfully..','data'=>$Data]);
            die;
           }
           echo json_encode(['status'=>'Error','message'=>'Something missing.','data'=>$Data]);
            die;
    }
    
    public function insertCartProducts($cart_id,$products)
    {
        foreach($products as $product){
            $productId = $product['id'];
            $cart_product = DB::table('cart_products')->where(['cart_id'=>$cart_id,'product_id'=>$productId])->first();
            $qty = $product['quantity'];
            $content='';
            if(isset($product['content'])){
                $content=$product['content'];
            }
            if(!empty($cart_product)){
                $qty = $cart_product->quantity;
                DB::table('cart_products')->where(['cart_id'=>$cart_id,'product_id'=>$productId])->update(['quantity'=>$qty,'content'=>$content]);
            } 
            else {
                $arrData = ['cart_id'=>$cart_id,'product_id'=>$productId,'quantity'=>$qty,'status'=>1,'created_at'=>date('Y-m-d h:i:s'),'content'=>$content];
                DB::table('cart_products')->insert($arrData);
               }
            }
    }
    
    public function getUserActiveCartDetails($user_id)
    {
        $cart=Cart::with('cartProducts','coupons')->select('carts.id','carts.user_id','carts.coupon_id','carts.status')
        ->where(['carts.user_id'=>$user_id])->first();
        if(!empty($cart)){
            $cart=$cart->toArray();
        }
        return json_decode(json_encode($cart),true);
    }
    
     public function getCartProductList($cart_id)
    {
        $products = DB::table('cart_products as cp')
        ->leftJoin('products as p','p.id','cp.product_id')
        ->where(['cp.cart_id'=>$cart_id,'cp.status'=>1,'p.status'=>1])
        ->OrderBy('cp.product_id')->get()->toArray();
        $products = json_decode(json_encode($products),true);
        
        return $products;
    }
    
         public function getUserCartProductList($user_id, $productArr=0)
    {
        $products = DB::table('carts as c')
        ->leftJoin('cart_products as cp','cp.cart_id','c.id')
        ->where(['c.user_id'=>$user_id,'cp.status'=>1])
        ->get()->toArray();
        $products = json_decode(json_encode($products),true);
        if($productArr==1 && $products)
        {
            $Products=[];
            foreach($products as $product)
            {
                $Products[$product['product_id']] = $product['quantity'];
            }
            return $Products;
        }
        return $products;
    }
    
    /**
     * Show the profile for a given user.
     */
    public function getUserCartDetails(Request $req, $withoutJson=0)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = Cart::with('cartProducts','coupons.CouponType')->where(['carts.user_id'=>$user_id])->first();
        $cart = $products = [];
        $product_count = $discount =  $net_amount = 0;
        $tax = $amount = [];
        if(!empty($cartDetails)){
            $cartDetails = $cartDetails->toArray();
            $serviceId=[];
            foreach($cartDetails['cart_products'] as $product){
                $productId=$product['product_id'];
                $Product = ServiceProducts::select('*','s.id as service_id','ss.service_id','s.name as service_name','ss.name as sub_service_name','ss.tax_percent','service_products.id','p.product_name',
                'product_image','service_products.price as product_price')
                ->leftJoin('sub_services as ss','ss.id','service_products.sub_service_id')
                ->leftJoin('services as s','s.id','ss.service_id','ss.name as sub_service_name')
                ->leftJoin('products as p','p.id','service_products.product_id')
                    ->where(['service_products.id'=>$productId])->first();
            if(!empty($Product)){
                $Product = $Product->toArray();
                $qty=$product['quantity'];
                $Product=array_merge($Product,['quantity'=>$qty,'content'=>$product['content']]);
                $amt=$qty*$Product['price'];
                $amount[]=$amt;
                $tax[]=$amt*$Product['tax_percent']*.01;
                $serviceId['services'][$Product['service_id']]['name']=$Product['service_name'];
           
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['sub_service_name']=$Product['sub_service_name'];
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['product'][] = $Product;
            $product_count++;
                
            }        
            
            }
            unset($cartDetails['cart_products']);
            
            if(!empty($serviceId)){
                $rec=[];
                foreach($serviceId['services'] as $id=>$service){
                    $subser=[];
                    foreach($service['sub_services'] as $k=>$ser){
                        $subser[]=array_merge(['id'=>$k],$ser);
                    }
                    $service['sub_services']=$subser;
                    $service=array_merge(['id'=>$id],$service);
                    $rec[]=$service;
            }
            if(!empty($coupon=$cartDetails['coupons'])){
                $type_val=$coupon['coupon_type']['type_value'];
                if($type_val=='%'){
                    $discount = array_sum($amount)*$coupon['value']*.01;
                }else {
                    $discount=number_format($coupon['value'],2);    
                }
                
            }
            $net_amount = array_sum($amount) - $discount + array_sum($tax);
            }
            $cartDetails=array_merge($cartDetails,['services'=>$rec,'product_count'=>$product_count,'tax'=>array_sum($tax),'amount'=>array_sum($amount),'discount'=>$discount,'net_amount'=>$net_amount]);
            $cart=$cartDetails;
        }
        if($withoutJson){
            return $cart;
        }
        echo json_encode(['status'=>'Success','data'=>$cart]);
            die;
    }    
    
        /**
     * Show the profile for a given user.
     */
    public function getUserCartDetail(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = $this->getUserActiveCartDetails($user_id);
        $cart = [];
        if(!empty($cartDetails)){
            $cart=$cartDetails;
            $cart_id=$cartDetails['id'];
            $cartProducts= $this->getCartProductList($cart_id);
            foreach($cartProducts as $product){
                $data=DB::table('services as s')
                ->leftjoin('sub_services as ss','ss.service_id','s.id')
                ->leftjoin('service_products as sp','sp.sub_service_id','ss.id')
                ->where(['sp.product_id'=>$product['id']])
                ->get()->toArray();
                print_r($data);
                die;
            }
            $cart['product']=$cartProducts;
            
        }
        echo json_encode(['status'=>'Success','data'=>$cart]);
            die;
    }  
    
        public function removeCartProducts(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = $this->getUserActiveCartDetails($user_id);
        $message = 'Product removed successfully';
        if(!empty($cartDetails)){
            $cart_id=$cartDetails['id'];
            DB::table('cart_products')->where(['cart_id'=>$cart_id,'product_id'=>$Data['product_id']])->delete();
        
            $this->getUserCartDetails($req);
        }
        
        echo json_encode(['status'=>'Success','data'=>$data]);
            die;
    }
    
    public function emptyCart(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = $this->getUserActiveCartDetails($user_id);
        $message = 'Cart is empty now';
        if(!empty($cartDetails)){
            $cart_id=$cartDetails['id'];
            DB::table('cart_products')->where(['cart_id'=>$cart_id])->delete();
            DB::table('carts')->where(['id'=>$cart_id])->delete();
        }
        echo json_encode(['status'=>'Success','message'=>$message]);
            die;
    }
    
     /*
    Coupon Section.
    */
        public function getCoupons()
    {
        $coupons= DB::table('coupons as c')->where(['c.status'=>1,'ct.status'=>1])
            ->leftJoin('coupon_types as ct','ct.id','c.coupon_type')
            ->select('c.*','ct.type_name','ct.type_value')->get()->toArray();

        if(!empty($coupons)){
            $dat = json_decode(json_encode($coupons),true);
            $status  = 'Success';
            $message = 'Coupon list fetched successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Error';
          $message = 'Something missing.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }
    
        public function verifyCoupon(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $coupon= DB::table('coupons as c')->where(['c.code'=>$data['coupon_code'],'c.status'=>1,'ct.status'=>1])
            ->leftJoin('coupon_types as ct','ct.id','c.coupon_type')
            ->select('c.*','ct.type_name','ct.type_value')->first();
        
        if(!empty($coupon)){
            $dat = json_decode(json_encode($coupon),true);
            $status  = 'Success';
            $message = 'Coupons fetched successfully.';
            if(isset($data['user_id']) && !empty($data['user_id'])){
                $res=DB::table('carts')->where(['user_id'=>$data['user_id']])->update(['coupon_id'=>$dat['id']]); 
            } else {
                $dat = [];
                $message="User-id is missing.";
            }
        } else 
        {
          $dat=[]; 
          $status = 'Error';
          $message = 'Coupon code not exist.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }

        public function proceedCartRequest(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = Cart::with('cartProducts','coupons.CouponType')->where(['user_id'=>$user_id,'status'=>1])->first();
        if(empty($cartDetails)){
            echo json_encode(['status'=>'Error', 'message'=>'No Active Cart found with this request.','data'=>[]]);
            die;
        }
        $cartDetails = $cartDetails->toArray();
        $cart_id = $cartDetails['id'];
        $service_schedule_start = str_replace("'","",str_replace('"',"",@$Data['service_schedule_start']));
        if(empty($service_schedule_start)){
            $service_schedule_start = date('Y-m-d h:i:s');
        } else {
            $service_schedule_start = date('Y-m-d h:i:s',strtotime($service_schedule_start));
        }
        $service_schedule_end = str_replace("'","",str_replace('"',"",@$Data['service_schedule_end']));
        if(empty($service_schedule_end)){
            $service_schedule_end = date('Y-m-d h:i:s');
        } else {
            $service_schedule_end = date('Y-m-d h:i:s',strtotime($service_schedule_end));
        }
        $OrderId = (new Orders())->insertGetId([
            'user_id'=>$user_id,
            'coupon_id'=>$cartDetails['coupon_id'],
            'address'=>$cartDetails['address'],
            'longitude'=>$cartDetails['longitude'],
            'latitude'=>$cartDetails['latitude'],
            'user_acceptance'=>1,
            'service_schedule_start'=>$service_schedule_start,
            'service_schedule_end'=>$service_schedule_end,
            'status'=>1,
            'created_at'=>date('Y-m-d h:i:s')
            ]);
        
        $providers = User::where(['status'=>1,'role'=>2,'availability'=>1])->get()->toArray();
        if(empty($cartDetails['cart_products'])){
             echo json_encode(['status'=>'Error', 'message'=>'No Cart Products found with this request.','data'=>[]]);
            die;
        }
        else {
            $products = [];
            foreach($cartDetails['cart_products'] as $product){
                $products[] = ['order_id'=>$OrderId,'product_id'=>$product['product_id'],'quantity'=>$product['quantity'],'status'=>1,'created_at'=>date('Y-m-d h:i:s')];
            }
            if(!empty($products)){
                (new OrderProducts())->insert($products);
            }
        }
        if(!empty($providers)){
            $records = [];
            foreach($providers as $provider){
                $records[] = ['order_id'=>$OrderId,'provider_id'=>$provider['id'],'status'=>1,'created_at'=>date('Y-m-d h:i:s')];
            }
            if(!empty($records))
            {
                if((new OrderRequest())->insert($records))
                {
                    if(DB::table('cart_products')->where(['cart_id'=>$cart_id])->delete()){
                        DB::table('carts')->where(['carts.id'=>$cart_id])->delete();
                    }
                }
            }
        }
        else {
             echo json_encode(['status'=>'Error', 'message'=>'No Provider found with this request.','data'=>[]]);
            die;
        }
        
        $orderDetails = Orders::with('orderProducts','coupons.CouponType')->where(['id'=>$OrderId])->first();

        if(empty($orderDetails)){
            echo json_encode(['status'=>'Error', 'message'=>'No Active Order record found with this request.','data'=>[]]);
            die;
        }
        $orderDetails = $orderDetails->toArray();
        echo json_encode(['status'=>'Success', 'message'=>'Request proceed further sucessfully.','data'=>$orderDetails]);
        die;
    }
    
        
        public function updateCartAddress(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = Cart::where(['user_id'=>$user_id,'status'=>1])->first();
        if(empty($cartDetails)){
            echo json_encode(['status'=>'Error', 'message'=>'No Active Cart found with this request.','data'=>[]]);
            die;
        }
        
        DB::table('carts')->where(['user_id'=>$user_id])->update(['address'=>$Data['address'],'longitude'=>$Data['longitude'],'latitude'=>$Data['latitude']]);
        echo json_encode(['status'=>'Success', 'message'=>'Address Request updated sucessfully.','data'=>$Data]);
            die;
    }
    
        public function orderList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        
        $provider_id = $Data['provider_id'];
        $cartOrderDetails = Orders::where(['provider_id'=>$provider_id])->get()->toArray();

        if(empty($cartOrderDetails)){
            echo json_encode(['status'=>'Error', 'message'=>'No Active Cart request found.','data'=>[]]);
            die;
        }
       
        if(!empty($cartOrderDetails)){
            echo json_encode(['status'=>'Error', 'message'=>'Cart ID already added with a request.','data'=>['provider_id'=>$provider_id]]);
            die;
            } 
            else {
                $providers = User::where(['status'=>1,'role'=>2])->get()->toArray();
                if(!empty($providers)){
                    $records = [];
                    foreach($providers as $provider){
                        $records[] = ['cart_id'=>$cart_id,'provider_id'=>$provider['id'],'status'=>1,'created_at'=>date('Y-m-d h:i:s')];
                    }
                    if(!empty($records)){
                        (new Orders())->insert($records);
                    }
                }
                DB::table('carts')->where(['id'=>$cart_id])->update(['status'=>5]);
            }
             echo json_encode(['status'=>'Success', 'message'=>'Request proceed further sucessfully.','data'=>['cart_id'=>$cart_id]]);
            die;
    }
    
    
     /**
     * Get user cart products.
     */
    public function getUserCartProducts(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['user_id']=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $user_id = $Data['user_id'];
        $cartDetails = Cart::with('cartProducts','coupons.CouponType')->where(['carts.user_id'=>$user_id])->first();
        $cart = $products = [];
        $product_count = $discount =  $net_amount = 0;
        $tax = $amount = [];
        if(!empty($cartDetails)){
            $cartDetails = $cartDetails->toArray();
            $serviceId=[];
            foreach($cartDetails['cart_products'] as $product){
                $productId=$product['product_id'];
                $Product = ServiceProducts::select('*','s.id as service_id','ss.service_id','s.name as service_name','ss.name as sub_service_name','ss.tax_percent','service_products.id','p.product_name',
                'product_image','service_products.price as product_price')
                ->leftJoin('sub_services as ss','ss.id','service_products.sub_service_id')
                ->leftJoin('services as s','s.id','ss.service_id','ss.name as sub_service_name')
                ->leftJoin('products as p','p.id','service_products.product_id')
                    ->where(['service_products.id'=>$productId])->first();
            if(!empty($Product)){
                $Product = $Product->toArray();
                $qty=$product['quantity'];
                $Product=array_merge($Product,['quantity'=>$qty,'content'=>$product['content']]);
                $amt=$qty*$Product['price'];
                $amount[]=$amt;
                $tax[]=$amt*$Product['tax_percent']*.01;
                $serviceId['services'][$Product['service_id']]['name']=$Product['service_name'];
           
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['sub_service_name']=$Product['sub_service_name'];
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['product'][] = $Product;
            $product_count++;
                
            }        
            
            }
            unset($cartDetails['cart_products']);
            
            if(!empty($serviceId)){
                $rec=[];
                foreach($serviceId['services'] as $id=>$service){
                    $subser=[];
                    foreach($service['sub_services'] as $k=>$ser){
                        $subser[]=array_merge(['id'=>$k],$ser);
                    }
                    $service['sub_services']=$subser;
                    $service=array_merge(['id'=>$id],$service);
                    $rec[]=$service;
            }
            if(!empty($coupon=$cartDetails['coupons'])){
                $type_val=$coupon['coupon_type']['type_value'];
                if($type_val=='%'){
                    $discount = array_sum($amount)*$coupon['value']*.01;
                }else {
                    $discount=number_format($coupon['value'],2);    
                }
                
            }
            $net_amount = array_sum($amount) - $discount + array_sum($tax);
            }
            $cartDetails=array_merge($cartDetails,['services'=>$rec,'product_count'=>$product_count,'tax'=>array_sum($tax),'amount'=>array_sum($amount),'discount'=>$discount,'net_amount'=>$net_amount]);
            $cart=$cartDetails;
        }
        echo json_encode(['status'=>'Success','data'=>$cart]);
            die;
    } 
}
