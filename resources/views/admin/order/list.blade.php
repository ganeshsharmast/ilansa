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
                            <th>User</th>
                            <th>Coupon</th>
                            <th>Schedule Start</th>
                            <th>Schedule End</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($orders))
                            @foreach($orders as $order)
                          <tr>
                            <td>{{$order['id']}}</td>
                            <td> {{$order['user_details']['name']}}</td>
                            <td> {{$order['coupon_id']}}</td>
                            <td> {{date('h:i a d-m-Y',strtotime($order['service_schedule_start']))}}</td>
                            <td> {{date('h:i a d-m-Y',strtotime($order['service_schedule_end']))}}</td>
                            <td><label class="badge badge-danger">{{$order['status_details']['name']}}</label>
                            <!--<label class="badge badge-danger">Pending</label>-->
                            </td>
                            <td>
                                <a href="{{url('admin/order/bill-generate/'.$order['id'])}}"><i class="mdi mdi-clipboard-text" title="Print Bill"></i></a>,
                                <a href="{{url('admin/order/detail/'.$order['id'])}}"><i class="mdi mdi-eye-outline" title="View"></i></a>
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