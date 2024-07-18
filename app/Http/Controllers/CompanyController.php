<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use File;
use App\Models\User;
use App\Models\Status;
use App\Models\Company;
use App\Http\Helpers\Helper;

class CompanyController extends Controller
{
    protected $status;
    protected $accType;
    
    public function __construct()
    {
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
        $this->accType = DB::table('account_type')->pluck('type_name','id')->toArray();
    }

    public function getCompanyDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $company=(new Company())::with('accountType')->where(['user_id'=>$data['user_id']])->first();
        if(empty($company)){
            echo json_encode(['status'=>'Error','message'=>'Company details not registered.']);
            die;   
        }
        if ($company->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Company account is deleted.']);
            die;
        }
        // print_r($company->toArray()['name']);
        // die;
        echo json_encode(['status'=>'Success', 'message'=>'Requested Account details fetched successfully.','data'=>$company->toArray()]);
        die;
    }

    public function updateCompanyDetails(Request $req)
    {   
        $jsonData = file_get_contents("php://input");
        
        if(1==2){
         $file_path = "webcam_captures/";
      if (!file_exists($file_path)) {
        // path does not exist
        File::makeDirectory($file_path, $mode = 0777, true, true);
        }
        $filename="";
        
        print_r($req->file('image'));
        print_r($req->all());
        print_r($jsonData);
        die("-*-");
        }
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $company = (new Company())::with('accountType')->where(['user_id'=>$data['user_id']])->first();
        $dat = ['user_id' => $data['user_id'],
                'account_type_id'=>$data['account_type_id'],
                'company_name' => $data['company_name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'ein'=>$data['ein'],
                'ein_later'=>$data['ein_later'],
                'ssn'=>$data['ssn'],
                'created_at'=>date('Y-m-d h:i:s')
                ];
        
        if(isset($data['image']) && !empty($data['image'])){
          $filename = time().'.'.request()->image->getClientOriginalExtension();
          request()->image->move(public_path('images'), $filename);
        $img=$company->image;
        if(!empty($img)){
            $img_path=public_path(str_replace(\Config("app.public"),"",$img));
            if(!empty($img_path) && File::exists($img_path)){
                File::delete($img_path);
            }
        }
            $dat['image'] = \Config("app.images").$filename;
        } 
        
        if(empty($company)){
            print_r($company);
            die;
            $dat['image'] = 'https://ilansa.shailtech.com/public/images/default.jpg';
            (new Company())->insertGetId($dat);
            $company = (new Company())::with('accountType')->where(['user_id'=>$data['user_id']])->first()->toArray();
            echo json_encode(['status'=>'Success','message'=>'Company register successfully.','data'=>$company]);
            die;   
        }
        if ($company->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }      
        $companyEmail  = $company->where(['email'=>$data['email']])->first();
        $companyPhone = $company->where(['phone'=>$data['phone']])->first();
        
        if(!empty($companyEmail) && $companyEmail->id!=$company->id){        
            echo json_encode(['status'=>'Error','message'=>'Sorry, Email already exist with different account.']);
            die;
        }
        else if(!empty($companyPhone) && $companyPhone->id!=$company->id){
            echo json_encode(['status'=>'Error','message'=>'Sorry, Phone already exist with different account.']);
            die;
        }
        
        if($company->where(['user_id'=>$data['user_id']])->update($dat))
        {
            $company = $company::with('accountType')->where(['user_id'=>$data['user_id']])->first()->toArray();
        echo json_encode(['status'=>'Success', 'message'=>'Company details updated successfully.','data'=>$company]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }


    public function list()
        {
            $breadcrumbs=['title'=>'Company List','header'=>'Company','sub_header'=>'List','sidebar'=>'company_list','url'=>'company/list'];
            $company = Company::with('statusDetails','accountType')->get()->toArray();
            if(!empty($company)){
                $company = json_decode(json_encode($company), true);
            }
            return view('admin.company.list',['companies'=>$company,'breadcrumbs'=>$breadcrumbs]);
        }
        
    public function view($companyId)
        {
            $breadcrumbs=['title'=>'Company View','header'=>'Company','sub_header'=>'View','sidebar'=>'company_view','url'=>'company/list'];
            $company = Company::with('statusDetails','accountType')->where('id',$companyId)->first();
            if(!empty($company)){
                $company = json_decode(json_encode($company), true);
            }
            return view('admin.company.view',['company'=>$company,'breadcrumbs'=>$breadcrumbs]);
        }
        
    public function edit($companyId)
        {
            $breadcrumbs=['title'=>'Company Edit','header'=>'Company','sub_header'=>'Edit','sidebar'=>'company_edit','url'=>'company/list'];
            $company = Company::with('statusDetails')->where(['id'=>$companyId])->first();
            
            if(!empty($company)){
                $company = json_decode(json_encode($company), true);
            }
            return view('admin.company.create',['company'=>$company,'status'=>$this->status,'accType'=>$this->accType,'breadcrumbs'=>$breadcrumbs]);
        }
        
    public function create()
        {
            $breadcrumbs=['title'=>'Company Create','header'=>'Company','sub_header'=>'Create','sidebar'=>'company_create','url'=>'company/list'];
            
            return view('admin.company.create',['status'=>$this->status,'accType'=>$this->accType,'breadcrumbs'=>$breadcrumbs]);
        }    
        
    public function save(Request $req)
        {
            $company= new Company();
            $dat=['company_name'=>$req->company_name,
                  'account_type_id'=>$req->account_type_id,
                  'phone'=>$req->phone,
                  'email'=>$req->email,
                  'ein'=>$req->ein,
                  'ein_later'=>(int)$req->ein_later,
                  'ssn'=>$req->ssn,
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
               $company->where(['id'=>$req->id])->update($dat);
            } else {
                $id=$company->insertGetId($dat);
            }   
            echo json_encode(['status'=>'Success', 'message'=>'Company details saved successfully.','data'=>$id]);
            die;
        }
        
        
        public function delete(Request $req)
        {
            $company= new Company();
            if($company->where(['id'=>$req->id])->update(['status'=>4]))
            {
            echo json_encode(['status'=>'Success', 'message'=>'Company record deleted successfully.','data'=>[]]);
            die;
            }
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }

    
}
