<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductCategory;
use App\Models\Status;

class ProductController extends Controller
{
    
    private $status;
    private $category;
    
    public function __construct()
    {
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
        $this->category = (new ProductCategory())::whereIn('status',[1,2])->pluck('name','id')->toArray();
    }
    
    /* Product list. */
    public function list()
        {
            $breadcrumbs=['title'=>'Product List','header'=>'Product','sub_header'=>'List','sidebar'=>'product_list','url'=>'product/list'];
            $products = Products::with('productCategory','productStatus')->whereIn('status',[1,2])->orderBy('product_category_id')->get()->toArray();
            if(!empty($products)){
                $products = json_decode(json_encode($products), true);
            }
            return view('admin.product.list',['products'=>$products,'breadcrumbs'=>$breadcrumbs]);
        }
        

    /* Product create*/
        public function create(Request $req)
        {
            $breadcrumbs=['title'=>'Product Create','header'=>'Product','sub_header'=>'Create','sidebar'=>'product_create','url'=>'product/list'];
            
            return view('admin.product.create',['category'=>$this->category,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
            /* Product view. */
        public function view($productId)
        {
            $breadcrumbs=['title'=>'Product View','header'=>'Product','sub_header'=>'View','sidebar'=>'product_view','url'=>'product/list'];
            $product = Products::with('productCategory','productStatus')->where(['id'=>$productId])->orderBy('product_category_id')->first();
            if(!empty($product)){
                $product = json_decode(json_encode($product), true);
            }
            return view('admin.product.view',['product'=>$product,'breadcrumbs'=>$breadcrumbs]);
        } 
        
        
        /* Product Edit*/
        public function edit($productId)
        {
            $breadcrumbs=['title'=>'Product Edit','header'=>'Product','sub_header'=>'Edit','sidebar'=>'product_edit','url'=>'product/list'];
            $product = Products::with('productCategory','productStatus')->where(['id'=>$productId])->orderBy('product_category_id')->first();
            if(!empty($product)){
                $product = json_decode(json_encode($product), true);
            }
            return view('admin.product.create',['category'=>$this->category,'product'=>$product,'status'=>$this->status,'breadcrumbs'=>$breadcrumbs]);
        }
        
        public function save(Request $req)
        {
            $product = new Products();
            $dat=['product_name'=>$req->product_name,
                  'product_category_id'=>$req->product_category_id,
                  'status'=>$req->status,
                  ];
            if($req->product_image){
                $image = time().'.'.request()->product_image->getClientOriginalExtension();
                request()->product_image->move(public_path('images'), $image);
                
                $dat['product_image']=url('/public/images/'.$image);
            }      
            if(isset($req->id))
            {
                $id = $req->id;
               $product->where(['id'=>$req->id])->update($dat);
            } else {
                $id=$product->insertGetId($dat);
            }   
            echo json_encode(['status'=>'Success', 'message'=>'Product details saved successfully.','data'=>$id]);
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
