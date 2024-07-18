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
                            <th>name</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($services))
                            @foreach($services as $service)
                          <tr>
                            <td>{{$service['id']}}</td>
                            <td><img src="{{$service['image']}}"/></td>
                            <td> {{$service['name']}}</td>
                            <td><label class="badge badge-danger">{{$service['status_details']['name']}}</label>
                            <!--<label class="badge badge-danger">Pending</label>-->
                            </td>
                            <td>
                                <a href="{{url('admin/service/edit/'.$service['id'])}}"><i class="mdi mdi-pencil" title="Edit" title="Edit Service"></i></a>,
                                <a href="{{url('admin/service/view/'.$service['id'])}}" title="View Service"><i class="mdi mdi-eye-outline" title="View"></i></a>,
                                <a class="service_delete" data-id="{{$service['id']}}" title="Delete Service"><i class="mdi mdi-delete-outline" title="Delete"></i></a>
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