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
          <?php
        //   echo "<pre>";
        //   print_r($sub_service);
        //   die;
          
          ?>
            <div class="row main-body">
              <div class="col-lg-12 col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <tbody>
                            @if(!empty($sub_service))
                            <tr>
                                <td><label>Sub Service Id</label></td>
                                <td>{{$sub_service['id']}}</td>
                            </tr>
                            <tr>
                                <td><label>Image</label></td>
                                <td><img src="{{$sub_service['image']}}"></td>
                            </tr>
                            <tr>
                                <td><label>Name</label></td>
                                <td>{{$sub_service['name']}}</td>
                            </tr>
                            <tr>
                                <td><label>Service Name</label></td>
                                <td>{{$sub_service['services']['name']}}</td>
                            </tr>
                            <tr>
                                <td><label>Tax Percent</label></td>
                                <td>{{$sub_service['tax_percent']}}</td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{$sub_service['status_details']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td><a href="{{url('admin/sub-service/edit/'.$sub_service['id'])}}" class="badge badge-info">Edit</a>
                                <a href="{{url('admin/sub-service/list')}}" class="badge badge-danger">Cancel</a>
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