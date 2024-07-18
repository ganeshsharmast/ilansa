<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Http\Helpers\Helper;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Coupons;
use App\Models\Company;
use PDF;
use App\Models\Products;
use App\Models\Orders;
use App\Models\OrderRequest;
use App\Models\OrderProducts;
use App\Models\OrderStatus;
use App\Models\ServiceProducts;
use App\Models\SubServices;
use App\Models\User;
use App\Models\UserFavorite;


class OrderController extends Controller
{
    public function __construct()
    {
        // die("-=-="); 
    }

     public function list(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $orderDetails = Orders::with('orderProducts','coupons.CouponType','orderStatus','userDetails','userAccept');
        
        if(isset($Data['user_id'])){
            $user_id=str_replace("'","",str_replace('"',"",$Data['user_id']));
            $orderDetails = $orderDetails->with('orderRequest.providerDetails','orderRequest.workStatusDetails')->where(['user_id'=>$user_id]);
        }
        else if(isset($Data['provider_id'])){
            
            $Data['provider_id'] = $provider_id = str_replace("'","",str_replace('"',"",$Data['provider_id']));
            $orderDetails = $orderDetails->with(['orderRequest'=>function($query) use ($provider_id){
                $dat= $query->with('providerDetails','orderStatus','workStatusDetails')->where(['provider_id'=>$provider_id])->whereIn('order_request.status',[1,2]);
                return $dat;
            }]);
            
        } else {
            echo json_encode(['status'=>'Error', 'message'=>'User-id or Provider-id is missing.','data'=>$Data]);
            die;
        }
        $orderDetails = $orderDetails->get()->toArray();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        if(isset($Data['provider_id']))
        {
        if(!empty($orderDetails)){
            foreach($orderDetails as $k=>$order){
                
                if(!empty($order['order_request']) && isset($Data['provider_id'])){
                    if(isset($order['order_request'][0]) && (in_array($order['user_acceptance'],[3]))){
                        unset($orderDetails[$k]);
                    }
                    // unset($orderDetails[$k]);
                }
                else if(empty($order['order_request']) && !isset($Data['provider_id'])){
                    unset($orderDetails[$k]);
                }
                if(isset($orderDetails[$k])){
                    $usrReq=$orderDetails[$k]['order_request'];
                    $orderDetails[$k]['order_request']=!empty($usrReq)?$usrReq:null;
                }
                
            }
         }
        }
        
        echo json_encode(['status'=>'Success', 'message'=>'Order details fetched successfully.','data'=>array_merge($orderDetails)]);
            die;
    }
    
        // User Order request response
         public function userOrderResponse(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(!isset($Data['order_id'])){
            echo json_encode(['status'=>'Error', 'message'=>'Order-id is missing.','data'=>$Data]);
            die;
        }
        $dataArr=$orderRes=[];
        $order_id = $Data['order_id'];
        if(!isset($Data['user_acceptance']) || empty($Data['user_acceptance'])){
            echo json_encode(['status'=>'Error', 'message'=>'Order response is in-valid.','data'=>$Data]);
            die;
        }
        $dataArr['user_acceptance']=$Data['user_acceptance'];
        if(isset($Data['content'])){
            $dataArr['content'] = $orderRes['content']= $Data['content'];
        }
        if(in_array($Data['user_acceptance'],[2,4,5])){
            $result = Orders::where(['id'=>$order_id])->update($dataArr);
        } else if($Data['user_acceptance']==3) {
            $orderRes['status'] = $Data['user_acceptance'];
            if(OrderRequest::where(['order_id'=>$order_id,'status'=>2])->update($orderRes)){
                OrderRequest::where(['order_id'=>$order_id,'status'=>4])->update(['status'=>1]);
            }
        }
        $orderDetails = Orders::with([
            'orderProducts','coupons.CouponType','userDetails','orderStatus','userAccept',
            'orderRequest'=>function($query){
                return $query->with('providerDetails','orderStatus','workStatusDetails')->whereIn('order_request.status',[2]);
            }])->where(['orders.id'=>$order_id])->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        if(in_array($Data['user_acceptance'],[1,2])){
            $message='User Order acceptance request submitted successfully.';
        } else {
            $message='User Order reject request submitted successfully.';
        }
        echo json_encode(['status'=>'Success', 'message'=>$message,'data'=>$orderDetails]);
            die;
    }
    
    // Provider Order request response
         public function providerRequestResponse(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(!isset($Data['order_id'])){
            echo json_encode(['status'=>'Error', 'message'=>'Order-id is missing.','data'=>$Data]);
            die;
        }
        if(!isset($Data['provider_id'])){
            echo json_encode(['status'=>'Error', 'message'=>'Provider-id is missing.','data'=>$Data]);
            die;
        }
        $Data['order_id']=str_replace("'","",str_replace('"',"",$Data['order_id']));
        $Data['provider_id']=str_replace("'","",str_replace('"',"",$Data['provider_id']));
        $order_id = $Data['order_id'];
        $provider_id = $Data['provider_id'];
        $order = Orders::with('userDetails')->where(['id'=>$order_id])->first();
        if(empty($order)){
            echo json_encode(['status'=>'Error', 'message'=>'Order is not exist.','data'=>$Data]);
            die;
        }
        if($order->status==3 || $order->user_acceptance==3)
        {
            echo json_encode(['status'=>'Success', 'message'=>'Order rejected by the user.','data'=>$Data]);    
            die;
        }
        else if($order->status==4 || $order->user_acceptance==4){
            echo json_encode(['status'=>'Error', 'message'=>'Order cancelled by user.','data'=>$Data]);
            die;
        }
        else if($order->status==5 || $order->user_acceptance==5){
            echo json_encode(['status'=>'Error', 'message'=>'Order is already completed.','data'=>$Data]);
            die;
        }
        if(in_array($Data['status'],[3,4,5])){
            OrderRequest::where(['order_id'=>$order_id,'provider_id'=>$provider_id])->update(['content'=>$Data['content'],'status'=>$Data['status']]);
        } else {
            OrderRequest::where(['order_id'=>$order_id])->update(['status'=>4]);
            OrderRequest::where(['order_id'=>$order_id,'provider_id'=>$provider_id])->update(['content'=>$Data['content'],'status'=>2]);    
        }
        
        $orderDetails = Orders::with('orderProducts','coupons.CouponType','userDetails','orderStatus','userAccept')
          ->with(['orderRequest'=>function($query) use ($provider_id){
                return $query->with('providerDetails','orderStatus','workStatusDetails')->where(['provider_id'=>$provider_id,'status'=>2]);
            }])
            ->where(['orders.id'=>$order_id])->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        echo json_encode(['status'=>'Success', 'message'=>'Order request updated successfully.','data'=>$orderDetails]);
            die;
    }
    

    /**
     * Show the profile for a given user.
     */
    public function orderDetails(Request $req, $withoutJson=0)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $Data['order_id']=str_replace("'","",str_replace('"',"",$Data['order_id']));
        $dataArr=[];
        $order_id = $Data['order_id'];
        $orderDetails = Orders::with('orderProducts.product.services','coupons.CouponType','userDetails','orderStatus','userAccept')->with(['orderRequest'=>function($query) {
                return $query->with('providerDetails','companyDetails','workStatusDetails','orderStatus')->whereIn('order_request.status',[2,5]);
            }])->where(['orders.id'=>$order_id])->first();
            
        $cart = $products = [];
        $product_count = $discount =  $net_amount = 0;
        $tax = $amount = [];
        if(!empty($orderDetails)){
            $orderDetails = $orderDetails->toArray();
            if(isset($orderDetails)){
                $usrReq=$orderDetails['order_request'];
                $orderDetails['order_request']=!empty($usrReq)?$usrReq:null;
            }
            $serviceId=[];
            foreach($orderDetails['order_products'] as $product){
                $productId=$product['product_id'];
                $Product = ServiceProducts::select('*','s.id as service_id','ss.service_id','s.name as service_name','ss.name as sub_service_name','ss.tax_percent','service_products.id','p.product_name',
                'product_image','service_products.price as product_price')
                ->leftJoin('sub_services as ss','ss.id','service_products.sub_service_id')
                ->leftJoin('services as s','s.id','ss.service_id','ss.name as sub_service_name')
                ->leftJoin('products as p','p.id','service_products.product_id')
                    ->where(['service_products.product_id'=>$productId])->first();
                
            if(!empty($Product)){
                $Product = $Product->toArray();
                $qty=$product['quantity'];
                
                $Product=array_merge($Product,['quantity'=>$qty,'content'=>$product['content'],'rating'=>$product['rating']]);
                $amt=$qty*$Product['price'];
                $amount[]=$amt;
                $tax[]=$amt*$Product['tax_percent']*.01;
                $serviceId['services'][$Product['service_id']]['name']=$Product['service_name'];
           
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['sub_service_name']=$Product['sub_service_name'];
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['product'][] = $Product;
            $product_count++;
            }        
            }
            unset($orderDetails['order_products']);
            $rec=[];
            if(!empty($serviceId)){
                
                foreach($serviceId['services'] as $id=>$service){
                    $subser=[];
                    foreach($service['sub_services'] as $k=>$ser){
                        $subser[]=array_merge(['id'=>$k],$ser);
                    }
                    $service['sub_services']=$subser;
                    $service=array_merge(['id'=>$id],$service);
                    $rec[]=$service;
            }
            if(!empty($coupon=$orderDetails['coupons'])){
                $type_val=$coupon['coupon_type']['type_value'];
                if($type_val=='%'){
                    $discount = array_sum($amount)*$coupon['value']*.01;
                }else {
                    $discount=number_format($coupon['value'],2);    
                }
                
            }
            $net_amount = array_sum($amount) - $discount + array_sum($tax);
            }
            if(!empty($orderDetails['order_request'])){
              $orderRequest = $orderDetails['order_request'][0];
              $userFav = (new UserFavorite())
                            ->where(['user_id'=>$orderDetails['user_id'],
                            'provider_id'=>$orderRequest['provider_id']
                            ])->count();
              $orderRequest['user_favorite'] = ($userFav==1)?$userFav:null;
              $orderDetails['order_request'] = $orderRequest;
            }
            $orders=array_merge($orderDetails,['pdf'=>url('/api/order/bill-generate/'.$orderDetails['id']),'services'=>$rec,'product_count'=>$product_count,'tax'=>array_sum($tax),'amount'=>array_sum($amount),'discount'=>$discount,'net_amount'=>$net_amount]);
        }
        
        if($withoutJson){
            return $orders;
        }
        echo json_encode(['status'=>'Success','data'=>$orders]);
            die;
    }

    
        // Provider Order Work Status Update
     public function orderWorkStatusUpdate(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(!isset($Data['order_id'])){
            echo json_encode(['status'=>'Error', 'message'=>'Order-id is missing.','data'=>$Data]);
            die;
        }
        if(!isset($Data['provider_id'])){
            echo json_encode(['status'=>'Error', 'message'=>'Provider-id is missing.','data'=>$Data]);
            die;
        }
        $order_id = str_replace("'","",str_replace('"',"",$Data['order_id']));
        $provider_id=str_replace("'","",str_replace('"',"",$Data['provider_id']));
        $order = Orders::with('orderProducts')->where(['orders.id'=>$order_id])->first();
       
        if(empty($order)){
            echo json_encode(['status'=>'Error', 'message'=>'Order is not exist.','data'=>$Data]);
            die;
        }
        if($order->status==3 || $order->user_acceptance==3)
        {
            echo json_encode(['status'=>'Success', 'message'=>'Order rejected by the user.','data'=>$Data]);    
            die;
        }
        else if($order->status==4 || $order->user_acceptance==4){
            echo json_encode(['status'=>'Error', 'message'=>'Order cancelled by user.','data'=>$Data]);
            die;
        }
        else if($order->status==5 || $order->user_acceptance==5){
            echo json_encode(['status'=>'Error', 'message'=>'Order is already completed.','data'=>$Data]);
            die;
        }
        if(in_array($Data['work_status'],[2,3,4,5])){
            OrderRequest::where(['order_id'=>$order_id,'provider_id'=>$provider_id])->update(['work_status'=>$Data['work_status']]);
        } else {
            echo json_encode(['status'=>'Error', 'message'=>'Something is missing.','data'=>$Data]);
            die;    
        }
        
        echo json_encode(['status'=>'Success', 'message'=>'Work status request updated successfully.','data'=>$Data]);
            die;
    }
     
     
     public function workHistory(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $orderDetails = Orders::with('userDetails');
        
        if(isset($Data['provider_id'])){
            
            $Data['provider_id'] = $provider_id = str_replace("'","",str_replace('"',"",$Data['provider_id']));
            $orderDetails = $orderDetails->with(['orderRequest'=>function($query) use ($provider_id){
                $dat= $query->with('providerDetails','orderStatus','workStatusDetails')->where(['provider_id'=>$provider_id]);
                return $dat;
            }]);
            
        } else {
            echo json_encode(['status'=>'Error', 'message'=>'Provider-id is missing.','data'=>$Data]);
            die;
        }
        $orderDetails = $orderDetails->get()->toArray();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        $orderStatus = (new OrderStatus())->pluck('name','id')->toArray();
        $result=[];     
        foreach($orderStatus as $k=>$name){
            $result[$k] = 0;    
        }
        if($Data['provider_id']==21)
        {
            foreach($orderDetails as $k=>$order)
            {
                if(in_array($order['user_acceptance'],[4,5]))  {
                    $result[$order['user_acceptance']]+=1;        
                } 
                else if(!empty($order['order_request']))
                {
                    if(isset($order['order_request'][0])){
                        $ordStatus = $order['order_request'][0]['status'];
                        $result[$ordStatus]+=1;        
                    } 
                }
            }
        }
        
        foreach($orderStatus as $id=>$name){
            unset($orderStatus[$id]);
            $orderStatus[$name]=$result[$id];
        }
                        
        echo json_encode(['status'=>'Success', 'message'=>'Work history fetched successfully.','data'=>array_merge($orderStatus)]);
            die;
    }       

    
        // User Order report
        public function userOrderReport(Request $req)
        {
            $jsonData = file_get_contents("php://input");
            $Data = json_decode($jsonData,true);
            if(empty($Data)){
               $Data = $req->all(); 
            }
            if(!isset($Data['order_id'])){
                echo json_encode(['status'=>'Error', 'message'=>'Order-id is missing.','data'=>$Data]);
                die;
            }
            $dataArr=$orderRes=[];
            $order_id = $Data['order_id'];
            if(!isset($Data['order_report']) || empty($Data['order_report'])){
                echo json_encode(['status'=>'Error', 'message'=>'Order report contains in-valid data.','data'=>$Data]);
                die;
            }
            $orderDetails = Orders::with(['userDetails',
                'orderRequest'=>function($query){
                    return $query->with('providerDetails','orderStatus','workStatusDetails')->whereIn('order_request.status',[2]);
                }])->where(['orders.id'=>$order_id])->first();
            $orderDetails = json_decode(json_encode($orderDetails),true);
            if(empty($orderDetails)){
                echo json_encode(['status'=>'Error', 'message'=>'Order not found in the record.','data'=>$Data]);
                die;
            } 
            else if($orderDetails['user_acceptance']!=5){
                echo json_encode(['status'=>'Error', 'message'=>'Order is not successfully completed.','data'=>$Data]);
                die;
            }
            
            $result = Orders::where(['id'=>$order_id])->update(['order_report'=>$Data['order_report']]);
           
            echo json_encode(['status'=>'Success', 'message'=>"Order Reported successfully to admin",'data'=>$Data]);
                die;
        }
    
    
        // User Order report
        public function orderProductRating(Request $req)
        {
            $jsonData = file_get_contents("php://input");
            $Data = json_decode($jsonData,true);
            if(empty($Data)){
               $Data = $req->all(); 
            }
            $Data = json_decode(json_encode($Data),true);
            if(!isset($Data['order_id'])){
                echo json_encode(['status'=>'Error', 'message'=>'Order-id is missing.','data'=>$Data]);
                die;
            }
            $dataArr=$orderRes=[];
            $order_id = $Data['order_id'];
            if(!isset($Data['rating']) || empty($Data['rating'])){
                echo json_encode(['status'=>'Error', 'message'=>'Order rating is not contains a valid data.','data'=>$Data]);
                die;
            }
            $orderDetails = Orders::with(['userDetails',
                'orderRequest'=>function($query){
                    return $query->with('providerDetails','orderStatus','workStatusDetails')->whereIn('order_request.status',[2]);
                }])->where(['orders.id'=>$order_id])->first();
            $orderDetails = json_decode(json_encode($orderDetails),true);
            if(empty($orderDetails)){
                echo json_encode(['status'=>'Error', 'message'=>'Order not found in the record.','data'=>$Data]);
                die;
            } 
            if($orderDetails['user_acceptance']!=5){
                echo json_encode(['status'=>'Error', 'message'=>'Order is still not completed to give rating.','data'=>$Data]);
                die;
            }
            $arr=[];
            $user_comment = preg_replace("/&#?[a-z0-9]{2,8};/i","",trim(@$Data['user_comment'])); 
            if(!empty($user_comment)){
                Orders::where(['id'=>$order_id])->update(['user_comment'=>$user_comment]);   
            }
            $Data['rating'] = json_decode($Data['rating'],true);
            foreach($Data['rating'] as $ar)
            {
                $product_id=str_replace("'","",str_replace('"',"",$ar['product_id']));
                $rating=str_replace("'","",str_replace('"',"",$ar['rating']));
                $result = OrderProducts::where(['order_id'=>$order_id,'product_id'=>$product_id])->update(['rating'=>$rating]);
            }
            echo json_encode(['status'=>'Success', 'message'=>"Order Rating done successfully",'data'=>$Data]);
                die;
        }
    
    
    /**
     * Show the profile for a given user.
     */
    public function orderProductDetails(Request $req, $withoutJson=0)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(empty($Data)){
          $Data = $_POST; 
        }
        $Data['order_id']=str_replace("'","",str_replace('"',"",$Data['order_id']));
        $dataArr=[];
        $order_id = $Data['order_id'];
        
        $orderDetails = Orders::with('orderProducts.product.services','coupons.CouponType','userDetails','orderStatus','userAccept')->with(['orderRequest'=>function($query) {
                return $query->with('providerDetails','companyDetails','workStatusDetails','orderStatus')->whereIn('order_request.status',[2]);
            }])->where(['orders.id'=>$order_id])->first();
        $cart = $products = [];
        $product_count = $discount =  $net_amount = 0;
        $tax = $amount = [];
        if(!empty($orderDetails)){
            $orderDetails = $orderDetails->toArray();
            if(isset($orderDetails)){
                $usrReq=$orderDetails['order_request'];
                $orderDetails['order_request']=!empty($usrReq)?$usrReq:null;
            }
            $serviceId=[];
            foreach($orderDetails['order_products'] as $product){
                $productId=$product['product_id'];
                $Product = ServiceProducts::select('*','s.id as service_id','ss.service_id','s.name as service_name','ss.name as sub_service_name','ss.tax_percent','service_products.id','p.product_name',
                'product_image','service_products.price as product_price')
                ->leftJoin('sub_services as ss','ss.id','service_products.sub_service_id')
                ->leftJoin('services as s','s.id','ss.service_id','ss.name as sub_service_name')
                ->leftJoin('products as p','p.id','service_products.product_id')
                ->where(['service_products.product_id'=>$productId])->first();
               
            if(!empty($Product)){
                $Product = $Product->toArray();
                $qty=$product['quantity'];
                $PRoduct = ['service_id'=>$Product['service_id'],
                            'service_name'=>$Product['service_name'],
                            'sub_service_id'=>$Product['sub_service_id'], 
                            'sub_service_name'=>$Product['sub_service_name'], 
                            'product_id'=>$Product['product_id'], 
                            'price'=>$Product['price'],
                            'status'=>$Product['status'],
                            'product_name'=>$Product['product_name'],
                            'tax_percent'=>$Product['tax_percent'],
                            'product_image'=>$Product['product_image']];
                
                $Product=array_merge($PRoduct,['quantity'=>$qty,'content'=>$product['content'],'rating'=>$product['rating']]);
                $amt=$qty*$Product['price'];
                $amount[]=$amt;
                $tax[]=$amt*$Product['tax_percent']*.01;
                $serviceId['services'][$Product['service_id']]['name']=$Product['service_name'];
           
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['sub_service_name']=$Product['sub_service_name'];
                $serviceId['services'][$Product['service_id']]['sub_services'][$Product['sub_service_id']]['product'][] = $Product;
            $product_count++;
                
            }        
            
            }
            unset($orderDetails['order_products']);
            
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
            if(!empty($coupon=$orderDetails['coupons'])){
                $type_val=$coupon['coupon_type']['type_value'];
                if($type_val=='%'){
                    $discount = array_sum($amount)*$coupon['value']*.01;
                }else {
                    $discount=number_format($coupon['value'],2);    
                }
                
            }
            $net_amount = array_sum($amount) - $discount + array_sum($tax);
            }
            if(!empty($orderDetails['order_request'])){
              $orderRequest = $orderDetails['order_request'][0];
              $userFav = (new UserFavorite())
                            ->where(['user_id'=>$orderDetails['user_id'],
                            'provider_id'=>$orderRequest['provider_id']
                            ])->count();
              $orderRequest['user_favorite'] = ($userFav==1)?$userFav:null;
              $orderDetails['order_request'] = $orderRequest;
            }
            $orders=array_merge($orderDetails,['services'=>$rec,'product_count'=>$product_count,'tax'=>array_sum($tax),'amount'=>array_sum($amount),'discount'=>$discount,'net_amount'=>$net_amount]);
        }
        if(empty($orders)){
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }
        if($withoutJson){
            return $orders;
        }
        echo json_encode(['status'=>'Success','data'=>$orders]);
            die;
    }
    

        /* Order list. */
        public function orderList()
        {
            $breadcrumbs=['title'=>'Order List','header'=>'Order','sub_header'=>'List','sidebar'=>'order_list','url'=>'order/list'];
            $orders = Orders::with('userDetails','orderRequest','orderProducts.serviceProduct','statusDetails')->get()->toArray();
            if(!empty($orders)){
                $orders = json_decode(json_encode($orders), true);
            }
            
            return view('admin.order.list',['orders'=>$orders,'breadcrumbs'=>$breadcrumbs]);
        }
        
        /* Order Detail*/
        public function detail($orderId)
        {
            $breadcrumbs=['title'=>'Order Detail','header'=>'Order','sub_header'=>'Detail','sidebar'=>'order_list','url'=>'order/list'];
            
            $order = Orders::with('orderProducts.product.services','orderProducts.productStatus','orderProducts.serviceProduct.subService','coupons.CouponType','orderStatus','userDetails','userAccept')->where(['id'=>$orderId]);
           
           
            $order = $order->with(['orderRequest'=>function($query){
                $dat= $query->with('providerDetails','orderStatus','workStatusDetails')->whereIn('order_request.status',[1,2]);
                return $dat;
            }])->first();
            
            
            if(!empty($order)){
                $order = json_decode(json_encode($order), true);
            }
            return view('admin.order.detail',['order'=>$order,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
        public function billGeneratePDF($orderId,Request $request)
        {
            $request->merge(['order_id'=> $orderId]);
            $order = $this->orderDetails($request, 1);
            if(empty($order)){
                echo json_encode(['status'=>'Error', 'message'=>'Order details found missing.']);
            die;
            }
            $company = (new Company())->where(['user_id'=>1])->first()->toArray();
            $data=['order'=>$order,'Company'=>$company];    
            $pdf = PDF::loadView('admin.order.invoice', $data);
            // $pdf->setPaper('a4', 'landscape');
            
            return $pdf->download('invoice.pdf');
        }
        

}
