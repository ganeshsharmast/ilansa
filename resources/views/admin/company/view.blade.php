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
                            @if(!empty($company))
                            <tr>
                                <td><label>Company Id</label></td>
                                <td>{{$company['id']}}</td>
                            </tr>
                            <tr>
                                <td><label>Image</label></td>
                                <td><img src="{{$company['image']}}"></td>
                            </tr>
                            <tr>
                                <td><label>Name</label></td>
                                <td>{{$company['company_name']}}</td>
                            </tr>
                            <tr>
                                <td><label>Type</label></td>
                                <td>{{$company['account_type']['type_name']}}</td>
                            </tr>
                            <tr>
                                <td><label>Email</label></td>
                                <td>{{$company['email']}}</td>
                            </tr>
                            <tr>
                                <td><label>SSN</label></td>
                                <td>{{$company['ssn']}}</td>
                            </tr>
                            <tr>
                                <td><label>EIN</label></td>
                                <td>{{$company['ein']}}</td>
                            </tr>
                            <tr>
                                <td><label>Phone</label></td>
                                <td>{{$company['phone']}}</td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{$company['status_details']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td><a href="{{url('admin/company/edit/'.$company['id'])}}" class="badge badge-info">Edit</a>
                                <a href="{{url('admin/company/list')}}" class="badge badge-danger">Cancel</a>
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