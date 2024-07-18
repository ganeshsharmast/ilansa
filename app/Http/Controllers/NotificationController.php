<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Notification;
use App\Http\Helpers\Helper;

use Twilio\Rest\Client;


class NotificationController extends Controller
{
    
    public function __construct()
    {
        
    }

    /* Notificaiton list. */
    public function getNotificationList(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(!isset($data['user_id'])){
          echo json_encode(['status'=>'Error','message'=>'User-id not set.']);
          die;   
        }
        $user_id = str_replace("'","",str_replace('"',"",$data['user_id']));
        $user = (new User())->where(['id'=>$user_id])->first();
        
        if(empty($user)){
          echo json_encode(['status'=>'Error','message'=>'User-id not exist.']);
          die;   
        }
        if ($user->status!=1) {
            echo json_encode(['status'=>'Error','message'=>'Your account is not active.']);
            die;
        }
        $noti = Notification::with(['userDetails'=>function($query){
            return $query->select('id','name','email','status');
            }])->where(['user_id'=>$user_id])->whereIn('status',[1,2])->get()->toArray();
        
        echo json_encode(['status'=>'Success', 'message'=>'Notitification list fetched successfully.','data'=>$noti]);
        die;
    }
    
    
    public function details(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(!isset($data['notification_id'])){
          echo json_encode(['status'=>'Error','message'=>'Notification-id is missing.']);
          die;   
        }
        $notification_id = str_replace("'","",str_replace('"',"",$data['notification_id']));
        $noti = (new Notification())::with(['userDetails'=>function($query){
            return $query->select('id','name','email','status');
            }])->where(['id'=>$notification_id])->whereIn('status',[1,2])->first();
        
        if(empty($noti)){
          echo json_encode(['status'=>'Error','message'=>'Notification-id not exist.']);
          die;   
        }
        $noti = $noti->toArray();
        echo json_encode(['status'=>'Success', 'message'=>'Notitification details fetched successfully.','data'=>$noti]);
        die;
    }

    public function read(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(!isset($data['notification_id'])){
          echo json_encode(['status'=>'Error','message'=>'Notification-id is missing.']);
          die;   
        }
        $notification_id = str_replace("'","",str_replace('"',"",$data['notification_id']));
        $read = str_replace("'","",str_replace('"',"",$data['read']));
        $noti = (new Notification())->where(['id'=>$notification_id])->whereIn('status',[1,2])->first();
        
        if(empty($noti)){
          echo json_encode(['status'=>'Error','message'=>'Notification-id not exist.']);
          die;   
        }
        Notification::where(['id'=>$notification_id])->update(['read'=>$read]);
        
        echo json_encode(['status'=>'Success', 'message'=>'Notitification read status updated successfully.','data'=>$data]);
        die;
    }


    public function statusUpdate(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(!isset($data['notification_id'])){
          echo json_encode(['status'=>'Error','message'=>'Notification-id is missing.']);
          die;   
        }
        $notification_id = str_replace("'","",str_replace('"',"",$data['notification_id']));
        $status = str_replace("'","",str_replace('"',"",$data['status']));
        $noti = (new Notification())->where(['id'=>$notification_id])->whereIn('status',[1,2])->first();
        
        if(empty($noti)){
          echo json_encode(['status'=>'Error','message'=>'Notification-id not exist.']);
          die;   
        }
        Notification::where(['id'=>$notification_id])->update(['status'=>$status]);
        
        echo json_encode(['status'=>'Success', 'message'=>'Notitification details updated successfully.','data'=>$data]);
        die;
    }



    
        /* Notificaiton list. */
        public function list(Request $req)
        {
            $breadcrumbs=['title'=>'Notification List','header'=>'Notification','sub_header'=>'List','sidebar'=>'notification_list','url'=>'notification/list'];
            $notifications = Notification::with(['statusDetails','userDetails'=>function($query){
                return $query->select('id','name','email','status');
                }])->where(['user_id'=>3])->whereIn('status',[1,2])->get()->toArray();
            
            return view('admin.notification.list',['notifications'=>$notifications,'breadcrumbs'=>$breadcrumbs]);
        }
    
        /* Notification View*/
        public function view($notiId)
        {
            $breadcrumbs=['title'=>'Notification View','header'=>'Notification','sub_header'=>'View','sidebar'=>'notification_view','url'=>'notification/list'];
            
            $notification = Notification::with(['statusDetails','userDetails'=>function($query){
                return $query->select('id','name','email','status');
                }])->where(['id'=>$notiId])->whereIn('status',[1,2])->first();
            
            if(!empty($notification)){
                $notification = json_decode(json_encode($notification), true);
            }
            
            return view('admin.notification.view',['notification'=>$notification,'breadcrumbs'=>$breadcrumbs]);
        }
    
    
}
