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
                      <form method="post" id="company_profile" enctype="multipart/form-data">
                          
                      <table class="table table-hover">
                        <tbody>
                            @if(!empty($company))
                            <tr><td>
                                <input type="hidden" name="id" value="{{$company['id']}}">    
                            </td></tr>
                            @endif
                            <tr>
                                <td></td>
                                <td><img style="width: 85px;height: 85px;" src="{{@$company['image']}}"  class="image_preview">
                                <br><input id="image" type="file" name="image"></img></td>
                            </tr>
                            <tr>
                                <td><label>Account Type</label></td>
                                <td>
                                    <select name="account_type_id">
                                        @foreach($accType as $id=>$type)
                                        <option value="{{$id}}" {{((@$company['account_type']==$id)?'selected':'')}}>{{$type}}</option> 
                                        @endforeach
                                    </select>
                               </td>
                           </tr>
                            <tr>
                                <td><label>Name</label></td>
                                <td><input type="text" name="company_name" autocomplete="off" value="{{@$company['company_name']}}" placeholder="Please enter your name" required></td>
                            </tr>
                            <tr>
                                <td><label>Email</label></td>
                                <td><input type="email" name="email" placeholder="" value="{{@$company['email']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>EIN</label></td>
                                <td><input type="text" name="ein" placeholder="" value="{{@$company['ein']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>EIN Later</label></td>
                                <td><input type="checkbox" name="ein_later" value="{{@$company['ein_later']}}" {{(@$company['ein_later']==1)?'checked':''}}></td>
                            </tr>
                            <tr>
                                <td><label>SSN</label></td>
                                <td><input type="text" placeholder="" name="ssn" value="{{@$company['ssn']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>Phone</label></td>
                                <td><input type="text" autocomplete="off" name="phone" value="{{@$company['phone']}}" placeholder="Please enter your phone" required></td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td>
                                    <select name="status">
                                        @foreach($status as $id=>$name)
                                        <option value="{{$id}}" {{((@$company['status']==$id)?'selected':'')}}>{{$name}}</option> 
                                        @endforeach
                                    </select>
                               </td>
                           </tr>
                            <tr>
                                <td></td>
                                <td><a id="company_profile_submit" class="badge badge-info">Save</a>
                                <a href="{{url('admin/company/list')}}" class="badge badge-danger">Cancel</a>
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