<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ilansa Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{URL::to('/public/frontend/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{URL::to('/public/frontend/assets/vendors/css/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{URL::to('/public/frontend/assets/css/style.css')}}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{URL::to('/public/frontend/assets/images/favicon.png')}}" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="row w-100 m-0">
          <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
            <div class="card col-lg-4 mx-auto">
              <div class="card-body px-5 py-5">
                <h3 class="card-title text-left mb-3">Login</h3>
                <form action="{{route('adminLoginPost')}}" method="post">
                    @csrf
                  <div class="form-group">
                    <label for="email" class="form-label">Username or Email *</label>
                    <input type="text" class="form-control p_input" id="email" name="email" value="{{old('email') }}" autofocus />
                    @if ($errors->has('email'))
                    <span class="help-block font-red-mint">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                  </div>
                  <div class="input-group form-group input-group-merge form-password-toggle">
                        <input type="password" class="form-control form-control-merge p_input" id="password" name="password" tabindex="2" />
                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                    </div>
                    @if ($errors->has('password'))
                    <span class="help-block font-red-mint">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                  <div class="form-group d-flex align-items-center justify-content-between">
                    <!--<div class="form-check">-->
                    <!--  <label class="form-check-label">-->
                    <!--    <input type="checkbox" class="form-check-input"> Remember me </label>-->
                    <!--</div>-->
                    <a href="#" class="forgot-pass">Forgot password</a>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Login</button>
                  </div>
                  <!--<div class="d-flex">-->
                  <!--  <button class="btn btn-facebook mr-2 col">-->
                  <!--    <i class="mdi mdi-facebook"></i> Facebook </button>-->
                  <!--  <button class="btn btn-google col">-->
                  <!--    <i class="mdi mdi-google-plus"></i> Google plus </button>-->
                  <!--</div>-->
                  <!--<p class="sign-up">Don't have an Account?<a href="#"> Sign Up</a></p>-->
                </form>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- row ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{URL::to('/public/frontend/assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{URL::to('/public/frontend/assets/js/off-canvas.js')}}"></script>
    <script src="{{URL::to('/public/frontend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{URL::to('/public/frontend/assets/js/misc.js')}}"></script>
    <script src="{{URL::to('/public/frontend/assets/js/settings.js')}}"></script>
    <script src="{{URL::to('/public/frontend/assets/js/todolist.js')}}"></script>
    <!-- endinject -->
  </body>
</html>