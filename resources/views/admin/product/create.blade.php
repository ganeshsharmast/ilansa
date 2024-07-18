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
                      <form method="post" id="product_form" enctype="multipart/form-data">
                          
                      <table class="table table-hover">
                        <tbody>
                            @if(isset($product['id']))
                            <tr><td colspan="2">
                                <input type="hidden" name="id" value="{{@$product['id']}}">
                            </td>
                            </tr>
                            @endif
                            <tr>
                                <td></td>
                                <td><img style="width: 85px;height: 85px;" src="{{@$product['product_image']}}"  class="image_preview">
                                <br><input id="image" type="file" name="product_image"></img></td>
                            </tr>
                            <tr>
                                <td><label>Product Category</label></td>
                                <td>
                                    <select name="product_category_id">
                                        @foreach($category as $id=>$name)
                                        <option value="{{$id}}" {{((@$product['product_category_id']==$id)?'selected':'')}}>{{$name}}</option> 
                                        @endforeach
                                    </select>
                               </td>
                           </tr>
                           <tr>
                                <td><label>Name</label></td>
                                <td><input type="text" autocomplete="off" name="product_name" value="{{@$product['product_name']}}" placeholder="Enter product name" required></td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td>
                                    <select name="status">
                                        @foreach($status as $id=>$name)
                                        <option value="{{$id}}" {{((@$product['status']==$id)?'selected':'')}}>{{$name}}</option> 
                                        @endforeach
                                    </select>
                               </td>
                           </tr>
                            <tr>
                                <td></td>
                                <td><a id="product_submit" class="badge badge-info">Submit</a>
                                <a href="{{url('admin/product/list')}}" class="badge badge-danger">Cancel</a>
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