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
                      <table class="table">
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
                            </tbody>
                          </table>
                          <form id="support_chat" method="post">
                              <input type="hidden" name="id" value="{{$chat['id']}}">
                              <input type="hidden" name="receiver_id" value="{{($chat['receiver_id']==1)?$chat['sender_id']:1;}}">
                          <table class="table">
                              <tbody>
                            <tr><td></td><td></td></tr>
                            <tr><td colspan="2">
                                <textarea id="message" name="message" rows="4"> </textarea>
                            </td></tr>
                            <tr><td colspan="2"><button type="button" name="submit" id="support_chat_submit">Submit</button>
                          <button type="button">cancel</button></td>
                          </tr>
                          </tbody>
                          </table>
                          </form>
                            
                          @else
                          <tr>
                            <td>No Record Found</td>
                           </tr>
                          </tbody>
                         </table>
                        @endif
                        <table class="table table-hover">
                            <tr>
                                <td></td>
                                <td><a href="{{url('admin/chat/support-chat-list')}}" class="badge badge-danger">Return</a></td>
                            </tr>
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