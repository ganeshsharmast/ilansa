<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Services;
use App\Models\SubServices;
use App\Models\ServiceProducts;
use App\Models\Status;
use App\Http\Helpers\Helper;
use App\Http\Controllers\CartController;

class SubServicesController extends Controller
{
    
    private $status;
    private $services;
    
    public function __construct()
    {
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
        $this->services = (new Services())::whereIn('status',[1,2])->pluck('name','id')->toArray();
    }

    public function list()
    {
        $breadcrumbs=['title'=>'Sub Service List','header'=>'Sub Service','sub_header'=>'List','sidebar'=>'sub_service_list','url'=>'sub-service/list'];
        $sub_services = SubServices::with('services','statusDetails')->where(['status'=>1])->get()->toArray();
        if(!empty($sub_services)){
            $sub_services = json_decode(json_encode($sub_services), true);
        }
        if(!empty($sub_services)){
            foreach($sub_services as $key=>$sub_service){
                $baseURL = URL::to('/');
                $image=!empty($service['image'])?$service['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $sub_services[$key]['image'] = $image;
            }   
        }
        
        return view('admin.sub-service.list',['sub_services'=>$sub_services,'breadcrumbs'=>$breadcrumbs]);
    }
    
    
    public function productList($subServiceId)
    {
        $breadcrumbs=['title'=>'Sub Service Products','header'=>'Sub Service','sub_header'=>'Products','sidebar'=>'sub_service_products','url'=>'sub-service/list'];
        $service_products = ServiceProducts::with('subService.services','product.productStatus')->where(['sub_service_id'=>$subServiceId])->get()->toArray();
        if(!empty($service_products)){
            $service_products = json_decode(json_encode($service_products), true);
        }
        
        return view('admin.sub-service.product_list',['service_products'=>$service_products,'breadcrumbs'=>$breadcrumbs]);
    }
    
    
    /* Sub-Service create*/
        public function create(Request $req)
        {
            $breadcrumbs=['title'=>'Sub Service Create','header'=>'Sub-Service','sub_header'=>'Create','sidebar'=>'sub_service_create','url'=>'sub-service/list'];
            
            return view('admin.sub-service.create',['services'=>$this->services,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
    public function view($subServiceId)
    {
        $breadcrumbs=['title'=>'Sub Service View','header'=>'Sub Service','sub_header'=>'View','sidebar'=>'sub_service_view','url'=>'sub-service/list'];
        $sub_service = SubServices::with('services','statusDetails')->where(['id'=>$subServiceId])->first();
        if(!empty($sub_service)){
            $sub_service = json_decode(json_encode($sub_service), true);
        }
        
        if(!empty($sub_service)){
                $baseURL = URL::to('/');
                $image=!empty($sub_service['image'])?$sub_service['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $sub_services['image'] = $image;
               
        }
        return view('admin.sub-service.view',['sub_service'=>$sub_service,'breadcrumbs'=>$breadcrumbs]);
    }
        
        
        /* Service Edit*/
        public function edit($subServiceId)
        {
            $breadcrumbs=['title'=>'Sub Service Create','header'=>'Sub-Service','sub_header'=>'Create','sidebar'=>'sub_service_create','url'=>'sub-service/list'];
            $sub_service = SubServices::with('statusDetails')->where(['id'=>$subServiceId])->first();
            if(!empty($sub_service)){
                $sub_service = json_decode(json_encode($sub_service), true);
            }
            return view('admin.sub-service.create',['services'=>$this->services,'sub_service'=>$sub_service,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        public function save(Request $req)
        {
            $sub_service = new SubServices();
            $dat=['name'=>$req->name,
                  'service_id'=>$req->service_id,
                  'tax_percent'=>floatval($req->tax_percent),
                  'status'=>$req->status
                  ];
            if($req->image){
                $image = time().'.'.request()->image->getClientOriginalExtension();
                request()->image->move(public_path('images'), $image);
                
                $dat['image']=url('/public/images/'.$image);
            }      
            if(isset($req->id))
            {
                $id = $req->id;
               $sub_service->where(['id'=>$req->id])->update($dat);
            } else {
                $id=$sub_service->insertGetId($dat);
            }   
            echo json_encode(['status'=>'Success', 'message'=>'Sub Service details saved successfully.','data'=>$id]);
            die;
        }
        
        
        public function delete(Request $req)
        {
            $sub_service= new SubServices();
            if($sub_service->where(['id'=>$req->id])->update(['status'=>4]))
            {
            echo json_encode(['status'=>'Success', 'message'=>'Sub Service details deleted successfully.','data'=>[]]);
            die;
            }
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }
    
    
}
