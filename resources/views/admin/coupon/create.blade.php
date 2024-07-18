<!DOCTYPE html>
<html lang="en">
{{ View::make('admin/includes/header')}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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
                      <form method="post" id="coupon_create" enctype="multipart/form-data">
                          
                      <table class="table">
                        <tbody>
                            @if(isset($coupon))
                            <tr>
                                <td>
                                <input type="hidden" name="id" value="{{@$coupon['id']}}">        
                                </td>
                            </tr>
                            <tr>
                                <td><label>Coupon Id</label></td>
                                <td>{{@$coupon['id']}}</td>
                            </tr>
                            @endif
                            
                            <tr>
                                <td><label>Name</label></td>
                                <td><input type="text" name="name" value="{{@$coupon['name']}}" autocomplete="off"  placeholder="Enter coupon name" required></td>
                            </tr>
                            <tr>
                                <td><label>Code</label></td>
                                <td><input type="text" autocomplete="off" name="code" value="{{@$coupon['code']}}" placeholder="Enter coupon code" required></td>
                            </tr>
                            <tr>
                                <td><label>Value</label></td>
                                <td><input type="text" autocomplete="off" name="value" value="{{@$coupon['value']}}" placeholder="Enter code value" required></td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{@$coupon['status_details']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td><a id="coupon_submit" class="badge badge-info">Save</a>
                                <a href="{{url('admin/coupon/list')}}" class="badge badge-danger">Cancel</a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      </form>
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