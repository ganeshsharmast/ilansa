<!DOCTYPE html>
<html lang="en">
{{ View::make('admin/includes/header')}}
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
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <tbody>
                            @if(!empty($notification))
                            <tr>
                                <td><label>Notification Id</label></td>
                                <td>{{$notification['id']}}</td>
                            </tr>
                            <tr>
                                <td><label>Subject</label></td>
                                <td>{{$notification['subject']}}</td>
                            </tr>
                            <tr>
                                <td><label>Description</label></td>
                                <td>{{$notification['description']}}</td>
                            </tr>
                            <tr>
                                <td><label>Read</label></td>
                                <td>{{($notification['read']==1)?'yes':'no'}}</td>
                            </tr>
                            <tr>
                                <td><label>Important</label></td>
                                <td>{{($notification['important']==1)?'yes':'no'}}</td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{$notification['status_details']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td>
                                <a href="{{url('admin/notification/list')}}" class="badge badge-danger">Return</a>
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