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
                            <th>Product Name</th>
                            <th>Service Name</th>
                            <th>Sub Service Name</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($service_products))
                            @foreach($service_products as $product)
                            <?php
                            // echo "<pre>";
                            // print_r($product);
                            // die;
                            
                            ?>
                          <tr>
                            <td>{{$product['id']}}</td>
                            <td><img src="{{$product['product']['product_image']}}"/></td>
                            <td> {{$product['product']['product_name']}}</td>
                            <td> {{@$product['sub_service']['services']['name']}}</td>
                            <td> {{@$product['sub_service']['name']}}</td>
                            <td><label class="badge badge-danger">{{$product['product']['product_status']['name']}}</label>
                            </td>
                            <td>
                                <a href="#"><i class="mdi mdi-pencil" title="Edit"></i></a>,
                                <a href="#"><i class="mdi mdi-eye-outline" title="View"></i></a>,
                                <a href="#"><i class="mdi mdi-delete-outline" title="Delete"></i></a>
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