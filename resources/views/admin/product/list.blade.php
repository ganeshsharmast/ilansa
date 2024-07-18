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
                            <th>Category</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($products))
                            @foreach($products as $product)
                          <tr>
                            <td>{{$product['id']}}</td>
                            <td>@if(!empty($product['product_image']))<img src="{{$product['product_image']}}"/>
                            @endif</td>
                            <td> {{$product['product_name']}}</td>
                            <td> {{$product['product_category']['name']}}</td>
                            <td><label class="badge badge-danger">{{$product['product_status']['name']}}</label>
                            <!--<label class="badge badge-danger">Pending</label>-->
                            </td>
                            <td>
                                <a href="{{url('admin/product/edit/'.$product['id'])}}"><i class="mdi mdi-pencil" title="Product Edit"></i></a>,
                                <a href="{{url('admin/product/view/'.$product['id'])}}"><i class="mdi mdi-eye-outline" title="Product View"></i></a>,
                                <a href="#" class="product_delete" data-id="{{$product['id']}}"><i class="mdi mdi-delete-outline" title="Product Delete"></i></a>
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