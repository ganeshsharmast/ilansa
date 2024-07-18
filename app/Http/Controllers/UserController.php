<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email;
use File;
use App\Models\User;
use App\Models\Status;
use App\Models\Orders;
use App\Models\UserServiceRequest;
use App\Models\UserFavorite;
use App\Models\Notification;
use App\Http\Helpers\Helper;

use Twilio\Rest\Client;


class UserController extends Controller
{
    public $twilio;
    private $role;
    private $status;
    
    public function __construct()
    {
        $this->twilio = Config("app.twilio");
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
        $this->role = DB::table('role')->whereIn('id',[2,3])->pluck('name','id')->toArray();

    }

    public function register(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $user= new User();
        $usrCount = (new User())->Orwhere(['email'=>$data['email'],'phone'=>$data['phone']])->count();
        if($usrCount>0){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone already exist.']);
            die;   
        }           
        $user->device_token = $data['device_token'];
        $user->device_type = $data['device_type'];
        $user->role = $data['role'];
        $user->role_active = $data['role'];
        $user->name = explode('@',$data['email'])[0];
        $user->email = $data['email'];
        $user->country_code = $data['country_code'];
        $user->phone = $data['phone'];
        $user->refer_code = $data['refer_code'];
        $user->phone_otp = Helper::generateOTPCode();
        $user->email_otp = Helper::generateOTPCode();
        $user->status = 1;
        $user->password = Hash::make($data['password']);
        $code = Helper::generateReferralCode();
        $user->referral_code = $code;
        $user->remember_token=md5($code);
        if(isset($data['refer_code']) && !empty($data['refer_code'])){
            $user->refer_code = $data['refer_code'];
        }
        if(isset($data['image']) && !empty($data['image'])){
          $filename = time().'.'.request()->image->getClientOriginalExtension();
request()->image->move(public_path('images'), $filename);

            $user->image = \Config("app.images").$filename;
        } else {
            $user->image = \Config("app.images").'default.jpg';
        }
        if($user->save())
        {
            $dat = $user;
            $message = "Welcome, In the Ilansa Service portal. Your 6-digit OTP is $user->phone_otp. Please use it for verify your phone number.";
            $this->sendSMS($dat->country_code."".$dat->phone, $message);
            $status  = 'Success';
            $message = 'User saved successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Error';
          $message = 'Something missing.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }

    public function login(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }

        $user = (new User())->Orwhere(['email'=>$data['email_phone'],'phone'=>$data['email_phone']])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone not exist.']);
            die;   
        }
        if (!Hash::check($data['password'], $user->password)) {
            echo json_encode(['status'=>'Error','message'=>'Your password mismatch.']);
            die;
        }
            (new User())->where(['id' => $user->id])
                        ->update(['device_type' => $data['device_type'], 'device_token'=>$data['device_token'],
                            'updated_at'=>date('Y-m-d h:i:s'),
                            'role_active'=>$data['role_active']]);
        if ($user->status!=1) {
            echo json_encode(['status'=>'Error','message'=>'Your account is not active.']);
            die;
        }
        $user = (new User())->findOrFail($user->id);
        if(!empty($user)){
            $user = $user->toArray();
            $user_id = (string)$user['id'];
            $user['id'] = "$user_id";
        }
        echo json_encode(['status'=>'Success', 'message'=>'Your login done successfully.','data'=>$user]);
        die;
    }
    

        public function switchRole(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = (new User())->where(['id'=>$data['user_id']])->first();
        
        if(empty($user)){
          echo json_encode(['status'=>'Error','message'=>'User-id not exist.']);
          die;   
        }
           (new User())->where(['id' => $user->id])
                        ->update(['role_active'=>$data['role_active']]);
        if ($user->status!=1) {
            echo json_encode(['status'=>'Error','message'=>'Your account is not active.']);
            die;
        }
        $user = (new User())->findOrFail($user->id);
        $user_id='';
        if(!empty($user)){
            $user = $user->toArray();
            $user_id = (string)$user['id'];
            $user['id'] = "$user_id";
        }
        echo json_encode(['status'=>'Success', 'message'=>'Your active role changed successfully.','data'=>$user]);
        die;
    }
    
    public function forgetPassword(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $user = (new User())->Orwhere(['email'=>$data['email_phone'],'phone'=>$data['email_phone']])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        $phoneOtp = Helper::generateOTPCode();
        $emailOtp = Helper::generateOTPCode();
        $usr = (new User());
        $usr->where(['id'=>$user->id])->update(['phone_otp' => $phoneOtp,
        'email_otp' => $emailOtp]);
        $user = $usr->where(['id'=>$user->id])->first();
        if(!empty($user)){
            $user = $user->toArray();
            $user_id = (string)$user['id'];
            $user['id'] = "$user_id";
        }
        $message = "Welcome, In the Ilansa Service portal. Your 6-digit OTP is $phoneOtp. Please use it for resetting password.";
        $this->sendSMS($user['country_code']."".$user['phone'], $message);

        echo json_encode(['status'=>'Success', 'message'=>'OTP sent successfully on your mobile.','data'=>$user]);
        die;
    }

   public function sendSMS($phone, $message)
   {
       $sid   = $this->twilio['key'];
        $token = $this->twilio['secret'];
        $twilio = new Client($sid, $token);

        $message = $twilio->messages->create($phone, // to
            [
              "from" => "+12133547206",
              "body" => $message
            ]
          );
       
   }

    public function resetPassword(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
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
        if (empty($data['otp_code']) || strlen($data['otp_code'])!=6 || !in_array($data['otp_code'],[$user->email_otp,$user->phone_otp])) {
            echo json_encode(['status'=>'Error','message'=>'Invalid OTP code inserted.']);
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
        
        if($usr->update(['password' => Hash::make($data['password']),
                         'phone_otp' => Helper::generateOTPCode(),
                         'email_otp' => Helper::generateOTPCode()
        ]))
        {
            $user = $usr->where(['id'=>$user->id])->first();
            if(!empty($user)){
                $user = $user->toArray();
                $arr = ['user_id'=>$user['id'],
                        'subject'=>'Reset password done', 
                        'description'=>'Your password updated recently'];
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
                (new Notification())->insert($arr);
            }
        echo json_encode(['status'=>'Success', 'message'=>'Password reset successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }

    public function verifyEmailPhone(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $usr = (new User())->orWhere(['email'=>$data['email_phone'],'phone'=>$data['email_phone']]);
        $user = $usr->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Inserted Email or Phone not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        if (empty($data['otp_code']) || strlen($data['otp_code'])!=6 || !in_array($data['otp_code'],[$user->email_otp, $user->phone_otp])) {
            echo json_encode(['status'=>'Error','message'=>'Invalid OTP code inserted.']);
            die;
        } 
        $response=0;
        $User = (new User())->orWhere(['email'=>$data['email_phone'],'phone'=>$data['email_phone']])->first();
        if(!empty($User)){
            $User = $User->toArray();
            $user_id = (string)$User['id'];
            $User['id'] = "$user_id";
        }
        if($data['otp_code']==$user->phone_otp && (new User())->where(['id'=>$user->id])->update(['phone_otp'=>Helper::generateOTPCode(), 'phone_verified_at' => date('Y-m-d h:i:s')
            ])){
            $arr = ['user_id'=>$user->id,
                    'subject'=>'Phone verified', 
                    'description'=>'Phone verified successfully'];
            (new Notification())->insert($arr);
            
            echo json_encode(['status'=>'Success', 'message'=>'Phone verified successfully.','data'=>$User]);
            die;   
        }                
        else if($data['otp_code']==$user->email_otp && (new User())->where(['id'=>$user->id])->update(['email_otp'=>Helper::generateOTPCode(), 'email_verified_at' => date('Y-m-d h:i:s')
                         ]))
        {           
            $arr = ['user_id'=>$user->id,
                    'subject'=>'Email verified', 
                    'description'=>'Email verified successfully'];
            (new Notification())->insert($arr);
        echo json_encode(['status'=>'Success', 'message'=>'Email verified successfully.','data'=>$User]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }

    public function changePassword(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
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


    public function changeLanguage(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $usr = (new User())->where(['id'=>$data['user_id']]);
        $user = $usr->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User ID not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        if (empty($data['language'])) {
            echo json_encode(['status'=>'Error','message'=>'Language is empty.']);
            die;
        }      
        if($usr->update(['language' => $data['language']]))
        {
            $user = (new User())->findOrFail($data['user_id']);
            if(!empty($user)){
                $user = $user->toArray();
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
            }
        echo json_encode(['status'=>'Success', 'message'=>'Language changed successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }

    public function updateAvailability(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $usr = (new User())->where(['id'=>$data['user_id']]);
        $user = $usr->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User ID not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        if (!isset($data['availability'])) {
            echo json_encode(['status'=>'Error','message'=>'Availability is not set.']);
            die;
        }      
        if($usr->update(['availability' => $data['availability']]))
        {
            $user = (new User())->findOrFail($data['user_id']);
            if(!empty($user)){
                $user = $user->toArray();
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
            }
        echo json_encode(['status'=>'Success', 'message'=>'Availability changed successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }

    public function getProfileDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = (new User())->Orwhere(['id'=>$data['user_id']])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User details not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Requested account is deleted.']);
            die;
        }
        if(!empty($user)){
            $user = $user->toArray();
            $user_id = (string)$user['id'];
            $user['id'] = "$user_id";
        }
        echo json_encode(['status'=>'Success', 'message'=>'Requested Account details fetched successfully.','data'=>$user]);
        die;
    }

    public function updateProfileDetails(Request $req)
    {   
        $jsonData = file_get_contents("php://input");
        $data=json_decode($jsonData,true);
        
        if(empty($data)){
           $data=$req->all(); 
        }
        if(empty($data)){
            $data=$_POST;
        }
        
        $usr = (new User());
        $user_id=str_replace("\n","",str_replace("'","",str_replace('"',"",trim($data['user_id']))));
        
        $user = $usr->where(['id'=>$user_id])->first();

        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }      
        $usrMail  = $usr->where(['email'=>$data['email']])->first();
        $usrPhone = $usr->where(['phone'=>$data['phone']])->first();
        
        if(!empty($usrMail) && $usrMail->id!=$user->id){        
            echo json_encode(['status'=>'Error','message'=>'Sorry, Email already exist with different account.']);
            die;
        }
        else if(!empty($usrPhone) && $usrPhone->id!=$user->id){
            echo json_encode(['status'=>'Error','message'=>'Sorry, Phone already exist with different account.']);
            die;
        }
        $dat = ['name' => $data['name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'address'=>@$data['address'],
                'updated_at'=>date('Y-m-d h:i:s')];
        if(!empty($data['area_radius'])){
            $dat['area_radius'] = $data['area_radius'];
        }

        if(isset($data['base64']) && !empty($data['base64'])){
                
            $image = "/public/image/".time().".jpg";
            $file_path = dirname(__DIR__,3).$image;
            $ifp = fopen("$file_path", 'w' ); 
            $data = explode( ',', $data['base64']);
            
            // we could add validation here with ensuring count( $data ) > 1
            fwrite( $ifp, base64_decode( $data[ 1 ] ) );
        
            // clean up the file resource
            fclose( $ifp ); 
            $dat['image']=url($image);
        } 
        
        if($usr->where(['id'=>$user_id])->update($dat))
        {
            $user = $usr->where(['id'=>$user_id])->first();
            if(!empty($user)){
                $user = $user->toArray();
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
            }
        echo json_encode(['status'=>'Success', 'message'=>'Profile details updated successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }


    public function updateProfileAlerts(Request $req)
    {        
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $usr = (new User());
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = $usr->where(['id'=>$data['user_id']])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }      
        
        if(empty($data['order_alert'])){        
            echo json_encode(['status'=>'Error','message'=>'Order alert is empty.']);
            die;
        }
        else if(empty($data['update_sms_alert'])){        
            echo json_encode(['status'=>'Error','message'=>'Update SMS alert is empty.']);
            die;
        }
        else if(empty($data['update_email_alert'])){        
            echo json_encode(['status'=>'Error','message'=>'Update Email alert is empty.']);
            die;
        }
        else if(empty($data['promotion_sms_alert'])){        
            echo json_encode(['status'=>'Error','message'=>'promotion SMS alert is empty.']);
            die;
        }
        else if(empty($data['promotion_email_alert'])){        
            echo json_encode(['status'=>'Error','message'=>'promotion Email alert is empty.']);
            die;
        }
        $dat = ['order_alert' => $data['order_alert'],'update_sms_alert'=>$data['update_sms_alert'],
                 'update_email_alert'=>$data['update_email_alert'], 'promotion_sms_alert'=>$data['promotion_sms_alert'],
                 'promotion_email_alert'=>$data['promotion_email_alert']
                ];
        if($usr->where(['id'=>$data['user_id']])->update($dat))
        {
            $user = $usr->where(['id'=>$data['user_id']])->first();
            if(!empty($user)){
                $user = $user->toArray();
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
            }
        echo json_encode(['status'=>'Success', 'message'=>'Profile App alerts updated successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }

    public function updateDeviceDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $usr = (new User());
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = $usr->where(['id'=>$data['user_id']])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'Email or Phone not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }      
        
        if($usr->where(['id'=>$data['user_id']])->update(['device_type' => $data['device_type'],'device_token'=>$data['device_token'],'updated_at'=>date('Y-m-d h:i:s')]))
        {
            $user = $usr->where(['id'=>$data['user_id']])->first();
            if(!empty($user)){
                $user = $user->toArray();
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
            }
        echo json_encode(['status'=>'Success', 'message'=>'Device details updated successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }
    
    
    /*
    Update longitude and latitude
    */
    public function updateLongLat(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $usr = (new User())->where(['id'=>$data['user_id']]);
        $user = $usr->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User ID not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        if (!isset($data['longitude']) || empty($data['longitude'])) {
            echo json_encode(['status'=>'Error','message'=>'Longitude is not proper set.']);
            die;
        } 
        else if (!isset($data['latitude']) || empty($data['latitude'])) 
        {
            echo json_encode(['status'=>'Error','message'=>'Latitude is not proper set.']);
            die;
        }      
        if($usr->update(['longitude' => $data['longitude'],'latitude' => $data['latitude']]))
        {
            $user = (new User())->findOrFail($data['user_id']);
            if(!empty($user)){
                $user = $user->toArray();
                $user_id = (string)$user['id'];
                $user['id'] = "$user_id";
            }
        echo json_encode(['status'=>'Success', 'message'=>'Longitude and Latitude changed successfully.','data'=>$user]);
        die;
        }
        echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
        die;
    }
    
    /*
    Get longitude and latitude
    */
    public function getLongLat(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $data['user_id']=str_replace("'","",str_replace('"',"",$data['user_id']));
        $usr = (new User())->where(['id'=>$data['user_id']]);
        $user = $usr->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User ID not exist.']);
            die;   
        }
        if ($user->status==4) {
            echo json_encode(['status'=>'Error','message'=>'Sorry, Your account is deleted.']);
            die;
        }
        $user = (new User())->select('id','name','longitude','latitude')->findOrFail($data['user_id']);
        
        if(!empty($user)){
            $user = $user->toArray();
            $user_id = (string)$user['id'];
            $user['id'] = "$user_id";
        } else {
            echo json_encode(['status'=>'Error','message'=>'Something missing.']);
            die;
        }
        echo json_encode(['status'=>'Success', 'message'=>'Longitude and Latitude fetched successfully.','data'=>$user]);
        die;
    }
    
    
    public function sendMail()
    { 
        $id=5;
    
    $user = User::find($id)?? auth()->user();
$order = Orders::find($id);

    $mail = new Email();
    $email = $user->email;
    // $email = 'ganeshsharmast@gmail.com';
    // $mail->to($email)->send(new Email());
    // Mail::to($user->email)->send(new Email());
    $msg = 'Test email sent!';
    // return $msg;
$response = mail("ganeshsharmast@gmail.com","My subject",$msg);;
print_r($response);
die("***");
    }

    
    /* Make User Favorite */
     public function makeUserFavorite(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $status='';
        $user_id     = str_replace("'","",str_replace('"',"",$data['user_id']));
        $provider_id = str_replace("'","",str_replace('"',"",$data['provider_id']));
        if(isset($data['status']) || !empty($data['status'])){
          $status = str_replace("'","",str_replace('"',"",$data['status']));   
        } 
        $user = (new User())->where(['id'=>$user_id])->first();
        $provider = (new User())->where(['id'=>$provider_id])->first();
        
        if(empty($user)){
          echo json_encode(['status'=>'Error','message'=>'User-id not exist.']);
          die;   
        }
        if(empty($provider)){
          echo json_encode(['status'=>'Error','message'=>'Provider-id not exist.']);
          die;   
        }
        if ($user->status!=1) {
            echo json_encode(['status'=>'Error','message'=>'Your account is not active.']);
            die;
        }
        if ($provider->status!=1) {
            echo json_encode(['status'=>'Error','message'=>'Provider account is not active.']);
            die;
        }
        if ($provider_id==$user_id) {
            echo json_encode(['status'=>'Error','message'=>'User-Id and Provider-Id cannot be same.']);
            die;
        }
        $recordExist = (new UserFavorite())->where(['user_id' => $user_id,'provider_id' => $provider_id])->first();
        
        if(!empty($recordExist) && $status==1){
          echo json_encode(['status'=>'Error','message'=>'Provider-id is already set to favorite.']);
          die;   
        } else if(empty($recordExist) && $status==''){
            die("kfc");
          echo json_encode(['status'=>'Error','message'=>'Record not exist.']);
          die;   
        }
        if($status==1){
            $result = (new UserFavorite())->insert(['user_id' => $user_id,'provider_id' => $provider_id]);
            if(!empty($result)){
                echo json_encode(['status'=>'Success', 'message'=>'Provider-Id is set to be favorite.','data'=>$data]);
                die;
            }    
        } else if($status!=1){
            
            $result = (new UserFavorite())->where(['user_id' => $user_id,'provider_id' => $provider_id])->delete();
            if(!empty($result)){
                echo json_encode(['status'=>'Success', 'message'=>'Provider-Id is unset to be favorite.','data'=>$data]);
                die;
            }    
        } 
        echo json_encode(['status'=>'Error', 'message'=>'Something is missing.','data'=>$data]);
        die;
    }
    
    
    
    /* User Service Requests. */
     public function makeUserServiceRequest(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        
        if(empty($data['user_id'])){
            echo json_encode(['status'=>'Error','message'=>'User Id is missing.']);
            die;
        } 
        if(empty(trim($data['message']))){
            echo json_encode(['status'=>'Error','message'=>'Message data is missing.']);
            die;
        }
        $user_id     = str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = DB::table('users')->where(['id'=>$user_id])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User-Id not exist in the records.']);
            die;
        }
        if($user->status!=1){
            echo json_encode(['status'=>'Error','message'=>'Sorry, This user is not acitve.']);
            die;
        }
            $request= new UserServiceRequest();
            $request->user_id = trim($data['user_id']);
            $request->message = trim($data['message']);
            $request->status=1;
            
        if($request->save())
           {
            $status  = 'Success';
            $message = 'Service request send successfully.';
        } else 
        { 
          $status = 'Error';
          $message = 'Something missing in sending service request.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$data]);
        die;
    }
    
    /* User Service Requests. */
     public function getUserServiceRequestList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(empty($data['user_id'])){
            echo json_encode(['status'=>'Error','message'=>'User Id is missing.']);
            die;
        } 
        $user_id     = str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = DB::table('users')->where(['id'=>$user_id])->first();
        if(empty($user)){
            echo json_encode(['status'=>'Error','message'=>'User-Id not exist in the records.']);
            die;
        }
        if($user->status!=1){
            echo json_encode(['status'=>'Error','message'=>'Sorry, This user is not acitve.']);
            die;
        }
            $requests = UserServiceRequest::with('statusDetails')->where(['user_id'=>$user_id])->get()->toArray();
            
            
        echo json_encode(['status'=>'Success', 'message'=>'Service request list found successfully.','data'=>$requests]);
        die;
    }
    
        /* Get User Service request details. */
         public function getUserServiceRequestDetail(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(empty($data['request_id'])){
            echo json_encode(['status'=>'Error','message'=>'User Id is missing.']);
            die;
        } 
        $request_id  = str_replace("'","",str_replace('"',"",$data['request_id']));
        $request = UserServiceRequest::with('statusDetails')->where(['id'=>$request_id])->first();
        if(!empty($request)){
            $request = $request->toArray();
        }
            
        echo json_encode(['status'=>'Success', 'message'=>'Service request details found successfully.','data'=>$request]);
        die;
    }
    
    
    /* Get Update Service request details. */
    public function updateServiceRequestStatus(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(empty($data['request_id'])){
         echo json_encode(['status'=>'Error','message'=>'User Id is missing.']);
            die;
        }
        
        $request_id  = str_replace("'","",str_replace('"',"",$data['request_id']));
        $status  = str_replace("'","",str_replace('"',"",$data['status']));
        $request = (new UserServiceRequest())->where(['id'=>$request_id])->first();
        if(empty($request)){
         echo json_encode(['status'=>'Error','message'=>'Request Id is not found.']);
            die;
        }else {
            (new UserServiceRequest())->where(['id'=>$request_id])->update(['status'=>$status]);    
        }
        
        echo json_encode(['status'=>'Success', 'message'=>'Service request updated successfully.','data'=>$data]);
        die;
    }
    
    
    /* Users list. */
    public function userList()
    {
        $breadcrumbs=['title'=>'Users List','header'=>'Users','sub_header'=>'List','sidebar'=>'user_list','url'=>'users/list'];
        $users = User::with('statusDetails')->where(['role'=>3])->get()->toArray();
        if(!empty($users)){
            $users = json_decode(json_encode($users), true);
        }
        
        if(!empty($users)){
            foreach($users as $key=>$user){
                $baseURL = URL::to('/');
                $image=!empty($user['image'])?$user['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $users[$key] = $user;
            }   
        }
        return view('admin.user.list',['users'=>$users,'breadcrumbs'=>$breadcrumbs]);
    }
    
     /* User view. */
    public function userView($userId)
    {
        $breadcrumbs=['title'=>'User View','header'=>'User','sub_header'=>'View','sidebar'=>'user_view','url'=>'users/list'];
        $user = User::with('statusDetails')->where(['id'=>$userId])->first();
        if(!empty($user)){
            $user = json_decode(json_encode($user), true);
        }
        
        if(!empty($user)){
                $baseURL = URL::to('/');
                $image=!empty($user['image'])?$user['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $user['image'] = $image;
        }
        return view('admin.user.view',['user'=>$user,'breadcrumbs'=>$breadcrumbs]);
    }
    
    
    
    
    
    /* Providers list. */
    public function providerList()
    {
        $breadcrumbs=['title'=>'Providers List','header'=>'Providers','sub_header'=>'List','sidebar'=>'provider_list','url'=>'providers/list'];
        $providers = User::with('statusDetails')->where(['role'=>2])->get()->toArray();
        if(!empty($providers)){
            $providers = json_decode(json_encode($providers), true);
        }
        
        if(!empty($providers)){
            foreach($providers as $key=>$provider){
                $baseURL = URL::to('/');
                $image=!empty($provider['image'])?$provider['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $providers[$key] = $provider;
            }   
        }
        return view('admin.provider.list',['providers'=>$providers,'breadcrumbs'=>$breadcrumbs]);
    }
    
     /* Providers view. */
    public function providerView($providerId)
    {
        $breadcrumbs=['title'=>'Provider View','header'=>'Provider','sub_header'=>'View','sidebar'=>'provider_view','url'=>'provider/list'];
        $provider = User::with('statusDetails')->where(['id'=>$providerId])->first();
        if(!empty($provider)){
            $provider = json_decode(json_encode($provider), true);
        }
        
        if(!empty($provider)){
                $baseURL = URL::to('/');
                $image=!empty($provider['image'])?$provider['image']:($baseURL.(empty(strpos($baseURL,'public'))?'/public':'').'/images/'.'default.jpg');
                
                $provider['image'] = $image;
        }
        return view('admin.provider.view',['provider'=>$provider,'breadcrumbs'=>$breadcrumbs]);
    }
    
    public function edit($userId)
        {
            $user = User::with('statusDetails')->where(['id'=>$userId])->first();
            
            if(!empty($user))
            {
                $user = json_decode(json_encode($user), true);
                $role=$user['role'];
                if($role==2)
                {
                    $breadcrumbs=['title'=>'Provider Edit','header'=>'Provider','sub_header'=>'Edit','sidebar'=>'provider_edit','url'=>'provider/list'];
                    return view('admin.provider.create',['user'=>$user,'status'=>$this->status,'role'=>$this->role,'breadcrumbs'=>$breadcrumbs]);
                } elseif($role==3)
                {
                    $breadcrumbs=['title'=>'User Edit','header'=>'User','sub_header'=>'Edit','sidebar'=>'user_edit','url'=>'user/list'];
                    return view('admin.user.create',['user'=>$user,'status'=>$this->status,'role'=>$this->role,'breadcrumbs'=>$breadcrumbs]);
                }
                
                
            } else {
                echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
                die;
            }
            
            
            if($user->role)
            
            $user = User::with('statusDetails')->where(['id'=>$userId])->first();
            
            if(!empty($user)){
                $user = json_decode(json_encode($user), true);
            }
            
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
