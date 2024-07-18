<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Services;
use App\Models\Status;
use App\Models\Subservices;
use App\Models\UserServiceRequest;
use App\Models\ProviderSubServices;
use App\Models\RequestSubServices;
use App\Http\Helpers\Helper;
use App\Http\Controllers\CartController;

class ServiceController extends Controller
{
    private $status;
    
    public function __construct()
    {
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
    }

    public function list()
    {
        $breadcrumbs=['title'=>'Service List','header'=>'Service','sub_header'=>'List','sidebar'=>'service_list','url'=>'services/list'];
        $services = Services::with('statusDetails')->where(['status'=>1])->get()->toArray();
        if(!empty($services)){
            $services = json_decode(json_encode($services), true);
        }
        
        if(!empty($services)){
            foreach($services as $key=>$service){
                $baseURL = URL::to('/');
                $image=!empty($service['image'])?$service['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $services[$key] = $service;
            }   
        }
        return view('admin.service.list',['services'=>$services,'breadcrumbs'=>$breadcrumbs]);
    }
    
        public function userRequests()
    {
        $breadcrumbs=['title'=>'Service User Requests','header'=>'Service','sub_header'=>'User requests','sidebar'=>'service_user_requests','url'=>'services/requests'];
        $serviceRequests = UserServiceRequest::with('userDetails','statusDetails')->where(['status'=>1])->get()->toArray();
  
        return view('admin.service.user_requests',['serviceRequests'=>$serviceRequests,'breadcrumbs'=>$breadcrumbs]);
    }
    
               /* Service View*/
        public function requestView($serviceId)
        {
            $breadcrumbs=['title'=>'Service Request View','header'=>'Service','sub_header'=>'Request view','sidebar'=>'service_request_view','url'=>'service/requests'];
            
            $serviceRequest = (new UserServiceRequest())::with('userDetails','statusDetails')->where(['id'=>$serviceId])->first();
            
            if(!empty($serviceRequest)){
                $serviceRequest = json_decode(json_encode($serviceRequest->toArray()), true);
            }
            return view('admin.service.request_view',['serviceRequest'=>$serviceRequest,'breadcrumbs'=>$breadcrumbs]);
        }
    
    public function getServiceList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $services = DB::table('services')->where(['status'=>1])->get()->toArray();
        if(!empty($services)){
            $services = json_decode(json_encode($services), true);
        }
        
        if(!empty($services)){
            foreach($services as $key=>$service){
                $baseURL = URL::to('/');
                $image=!empty($service['image'])?$service['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                // $service['image']=$baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.$image;
                $services[$key] = $service;
            }
            echo json_encode(['status'=>'Success','message'=>'Service List fetched successfully.','data'=>$services]);
            die;   
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$services]);
        die;
    }
    
    
    public function searchServiceList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(empty($Data['service_name'])){
            echo json_encode(['status'=>'Error','message'=>'Service name is empty.']);
            die;
        }
        $services = DB::table('services')->where(['status'=>1])->where('name','like','%'.$Data['service_name'].'%')->get()->toArray();
        $services = json_decode(json_encode($services), true);
        
        if(!empty($services)){
            foreach($services as $key=>$service){
                $image=!empty($service['image'])?$service['image']:'default.jpg';
                $baseURL = URL::to('/');
                $service['image']=/*$baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.
                */
                $image;
                $services[$key] = $service;
            }
            echo json_encode(['status'=>'Success','message'=>'Service List fetched successfully.','data'=>$services]);
            die;   
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$services]);
        die;
    }
    /**
     * Show the profile for a given user.
     */
    public function getSubServiceList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $service_id=str_replace("'","",str_replace('"',"",$Data['service_id']));
        $service = (new Services())->where(['id'=>$service_id])->first();
        if(empty($service)){
            echo json_encode(['status'=>'Error','message'=>'Service not exist.']);
            die;   
        }
        if($service->status!=1){
            echo json_encode(['status'=>'Error','message'=>'Service is not active.']);
            die;   
        }
        $subServices = DB::table('sub_services')->where(['service_id'=>$service->id])->get()->toArray();
        if(!empty($subServices))
          {
              $subServices=json_decode(json_encode($subServices),true);
              $subServiceIds = [];
            if(isset($Data['provider_id']) && !empty($Data['provider_id']))
             {
                 $provider_id=str_replace("'","",str_replace('"',"",$Data['provider_id']));
                $subServiceIds = DB::table('provider_sub_services')->select('sub_service_id')->where(['provider_id'=>$provider_id])->get()->toArray();
                if(!empty($subServiceIds)){
                    $subServiceIds=json_decode(json_encode($subServiceIds),true);
                    $subServiceIds = array_column($subServiceIds,'sub_service_id');
                }
            }
            foreach($subServices as $k=>$service){
                 $subServices[$k]['selected'] = in_array($service['id'],$subServiceIds)?1:0;
            }
            echo json_encode(['status'=>'Success','message'=>'Sub Service List fetched successfully.','data'=>$subServices]);
            die;   
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$subServices]);
        die;
    }    
    
    
    public function searchSubServiceList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        if(!isset($Data['service_id']) || empty($Data['service_id'])){
            echo json_encode(['status'=>'Error','message'=>'Service ID is empty.']);
            die;
        }
        if(empty($Data['service_name'])){
            echo json_encode(['status'=>'Error','message'=>'Service name is empty.']);
            die;
        }
        $service_id=str_replace("'","",str_replace('"',"",$Data['service_id']));
        $service = (new Services())->where(['status'=>$service_id])->first();
        if(empty($service)){
            echo json_encode(['status'=>'Error','message'=>'Service not exist.']);
            die;   
        }
        if($service->status!=1){
            echo json_encode(['status'=>'Error','message'=>'Service is not active.']);
            die;   
        }
        $subServices = DB::table('sub_services')->where(['service_id'=>$service->id])
                        ->where('name','like','%'.$Data['service_name'].'%')->get()->toArray();
        if(!empty($subServices)){
            echo json_encode(['status'=>'Success','message'=>'Sub Service List fetched successfully.','data'=>$subServices]);
            die;   
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$subServices]);
        die;
    }    
    
    /** getSubServiceProductList
     * Show the profile for a given user.
     */
    public function getServiceProductList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }   
        $user_id=str_replace("'","",str_replace('"',"",$Data['user_id']));
        $cartProducts = (new CartController())->getUserCartProductList($user_id, 1);
        $sub_service_id=str_replace("'","",str_replace('"',"",$Data['sub_service_id']));
        
        $subService = DB::table('sub_services')->where(['id'=>$sub_service_id])->first();
        if(empty($subService)){
            echo json_encode(['status'=>'Error','message'=>'Sub Service not exist.']);
            die;   
        }
        if($subService->status!=1){
            echo json_encode(['status'=>'Error','message'=>'Sub Service is not active.']);
            die;   
        }
        $products = DB::table('service_products as s')
                    ->leftJoin('products as p','p.id','s.product_id')
                    ->select('s.*','p.product_name','p.product_image')
                    ->where(['sub_service_id'=>$subService->id,'p.status'=>1])->get()->toArray();
        if(!empty($products)){
            $products=json_decode(json_encode($products),true);
            foreach($products as $key=>$product)
            {
                $product['image']    = $product['product_image'];
                $product['quantity'] = (!empty($cartProducts) && isset($cartProducts[$product['product_id']]))?$cartProducts[$product['product_id']]:0;
                $products[$key] = $product;
            }
            echo json_encode(['status'=>'Success','message'=>'Products List fetched successfully.','data'=>$products]);
            die;   
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$products]);
        die;
    }
    
    /* Get Provider Service details. */
    public function getProviderServiceDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $provider_id = str_replace("'","",str_replace('"',"",$Data['provider_id']));
        $serviceDetails = $this->getProviderServiceInfo($provider_id);
        if(!empty($serviceDetails)){
            $services = [];
            foreach($serviceDetails as $service){
                if(!empty($service['sub_services'])){
                    $ser = $service['sub_services']['services'];
                    $services[$ser['id']] = ['name'=>$ser['name'],'image'=>$ser['image']];    
                }
            }
            
            if(!empty($services)){
            $Services = [];
            foreach($services as $id=>$val){
                $Services[] = ['id'=>$id,'service_name'=>$val['name'],'image'=>$val['image']];
            }
            echo json_encode(['status'=>'Success','message'=>'Records Found successfully.','data'=>$Services]);
            die;
            }
            $serviceDetails = json_decode(json_encode($serviceDetails), true);
            echo json_encode(['status'=>'Success','message'=>'Records Found successfully.','data'=>$serviceDetails]);
            die;
            
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$serviceDetails]);
        die;
    }
    
        /* Get Provider Service details. */
    public function getProviderSubServiceDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $provider_id = str_replace("'","",str_replace('"',"",$Data['provider_id']));
        $service_id = str_replace("'","",str_replace('"',"",$Data['service_id']));
        $serviceDetails = $this->getProviderServiceInfo($provider_id,$service_id);
        if(!empty($serviceDetails)){
            $subServices = [];
            foreach($serviceDetails as $service)
            {
                $subSer = $service['sub_services'];
                if($subSer!=null && !empty($subSer)){
                    $subServices[$service['sub_service_id']] = ['name'=>$subSer['name'],'image'=>$subSer['image']];
                }
            }
            if(!empty($subServices)){
            $SubServices = [];
            foreach($subServices as $id=>$val){
                $SubServices[] = ['id'=>$id,'sub_service_name'=>$val['name'],'image'=>$val['image'],'selected'=>1];
            }
            echo json_encode(['status'=>'Success','message'=>'Records Found successfully.','data'=>$SubServices]);
            die;
            }
            $serviceDetails = json_decode(json_encode($subServices), true);
            echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$serviceDetails]);
            die;
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>$serviceDetails]);
        die;
    }
    
        /* Add Sub Service. */
    public function addSubService(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        $sub_service_id = str_replace("'","",str_replace('"',"",$Data['sub_service_id']));
        $provider_id = str_replace("'","",str_replace('"',"",$Data['provider_id']));
        if(!empty($sub_service_id)){
            $subServices = [];
            $Data['sub_service_id'] = (array)$sub_service_id;
            foreach($Data['sub_service_id'] as $subService){
                $subServiceDetails = DB::table('provider_sub_services')->where(['provider_id'=>$provider_id,'sub_service_id'=>$subService])->first();
                if(empty($subServiceDetails)){
                    $subServices[] = ['provider_id'=>$provider_id,'sub_service_id'=>$subService,'status'=>1,'created_at'=>date('Y-m-d h:i:s')];
                }
            }
            if(!empty($subServices)){
                DB::table('provider_sub_services')->insert($subServices);
            echo json_encode(['status'=>'Success','message'=>'Sub-Services added successfully.','data'=>[]]);
            die;
            }
            echo json_encode(['status'=>'Success','message'=>'Services already added.','data'=>[]]);
            die;
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>[]]);
        die;
    }
    
            /* Remove Sub Service. */
    public function removeSubService(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $Data = json_decode($jsonData,true);
        if(empty($Data)){
           $Data = $req->all(); 
        }
        
        if(!empty($Data['sub_service_id']))
        {
            $sub_service_id = str_replace("'","",str_replace('"',"",$Data['sub_service_id']));
            $provider_id = str_replace("'","",str_replace('"',"",$Data['provider_id']));
               
            $Data['sub_service_id'] = (array)$sub_service_id;
            $query=DB::table('provider_sub_services')->where(['provider_id'=>$provider_id])->whereIn('sub_service_id',$sub_service_id)->delete();
             echo json_encode(['status'=>'Success','message'=>'Sub-Services removed successfully.','data'=>[]]);
            die;
        }
        echo json_encode(['status'=>'Success','message'=>'No Record Found.','data'=>[]]);
        die;
    }
    
            /* Request Sub Service. */
    public function requestSubService(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data = $req->all(); 
        }
        $service_id = str_replace("'","",str_replace('"',"",$data['service_id']));
        $provider_id = str_replace("'","",str_replace('"',"",$data['provider_id']));
        if(empty($service_id))
        {
            echo json_encode(['status'=>'Error','message'=>'Service-id is empty or not selected.','data'=>$data]);
            die;
        }
        (new RequestSubServices())->insert(['service_id'=>$service_id,
            'provider_id' => $provider_id, 
            'subject' => $data['subject'], 
            'description' => $data['description'], 
            'created_at'=>date('Y-m-d h:i:s'),
            'status'=>1]);
        
        echo json_encode(['status'=>'Error','message'=>'Request Submitted Successfully.','data'=>$data]);
        die;
    }
    
            /* Request Sub Service. */
    public function requestSubServiceList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data = $req->all(); 
        }
        $provider_id = str_replace("'","",str_replace('"',"",$data['provider_id']));
        if(empty($provider_id))  {
            echo json_encode(['status'=>'Error','message'=>'Provider-id is missing.','data'=>$data]);
            die;
        }
        $data = RequestSubServices::with(['services'])->where(['provider_id'=>$provider_id,'status'=>1])->get()->toArray();
        echo json_encode(['status'=>'Success','message'=>'Request Submitted Successfully.','data'=>$data]);
        die;
    }
    
        public function requestSubServiceDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data = $req->all(); 
        }
        $request_id = str_replace("'","",str_replace('"',"",$data['request_id']));
        if(empty($request_id))
        {
            echo json_encode(['status'=>'Error','message'=>'Request-id is missing.','data'=>$data]);
            die;
        }
        $data = RequestSubServices::with(['services'])->where(['id'=>$request_id,'status'=>1])->get()->toArray();
        echo json_encode(['status'=>'Success','message'=>'Request fetched Successfully.','data'=>$data]);
        die;
    }
    
    /* Get Provider Service information. */
    public function getProviderServiceInfo($provider_id,$service_id='')
    {
        $query = ProviderSubServices::with(['subServices'=>function($query) use( $service_id)
                {
                if(!empty($service_id)){
                    $query->where('service_id',$service_id);    
                }
                 return $query->select('id','name','service_id','image');
                },
                'subServices.services' => function($query) use ($service_id){
            
            return $query->select('id','name','image');
        }
        ]);
        return $query->get()->toArray();
    }

    /* Service create*/
        public function create(Request $req)
        {
            $breadcrumbs=['title'=>'Service Create','header'=>'Service','sub_header'=>'Create','sidebar'=>'service_create','url'=>'service/list'];
            
            return view('admin.service.create',['status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
           /* Service View*/
        public function view($serviceId)
        {
            $breadcrumbs=['title'=>'Service View','header'=>'Service','sub_header'=>'View','sidebar'=>'service_view','url'=>'service/list'];
            
            $service = (new Services())::with('statusDetails')->where(['id'=>$serviceId])->first();
            
            if(!empty($service)){
                $service = json_decode(json_encode($service), true);
            }
            return view('admin.service.view',['service'=>$service,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
        /* Service Edit*/
        public function edit($serviceId)
        {
            $breadcrumbs=['title'=>'Service Edit','header'=>'Service','sub_header'=>'Edit','sidebar'=>'service_edit','url'=>'service/list'];
            $service = Services::with('statusDetails')->where(['id'=>$serviceId])->first();
            if(!empty($service)){
                $service = json_decode(json_encode($service), true);
            }
            return view('admin.service.create',['service'=>$service,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        public function save(Request $req)
        {
            $service = new Services();
            $dat=['name'=>$req->name,
                  'status'=>$req->status,
                  ];
            if($req->image){
                $image = time().'.'.request()->image->getClientOriginalExtension();
                request()->image->move(public_path('images'), $image);
                
                $dat['image']=url('/public/images/'.$image);
            }      
            if(isset($req->id))
            {
                $id = $req->id;
               $service->where(['id'=>$req->id])->update($dat);
            } else {
                $id=$service->insertGetId($dat);
            }   
            echo json_encode(['status'=>'Success', 'message'=>'Service details saved successfully.','data'=>$id]);
            die;
        }
        
        
        public function delete(Request $req)
        {
            $service= new Services();
            if($service->where(['id'=>$req->id])->update(['status'=>4]))
            {
            echo json_encode(['status'=>'Success', 'message'=>'Service details deleted successfully.','data'=>[]]);
            die;
            }
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }

}
