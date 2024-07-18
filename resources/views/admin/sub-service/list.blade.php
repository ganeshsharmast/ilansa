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
                        <thead>
                          <tr>
                            <th>Id</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Service Name</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($sub_services))
                            @foreach($sub_services as $sub_service)
                          <tr>
                            <td>{{$sub_service['id']}}</td>
                            <td><img src="{{$sub_service['image']}}"/></td>
                            <td> {{$sub_service['name']}}</td>
                            <td> {{$sub_service['services']['name']}}</td>
                            <td><label class="badge badge-danger">{{$sub_service['status_details']['name']}}</label>
                            <!--<label class="badge badge-danger">Pending</label>-->
                            </td>
                            <td>
                                <a href="{{url('admin/sub-service/product/list/'.$sub_service['id'])}}"><i class="mdi mdi-clipboard-text" title="Products"></i></a>
                                <a href="{{url('admin/sub-service/edit/'.$sub_service['id'])}}"><i class="mdi mdi-pencil" title="Sub Service Edit"></i></a>,
                                <a href="{{url('admin/sub-service/view/'.$sub_service['id'])}}"><i class="mdi mdi-eye-outline" title="Sub Service View"></i></a>,
                                <a href="#" class="sub_service_delete" data-id="{{$sub_service['id']}}" title="Sub Service Delete"><i class="mdi mdi-delete-outline" title="Delete"></i></a>
                            </td>
                          </tr>
                          @endforeach
                          @else
                          <tr>
                            <td colspan="4">No Record Found</td>
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