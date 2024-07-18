<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use File;
use App\Models\User;
use App\Models\Status;
use App\Models\Orders;
use App\Models\Chat;
use App\Models\ChatContent;
use App\Http\Helpers\Helper;

class ChatController extends Controller
{
    protected $status;
    protected $accType;
    
    public function __construct()
    {
        $this->status = (new Status())::where('type',0)->pluck('name','id')->toArray();
    }


    public function supportChats()
        {
            $breadcrumbs=['title'=>'Chat List','header'=>'Chat','sub_header'=>'List','sidebar'=>'chat_list','url'=>'chat/list'];
            $chats = Chat::with(['receiverDetails'=>function($query){
                return $query->select('id','name');
            }])->with(['userDetails'=>function($query){
                return $query->select('id','name');
            }])
            ->with('statusDetails')->where(['receiver_id'=>1])
            ->orWhere(['sender_id'=>1])->get()->toArray();
            
            if(!empty($chats)){
                $chats = json_decode(json_encode($chats), true);
            }
            return view('admin.chat.support_list',['chats'=>$chats,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
    public function memberChats()
        {
            $breadcrumbs=['title'=>'Chat List','header'=>'Chat','sub_header'=>'List','sidebar'=>'chat_list','url'=>'chat/list'];
            $chats = Chat::with(['receiverDetails'=>function($query){
                return $query->select('id','name');
            }])->with(['userDetails'=>function($query){
                return $query->select('id','name');
            }])
            ->with('statusDetails')->whereNotIn('receiver_id',[1])
            ->whereNotIn('sender_id',[1])->get()->toArray();
            
            if(!empty($chats)){
                $chats = json_decode(json_encode($chats), true);
            }
            return view('admin.chat.member_list',['chats'=>$chats,'breadcrumbs'=>$breadcrumbs]);
        }    
        
    public function view($chatId)
        {
            $breadcrumbs=['title'=>'Chat List','header'=>'Chat','sub_header'=>'view','sidebar'=>'chat_view','url'=>'chat/list'];
            $chat = Chat::with(['receiverDetails'=>function($query){
                return $query->select('id','name');
                    }])->with(['userDetails'=>function($query){
                return $query->select('id','name');
                    }])
                    ->with(['chatContents'=>function($query){
                return $query->select('id','chat_id','sender_id','receiver_id','message','created_at');
                    }])
                    ->with('statusDetails')->where(['id'=>$chatId])->first();
            
            if(!empty($chat)){
                $chat = json_decode(json_encode($chat), true);
            }
            return view('admin.chat.view',['chat'=>$chat,'breadcrumbs'=>$breadcrumbs]);
        }
        
        public function make($chatId)
        {
            $breadcrumbs=['title'=>'Chat List','header'=>'Chat','sub_header'=>'view','sidebar'=>'chat_view','url'=>'chat/list'];
            $chat = Chat::with(['receiverDetails'=>function($query){
                return $query->select('id','name');
                    }])->with(['userDetails'=>function($query){
                return $query->select('id','name');
                    }])
                    ->with(['chatContents'=>function($query){
                return $query->select('id','chat_id','sender_id','receiver_id','message','created_at');
                    }])
                    ->with('statusDetails')->where(['id'=>$chatId])->first();
            
            if(!empty($chat)){
                $chat = json_decode(json_encode($chat), true);
            }
            return view('admin.chat.make',['chat'=>$chat,'breadcrumbs'=>$breadcrumbs]);
        }
        
        
        public function delete($chatId)
        {
            $chat= new Chat();
            if($chat->where(['id'=>$chatId])->update(['status'=>4]))
            {
            echo json_encode(['status'=>'Success', 'message'=>'Chat record deleted successfully.','data'=>[]]);
            die;
            }
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }
        
        
        public function save(Request $req)
        {
            $chatId  = trim(@$req->id);
            $message = trim(@$req->message);
            $receiverId = trim(@$req->receiver_id);
            if(empty($chatId)){
            echo json_encode(['status'=>'Error','message'=>'Chat-Id is empty.']);
            die;
            }
            if(empty($message)){
            echo json_encode(['status'=>'Error','message'=>'Chat message is empty.']);
            die;
            }
            if(empty($receiverId)){
            echo json_encode(['status'=>'Error','message'=>'Receiver Id is missing.']);
            die;
            }
            $chat= (new Chat())->where(['id'=>$chatId])->first();
            if(empty($chat)){
            echo json_encode(['status'=>'Error','message'=>'Chat-Id is empty.']);
            die;
            }
            $chat = json_decode(json_encode($chat),true);
            $cont = ['chat_id'=>$chatId,
                    'sender_id'=>1,
                    'receiver_id'=>$receiverId,
                    'message'=>$message,
                    'created_at'=>date('Y-m-d h:i:s')
                    ];
           
            if((new ChatContent())->insert($cont))
            {
            echo json_encode(['status'=>'Success', 'message'=>'Chat message insert successfully.','data'=>$cont]);
            die;
            }
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }
        
        
        public function deleteChat(Request $req)
        {
            $jsonData = file_get_contents("php://input");
            $data = json_decode($jsonData,true);
            if(empty($data)){
               $data = $req->all(); 
            }
            $chatId=str_replace("'","",str_replace('"',"",@$data['chat_id']));
            if(empty($chatId)){
            echo json_encode(['status'=>'Error','message'=>'Chat-Id is empty.']);
            die;
            }
            $Chat = (new Chat())->where(['id'=>$chatId])->first();
            
            if(empty($Chat)){
            echo json_encode(['status'=>'Error','message'=>'Chat-Id not exist.']);
            die;
            }
            $Chat= $Chat->toArray();
            $chat= (new Chat())->where(['id'=>$chatId]);
            $userId=str_replace("'","",str_replace('"',"",@$data['user_id']));
            if(empty($userId)){
            echo json_encode(['status'=>'Error','message'=>'User-Id is empty.']);
            die;
            }
            if($Chat['sender_id']==$userId || $Chat['receiver_id']==$userId)
            {
                (new ChatContent())->where(['chat_id'=>$chatId,'receiver_id'=>$userId])->update(['receiver_status'=>4]);
                (new ChatContent())->where(['chat_id'=>$chatId,'sender_id'=>$userId])->update(['sender_status'=>4]);
                echo json_encode(['status'=>'Success', 'message'=>'User Chat record deleted successfully..','data'=>[]]);
                    die;
            }
            else {
                echo json_encode(['status'=>'Error','message'=>'User-Id not exist with chat.']);
                die;
            }
            
            echo json_encode(['status'=>'Error', 'message'=>'Something missing.']);
            die;
        }
     
        
        public function getChatList(Request $req)
        {
            $jsonData = file_get_contents("php://input");
            $data = json_decode($jsonData,true);
            if(empty($data)){
               $data=$req->all(); 
            }
            $userId=str_replace("'","",str_replace('"',"",$data['user_id']));
            $chats= Chat::with('chatContents')->where(['chats.status'=>1])
                    ->where(function($query) use($userId) {
                         return $query->orWhere(['chats.sender_id'=>$userId,'chats.receiver_id'=>$userId]);
                         })
            ->leftJoin('users as s','s.id','chats.sender_id')
            ->leftJoin('users as r','r.id','chats.receiver_id')
            ->select('chats.*','s.name as sender_name','r.name as receiver_name','s.image as sender_image','r.image as receiver_image')
            ->get()->toArray();
            
            if(!empty($chats)){
                $dat = json_decode(json_encode($chats),true);
                
                foreach($dat as $k=>$da)
                {
                    $cons = $da['chat_contents'];
                    if(empty($cons)){
                        unset($dat[$k]);
                    } else {
                        $count=0;
                        foreach($cons as $p=>$con)
                        {
                            if(($con['sender_id']==$userId && $con['sender_status']==1) || ($con['receiver_id']==$userId && $con['receiver_status']==1))
                            {
                                $count++;
                            } else {
                                unset($dat[$k]['chat_contents'][$p]);
                            }
                        }
                        if($count==0){
                            unset($dat[$k]);
                            break;
                        }
                        unset($dat[$k]['chat_contents']);
                    }
                }
                $status  = 'Success';
                $message = 'Chat list found successfully.';
            } else 
            {
              $dat=[]; 
              $status = 'Sucess';
              $message = 'Chat list is empty.';
            }
            echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
            die;
        }
        
        
        public function getChatDetails(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $chatId = str_replace("'","",str_replace('"',"",$data['chat_id']));
        $userId=str_replace("'","",str_replace('"',"",$data['user_id']));
        $chat= (new Chat())->with(['chatContents'=>function($query){
            return $query->orderBy('created_at', 'ASC');
        }])
        ->where(['chats.id'=>$chatId,'chats.status'=>1])
        ->leftJoin('chat_contents as c','c.chat_id','chats.id')
        ->leftJoin('users as s','s.id','chats.sender_id')
        ->leftJoin('users as r','r.id','chats.receiver_id')
        ->select('chats.*','s.name as sender_name','r.name as receiver_name','s.image as sender_image','r.image as receiver_image')
        ->first();
        if(!empty($chat)){
            $chat = $chat->toArray();
            
            if (empty($userId)) {
                echo json_encode(['status'=>'Error','message'=>'User-id is missing.']);
                die;
            }
            if ($chat['sender_id']==$userId || $chat['receiver_id']==$userId) {
                
            } else {
                echo json_encode(['status'=>'Error','message'=>'User-id not belongs to chat-Id.']);
                die;
            }
            
            $cons = $chat['chat_contents'];
            if(!empty($cons))
            {
                $rec=[];
                foreach($cons as $p=>$con)
                {
                    if(($con['sender_id']==$userId && $con['sender_status']==1) || ($con['receiver_id']==$userId && $con['receiver_status']==1))
                    {
                        $rec[]=$con;
                    } 
                }
                $chat['chat_contents']=$rec;
            }
            
            $chat_id = (string)$chat['id'];
            $chat['id'] = "$chat_id";
            $dat = json_decode(json_encode($chat),true);
            $status  = 'Success';
            $message = 'Chat details found successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Success';
          $message = 'Chat list is empty.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }
    
    public function getUserChats(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $userId = str_replace("'","",str_replace('"',"",$data['user_id']));
        $otherId = str_replace("'","",str_replace('"',"",$data['other_id']));
        $chats= (new Chat())->with(['chatContents'=>function($query){
            return $query->select('chat_id','sender_id','receiver_id','message','sender_status','receiver_status','created_at')->orderBy('created_at', 'ASC');
        }])
        ->where(['chats.order_id'=>null,'chats.status'=>1])
        ->where(function($query) use($userId,$otherId){
            return $query->where(['chats.sender_id'=>$userId,'chats.receiver_id'=>$otherId]);
        })
        ->orWhere(function($query) use($userId,$otherId){
            return $query->where(['chats.sender_id'=>$otherId,'chats.receiver_id'=>$userId]);
        })
        ->leftJoin('chat_contents as c','c.chat_id','chats.id')
        ->leftJoin('users as s','s.id','chats.sender_id')
        ->leftJoin('users as r','r.id','chats.receiver_id')
        ->select('chats.*','s.name as sender_name','r.name as receiver_name','s.image as sender_image','r.image as receiver_image')
        
        ->first();
        
        if(!empty($chats)){
            $chat = $chats->toArray();
            $cons = $chat['chat_contents'];
            if(!empty($cons))
            {
                $rec=[];
                foreach($cons as $p=>$con)
                {
                    if(($con['sender_id']==$userId && $con['sender_status']==1) || ($con['receiver_id']==$userId && $con['receiver_status']==1))
                    {
                        $rec[]=$con;
                    } 
                }
                $chat['chat_contents']=$rec;
            }
            
            $chat_id = (string)$chats['id'];
            $chat['id'] = "$chat_id";
            $dat = json_decode(json_encode($chat),true);
            $status  = 'Success';
            $message = 'Chat details found successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Success';
          $message = 'Chat records are empty.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }
    
    public function getOrderChats(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        $order_id = str_replace("'","",str_replace('"',"",$data['order_id']));
        $chat= (new Chat())->with(['chatContents'=>function($query){
            return $query->select('chat_id','sender_id','receiver_id','message','sender_status','receiver_status','created_at')->orderBy('created_at', 'ASC');
        }])
        ->where(['chats.order_id'=>$order_id,'chats.status'=>1])
        ->leftJoin('chat_contents as c','c.chat_id','chats.id')
        ->leftJoin('users as s','s.id','chats.sender_id')
        ->leftJoin('users as r','r.id','chats.receiver_id')
        ->select('chats.*','s.name as sender_name','r.name as receiver_name','s.image as sender_image','r.image as receiver_image')
        ->first();
        
        if(!empty($chat)){
            $chat=$chat->toArray();
            $userId = str_replace("'","",str_replace('"',"",$data['user_id']));
            $cons = $chat['chat_contents'];
            
            if(!empty($cons))
            {
                $rec=[];
                foreach($cons as $p=>$con)
                {
                    if(($con['sender_id']==$userId && $con['sender_status']==1) || ($con['receiver_id']==$userId && $con['receiver_status']==1))
                    {
                        $rec[]=$con;
                    } 
                }
                $chat['chat_contents']=$rec;
            }
            $chat_id = (string)$chat['id'];
            $chat['id'] = "$chat_id";
        }
        if(!empty($chat)){
            $dat = json_decode(json_encode($chat),true);
            $status  = 'Success';
            $message = 'Chat details found successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Success';
          $message = 'Chat details not found.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }
    
        
    /* Send Order Chat Message*/
     public function sendOrderChatMsg(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(!isset($data['order_id']) || empty($data['order_id'])){
            echo json_encode(['status'=>'Error','message'=>'Order Id is missing.']);
            die;
        } else if(empty($data['sender_id'])){
            echo json_encode(['status'=>'Error','message'=>'Sender Id is missing.']);
            die;
        } else if(empty($data['receiver_id'])){
            echo json_encode(['status'=>'Error','message'=>'Receiver Id is missing.']);
            die;
        }      
        ChatOrder:
        $chat=DB::table('chats as c')
               ->where(['c.status'=>1,'c.order_id'=>$data['order_id']])
               ->where(function ($query) use ($data){
                   return $query->Orwhere(['c.sender_id'=>$data['sender_id'],'c.receiver_id'=>$data['sender_id']]);
           })
           ->where(function ($query) use ($data){
               return $query->Orwhere(['c.sender_id'=>$data['receiver_id'],'c.receiver_id'=>$data['receiver_id']]);
           })->first();
           
        if(empty($chat)){
            $chat= new Chat();
            $chat->order_id=$data['order_id'];
            $chat->sender_id=$data['sender_id'];
            $chat->receiver_id=$data['receiver_id'];
            $chat->status=1;
            
            if($chat->save())
            {
                goto ChatOrder;
                
            } else {
                echo json_encode(['status'=>'Error','message'=>'Something missing in the chat.']);
                die;   
            }
        }
        
        $chatcontents = new ChatContent();
        $chatcontents->chat_id = $chat->id;
        $chatcontents->sender_id = $data['sender_id'];
        $chatcontents->receiver_id = $data['receiver_id'];
        $chatcontents->message = $data['message'];
        $chatcontents->status = 1;
        
        if($chatcontents->save())
           {
            $dat = $data;
            $status  = 'Success';
            $message = 'Message send successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Error';
          $message = 'Something missing in sending message.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }
    
     /* Send Chat Message*/
     public function sendChatMsg(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
        if(empty($data['sender_id'])){
            echo json_encode(['status'=>'Error','message'=>'Sender Id is missing.']);
            die;
        } 
        if(empty($data['receiver_id'])){
            echo json_encode(['status'=>'Error','message'=>'Receiver Id is missing.']);
            die;
        }      
        ChatOrder:
        $chat=DB::table('chats as c')
               ->where(['c.status'=>1,'c.order_id'=>null])
               ->where(function ($query) use ($data){
                   return $query->Orwhere(['c.sender_id'=>$data['sender_id'],'c.receiver_id'=>$data['sender_id']]);
           })
           ->where(function ($query) use ($data){
               return $query->Orwhere(['c.sender_id'=>$data['receiver_id'],'c.receiver_id'=>$data['receiver_id']]);
           })->first();
        
        if(empty($chat)){
            $chat= new Chat();
            $chat->order_id=@$data['order_id'];
            $chat->sender_id=$data['sender_id'];
            $chat->receiver_id=$data['receiver_id'];
            $chat->status=1;
            
            if($chat->save())
            {
                goto ChatOrder;
            } else {
                echo json_encode(['status'=>'Error','message'=>'Something missing in the chat.']);
                die;   
            }
        } 
        
        $chatcontents = new ChatContent();
        $chatcontents->chat_id = $chat->id;
        $chatcontents->sender_id = $data['sender_id'];
        $chatcontents->receiver_id = $data['receiver_id'];
        $chatcontents->message = $data['message'];
        $chatcontents->status = 1;
        
        if($chatcontents->save())
           {
            $status  = 'Success';
            $message = 'Message send successfully.';
        } else 
        { 
          $status = 'Error';
          $message = 'Something missing in sending message.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$data]);
        die;
    }
    
        /* Receive Order Chat Message*/
     public function receiveOrderChatMsg(Request $req)
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData,true);
        if(empty($data)){
           $data=$req->all(); 
        }
               
        $chat=DB::table('chats as c')
               ->where(['c.status'=>1,'c.order_id'=>$data['order_id']])
               ->where(function ($query) use ($data){
                   return $query->Orwhere(['c.sender_id'=>$data['sender_id'],'c.receiver_id'=>$data['sender_id']]);
           })
           ->where(function ($query) use ($data){
               return $query->Orwhere(['c.sender_id'=>$data['receiver_id'],'c.receiver_id'=>$data['receiver_id']]);
           });
           
          $chat = $chat->get()->toArray();
           
        if(empty($chat)){
            echo json_encode(['status'=>'Error','message'=>'Chat Account not exist.']);
            die;   
        }
        if(!empty($chat)){
            $chat = json_decode(json_encode($chat), true);
        }
            $status  = 'Success';
            $message = 'Chat found successfully.';
        
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$chat]);
        die;
    }

    
}
