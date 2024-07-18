<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductCategory;
use App\Models\Status;

class ProductCategoryController extends Controller
{
    
    private $status;
    
    public function __construct()
    {
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
    }
    
    /* Product list. */
    public function list()
        {
            $breadcrumbs=['title'=>'Product Category List','header'=>'Product Category','sub_header'=>'List','sidebar'=>'product_category_list','url'=>'product-category/list'];
            $product_cateogories = ProductCategory::with('statusDetails')->whereIn('status',[1,2])->get()->toArray();
            if(!empty($product_cateogories)){
                $product_cateogories = json_decode(json_encode($product_cateogories), true);
            }
            return view('admin.product_category.list',['product_cateogories'=>$product_cateogories,'breadcrumbs'=>$breadcrumbs]);
        }
        

    /* Product create*/
        public function create(Request $req)
        {
            $breadcrumbs=['title'=>'Product Create','header'=>'Product','sub_header'=>'Create','sidebar'=>'product_create','url'=>'product/list'];
            
            return view('admin.product_category.create',['category'=>$this->category,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
            /* Product Category view. */
        public function view($productCatId)
        {
            $breadcrumbs=['title'=>'Product Category View','header'=>'Product Category','sub_header'=>'View','sidebar'=>'product_category_view','url'=>'product-category/list'];
            $product_category = ProductCategory::with('statusDetails')->where(['id'=>$productCatId])->first();
            if(!empty($product_category)){
                $product_category = json_decode(json_encode($product_category), true);
            }
            return view('admin.product_category.view',['product_category'=>$product_category,'breadcrumbs'=>$breadcrumbs]);
        } 
        
        
        /* Product Edit*/
        public function edit($productCatId)
        {
            $breadcrumbs=['title'=>'Product Edit','header'=>'Product','sub_header'=>'Edit','sidebar'=>'product_edit','url'=>'product/list'];
            $product_category = ProductCategory::with('statusDetails')->where(['id'=>$productCatId])->first();
            if(!empty($product_category)){
                $product_category = json_decode(json_encode($product_category), true);
            }
            return view('admin.product_category.create',['product_category'=>$product_category,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        public function save(Request $req)
        {
            $product_category = new ProductCategory();
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
               $product_category->where(['id'=>$req->id])->update($dat);
            } else {
                $id=$product_category->insertGetId($dat);
            }   
            echo json_encode(['status'=>'Success', 'message'=>'Product Category details saved successfully.','data'=>$id]);
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
