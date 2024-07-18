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
                            @if(!empty($order))
                            <tr>
                                <td><label>Order Id</label></td>
                                <td>{{$order['id']}}</td>
                            </tr>
                            <tr>
                                <td><label>User</label></td>
                                <td> {{$order['user_details']['name']}}</td>
                            <tr>
                            <tr>
                                <td><label>Provider</label></td>
                                <td> {{@$order['order_request'][0]['provider_details']['name']}}</td>
                            <tr>
                            <tr>
                                <td><label>Coupon Code</label></td>
                                <td> {{$order['coupon_id']}}</td>
                            </tr>
                            <tr>
                                <td><label>Delivery Address</label></td>
                                <td> <input type="text" name="address" placeholder="Enter address" value="{{$order['address']}}"/></td>
                            </tr>
                            <tr>
                                <td><label>Schedule Start</label></td>
                                <td> {{date('d-m-Y h:i a',strtotime($order['service_schedule_start']))}}</td>
                            </tr>
                            <tr>
                                <td><label>Schedule End</label></td>
                                <td> {{date('d-m-Y h:i a',strtotime($order['service_schedule_end']))}}</td>
                            </tr>
                            <tr>
                                <td><label>User Acceptance</label></td>
                                <td> {{$order['user_acceptance']}}</td>
                            </tr>
                            <tr>
                                <td><label>Order Report</label></td>
                                <td> {{$order['order_report']}}</td>
                            </tr>
                            <tr>
                                <td><label>User Acceptance</label></td>
                                <td> {{$order['user_acceptance']}}</td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{$order['order_status']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td><a href="{{url('admin/order/edit/'.$order['id'])}}" class="badge badge-info">Edit</a>
                                <a href="{{url('admin/order/list')}}" class="badge badge-danger">Cancel</a>
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