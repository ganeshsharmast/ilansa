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
                            @if(!empty($serviceRequest))
                            <tr>
                                <td><label>Service Id</label></td>
                                <td>{{$serviceRequest['id']}}</td>
                            </tr>
                            <tr>
                                <td><label>User Name</label></td>
                                <td>{{$serviceRequest['user_details']['name']}}</td>
                            </tr>
                            <tr>
                                <td><label>Name</label></td>
                                <td>{{$serviceRequest['message']}}</td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{$serviceRequest['status_details']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td>
                                <a href="{{url('admin/service/user-requests')}}" class="badge badge-danger">Return</a>
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