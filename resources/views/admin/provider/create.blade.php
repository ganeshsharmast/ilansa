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
                      <form method="post" id="user_profile" enctype="multipart/form-data">
                          
                      <table class="table table-hover">
                        <tbody>
                            @if(!empty($provider))
                            <tr><td>
                                <input type="hidden" name="id" value="{{$provider['id']}}">    
                            </td></tr>
                            @endif
                            <tr>
                                <td></td>
                                <td><img style="width: 85px;height: 85px;" src="{{@$provider['image']}}"  class="image_preview">
                                <br><input id="image" type="file" name="image"></img></td>
                            </tr>
                            <tr>
                                <td><label>Role</label></td>
                                <td>
                                    <select name="account_type_id">
                                        @foreach($role as $id=>$rol)
                                        <option value="{{$id}}" {{((@$provider['role']==$id)?'selected':'')}}>{{$rol}}</option> 
                                        @endforeach
                                    </select>
                               </td>
                           </tr>
                            <tr>
                                <td><label>Name</label></td>
                                <td><input type="text" name="company_name" autocomplete="off" value="{{@$provider['name']}}" placeholder="Please enter your name" required></td>
                            </tr>
                            <tr>
                                <td><label>Email</label></td>
                                <td><input type="email" name="email" placeholder="" value="{{@$provider['email']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>Area Radius</label></td>
                                <td><input type="text" name="area_radius" placeholder="" value="{{@$provider['area_radius']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>Refer Code</label></td>
                                <td><input type="text" name="refer_code" placeholder="" value="{{@$provider['refer_code']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>Address</label></td>
                                <td><input type="text" placeholder="" name="address" value="{{@$provider['address']}}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label>Phone</label></td>
                                <td><input type="text" autocomplete="off" name="phone" value="{{@$provider['phone']}}" placeholder="Please enter your phone" required></td>
                            </tr>
                            <tr>
                                <td><label>Order Alert</label></td>
                                <td><input type="checkbox" name="order_alert" value="{{@$provider['order_alert']}}" {{(@$provider['order_alert']==1)?'checked':''}}></td>
                            </tr>
                            <tr>
                                <td><label>Promotion SMS Alert</label></td>
                                <td><input type="checkbox" name="promotion_sms_alert" value="{{@$provider['promotion_sms_alert']}}" {{(@$provider['ein_later']==1)?'checked':''}}></td>
                            </tr>
                            <tr>
                                <td><label>Promotion Email Alert</label></td>
                                <td><input type="checkbox" name="promotion_email_alert" value="{{@$provider['promotion_email_alert']}}" {{(@$provider['promotion_email_alert']==1)?'checked':''}}></td>
                            </tr>
                            <tr>
                                <td><label>Update Email Alert</label></td>
                                <td><input type="checkbox" name="update_email_alert" value="{{@$provider['update_email_alert']}}" {{(@$provider['update_email_alert']==1)?'checked':''}}></td>
                            </tr>
                            <tr>
                                <td><label>Update SMS Alert</label></td>
                                <td><input type="checkbox" name="update_sms_alert" value="{{@$provider['update_sms_alert']}}" {{(@$provider['update_sms_alert']==1)?'checked':''}}></td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td>
                                    <select name="status">
                                        @foreach($status as $id=>$name)
                                        <option value="{{$id}}" {{((@$provider['status']==$id)?'selected':'')}}>{{$name}}</option> 
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