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
                      <form method="post" id="admin_profile" enctype="multipart/form-data">
                          
                      <table class="table table-hover">
                        <tbody>
                            @if(!empty($user))
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="id" value="{{$user['id']}}">
                            <tr>
                                <td></td>
                                <td><img style="width: 85px;height: 85px;" src="{{$user['image']}}"  class="image_preview">
                                <br><input id="image" type="file" name="image"></img></td>
                            </tr>
                            <tr>
                                <td><label>User Id</label></td>
                                <td>{{$user['id']}}</td>
                            </tr>
                            <tr>
                                <td><label>Name</label></td>
                                <td><input type="text" name="name" value="{{$user['name']}}" placeholder="Please enter your name" required></td>
                            </tr>
                            <tr>
                                <td><label>Email</label></td>
                                <td><label for="email">{{$user['email']}}</label></td>
                            </tr>
                            <tr>
                                <td><label>Phone</label></td>
                                <td><input type="text" name="phone" value="{{$user['phone']}}" placeholder="Please enter your phone" required></td>
                            </tr>
                            <tr>
                                <td><label>Status</label></td>
                                <td><label class="badge badge-danger">{{@$user['status_details']['name']}}</label>
                            </td>
                          </tr>
                            <tr>
                                <td></td>
                                <td><a href="{{url('admin/profile/edit')}}" class="badge badge-info">Update</a>
                                <a href="{{url('admin')}}" class="badge badge-danger">Cancel</a>
                            </td>
                          </tr>
                          @else
                          <tr>
                            <td>No Record Found</td>
                          </tr>
                          @endif
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
    <script>
        $(".badge-info").click(function(e)
        {
            e.preventDefault();

        var data = $('#admin_profile').serialize(),
            // data1=new FormData($('#admin_profile')),
            image=$(".image_preview").files;
            // data.append('image',image);
            $.ajax({url: "https://ilansa.shailtech.com/admin/profile/update",
                    data: data,
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        $("#div1").html(result);
                     }}).done(function( msg ) {
                window.location.href = "https://ilansa.shailtech.com/admin/profile/view";
            });
        });
        
        //todo:image preview
        $(document).on('change','#image', function() {
            $('.error_success_msg_container').html('');
            if (this.files && this.files[0]) {
                let img = document.querySelector('.image_preview');
                img.onload = () =>{
                    URL.revokeObjectURL(img.src);
                }
                img.src = URL.createObjectURL(this.files[0]);
                document.querySelector(".image_preview").files = this.files;
            }
        });
    </script>
  </body>
</html>