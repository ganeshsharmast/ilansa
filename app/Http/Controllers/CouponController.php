<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use File;
use App\Models\Coupons;
use App\Http\Helpers\Helper;

class CouponController extends Controller
{

        /* Coupon list. */
        public function list()
        {
            $breadcrumbs=['title'=>'Coupon List','header'=>'Coupon','sub_header'=>'List','sidebar'=>'coupon_list','url'=>'coupon/list'];
            $coupons = Coupons::with('statusDetails')->whereIn('status',[1,2])->get()->toArray();
            if(!empty($coupons)){
                $coupons = json_decode(json_encode($coupons), true);
            }
            return view('admin.coupon.list',['coupons'=>$coupons,'breadcrumbs'=>$breadcrumbs]);
        }
        
    public function view($couponId)
        {
            $breadcrumbs=['title'=>'coupon View','header'=>'Coupon','sub_header'=>'View','sidebar'=>'coupon_view','url'=>'coupon/list'];
            $coupon = Coupons::with('statusDetails')->first();
            if(!empty($coupon)){
                $coupon = json_decode(json_encode($coupon), true);
            }
            return view('admin.coupon.view',['coupon'=>$coupon,'breadcrumbs'=>$breadcrumbs]);
        }    
        
        
        public function edit($couponId)
        {
            $breadcrumbs=['title'=>'Coupon Edit','header'=>'Coupon','sub_header'=>'Edit','sidebar'=>'coupon_edit','url'=>'coupon/list'];
            $coupon = Coupons::with('statusDetails')->where(['id'=>$couponId])->first();
            if(!empty($coupon)){
                $coupon = json_decode(json_encode($coupon), true);
            }
            return view('admin.coupon.edit',['coupon'=>$coupon,'breadcrumbs'=>$breadcrumbs]);
        }
        
        public function create()
        {
            $breadcrumbs=['title'=>'Coupon Create','header'=>'Coupon','sub_header'=>'Create','sidebar'=>'coupon_create','url'=>'coupon/list'];
            return view('admin.coupon.create',['breadcrumbs'=>$breadcrumbs]);
        }
        
        public function save(Request $req)
        {
            $coupon= new Coupons();
            $dat=['name'=>$req->name,
                  'code'=>$req->code,
                  'value'=>$req->value,
                //   'status'=>$req->status
                  ];
            if(isset($req->id))
            {
                $couponId = $req->id;
                $coupon->where(['id'=>$req->id])->update($dat);
            }
            else {
                $couponId = $coupon->insertGetId($dat);
            }
            echo json_encode(['status'=>'Success', 'message'=>'Coupon details saved successfully.','data'=>$couponId]);
            die;
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }
        
        
        public function delete(Request $req)
        {
            $coupon= new Coupons();
            if($coupon->where(['id'=>$req->id])->update(['status'=>4]))
            {
            echo json_encode(['status'=>'Success', 'message'=>'Coupon details deleted successfully.','data'=>[]]);
            die;
            }
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }


    
}
