<!DOCTYPE html>
<html lang="en">
{{ View::make('admin/includes/header')}}
  <style>
      .chat_sender {
          color: cornflowerblue;
      }
      .chat_receiver {
          color: palevioletred;
      }
      #card_content td {
          white-space: initial;
      }
      #card_content {
          width: 60%;
          min-width: 57%;
          display: block;
      }
      
  </style>
  
  <body>  
      
    <div class="container-scroller">
      {{ View::make('admin/includes/sidebar',['breadcrumbs'=>$breadcrumbs])}}          
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
         {{ View::make('admin/includes/top_header')}} 
        <!-- partial -->
        <div class="main-panel">
          <!--<div class="content-wrapper">-->
          <!--</div>-->
          {{ View::make('admin/includes/breadcrumbs',['breadcrumbs'=>$breadcrumbs])}}
            <div class="row main-body">
              <div class="col-lg-12 col-md-12 grid-margin stretch-card">
                <div class="card" id="card_content">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <tbody>
                            @if(!empty($chat) && !empty($chat['chat_contents']))
                            @foreach($chat['chat_contents'] as $cont)
                            @if($chat['sender_id']==$cont['sender_id'])
                            <tr>
                                <td><label class="chat_sender">{{$chat['user_details']['name']}}</label><br/>
                                {{$cont['message']}}
                                </td>
                                <td></td>
                            </tr>
                            @else
                            <tr>
                                <td></td>
                                <td><label class="chat_receiver">{{$chat['receiver_details']['name']}}</label><br/>
                                {{$cont['message']}}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            <tr><td></td><td></td></tr>
                            <tr>
                                <td></td>
                                <td>
                                <a href="{{url('admin/chat/list')}}" class="badge badge-danger">Return</a>
                            </td>
                          </tr>
                          @else
                          <tr>
                            <td>No Record Found</td>
                          </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            
           {{ View::make('admin/includes/sub_footer')}} 
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    {{ View::make('admin/includes/footer')}}
  </body>
</html>