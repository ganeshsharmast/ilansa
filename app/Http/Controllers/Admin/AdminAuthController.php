<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class AdminAuthController extends Controller
{
    public function getLogin(){
        return view('admin.auth.login');
    }

    public function postLogin(Request $req)
    {
        $this->validate($req, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $user = User::where(['email'=>$req->email])->first();
        // User::where(['email'=>$req->email])->update(['password' => Hash::make($req->password)]);
    
        if($user && Hash::check($req->password, $user->password))
        {
            Auth::loginUsingId($user->id);
            if(Auth::user()->role == 1){
                return redirect()->route('adminDashboard')->with('success','You are Logged in sucessfully.');
            } else {
                return back()->with('error','Whoops! you are not admin.');
            }
        }else {
            return back()->with('error','Whoops! invalid email and password.');
        }
            
    }

    public function adminLogout(Request $request)
    {
        auth()->guard('admin')->logout();
        Session::flush();
        Session::put('success', 'You are logout sucessfully');
        return redirect(route('adminLogin'));
    }
    
    /* Admin view. */
    public function profileView()
    {
        $breadcrumbs=['title'=>'Admin View','header'=>'Admin','sub_header'=>'','sidebar'=>'admin_view','url'=>'/'];
        $user = User::with('statusDetails')->where(['role'=>1])->first();
        if(!empty($user)){
            $user = json_decode(json_encode($user), true);
        }
        
        if(!empty($user)){
                $baseURL = URL::to('/');
                $image=!empty($user['image'])?$user['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $user['image'] = $image;
        }
        return view('admin.profile_view',['user'=>$user,'breadcrumbs'=>$breadcrumbs]);
    }
    
    public function changePassword(Request $req)
    {
        $breadcrumbs=['title'=>'Admin View','header'=>'Admin','sub_header'=>'','sidebar'=>'admin_view','url'=>'/'];
        $admin = (new User())->where(['id'=>1])->first();
        if(empty($admin)){
            echo json_encode(['status'=>'Error','message'=>'Admin account not exist.']);
            die;   
        }
        if ($admin->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Admin account is deleted.']);
            die;
        }
              
        $arr = ['admin_id'=>$admin->id,
                        'subject'=>'Password change', 
                        'description'=>'Password has been changed'];
        (new Notification())->insert($arr);
         return view('admin.profile_view',['user'=>$admin,'breadcrumbs'=>$breadcrumbs]);
       
    }
    
    public function updatePassword(Request $req)
    {
        if(empty($data)){
           $data=$req->all(); 
        }
        $usr = (new User())->Orwhere(['email'=>$data['email_phone'],'phone'=>$data['email_phone']]);
        $user = $usr->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        if (!Hash::check($data['old_password'], $user->password)){
            echo json_encode(['status'=>'Error','message'=>'Sorry, Old password not match.']);
            die;
        }       
        if (empty($data['password'])) {
            echo json_encode(['status'=>'Error','message'=>'Password is empty.']);
            die;
        }
        if (empty($data['confirm_password'])) {
            echo json_encode(['status'=>'Error','message'=>'Confirm password is empty.']);
            die;
        }
        if ($data['password']!=$data['confirm_password']) {
            echo json_encode(['status'=>'Error','message'=>'Password does not match.']);
            die;
        }      
        if($usr->update(['password' => Hash::make($data['password'])]))
        {
        $arr = ['user_id'=>$user->id,
                        'subject'=>'Password changed', 
                        'description'=>'Password has been changed'];
        (new Notification())->insert($arr);
        
        echo json_encode(['status'=>'Success', 'message'=>'Password changed successfully.']);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }
    
    
    public function profileEdit(Request $req)
    {
        $breadcrumbs=['title'=>'Admin Edit','header'=>'Admin','sub_header'=>'Edit','sidebar'=>'admin_view','url'=>'/'];
        $admin = (new User())->where(['id'=>1])->first();
        if(empty($admin)){
            echo json_encode(['status'=>'Error','message'=>'Admin account not exist.']);
            die;   
        }
         return view('admin.profile_edit',['user'=>$admin,'breadcrumbs'=>$breadcrumbs]);
       
    }
    
        public function profileUpdate(Request $req)
    {
        $id   = $req->get('id');
        $name = $req->get('name');
        $phone= $req->get('phone');
        $admin = (new User())->where(['id'=>$id])->update(['name'=>$name,'phone'=>$phone]);
        $admin = (new User())->where(['id'=>$id])->first();
        
        echo json_encode(['status'=>true,'message'=>'data updated successfully.','data'=>$admin]);
        die;
       
    }

}