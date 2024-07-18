<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Page;
use App\Http\Helpers\Helper;


class PageController extends Controller
{

    public function getPageContent($url='')
    {
        if(!empty($_GET)){
           $url=@$_GET['url']; 
        }
         $page = (new Page())->where(['url'=>$url])->first();
        if(empty($page)){
            echo json_encode(['status'=>'Error','message'=>'Page not found.']);
            die;   
        }
        $pageContents= DB::table('pages as p')->leftJoin('page_contents as pc','p.id','pc.page_id')
        ->select('p.*','pc.contents')->where(['p.id'=>$page->id,'p.status'=>1])->get()->toArray();

        if(!empty($pageContents)){
            $dat = json_decode(json_encode($pageContents),true);
            $status  = 'Success';
            $message = 'Page found successfully.';
        } else 
        {
          $dat=[]; 
          $status = 'Error';
          $message = 'Something missing.';
        }
        echo json_encode(['status'=>$status, 'message'=>$message,'data'=>$dat]);
        die;
    }

}
