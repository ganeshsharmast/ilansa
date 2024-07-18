<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubServicesController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\ProductCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('clear_cache', function () {

    // \Artisan::call('make:mail MyEmail');
    // die("address mail done");
    \Artisan::call('optimize');
    \Artisan::call('config:cache');
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    // \Artisan::call('make:mail Email');
    dd("Cache is cleared");

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function(){
    return view('admin.auth.login');
});

Route::get('/login', [AdminAuthController::class, 'getLogin'])->name('adminLogin');
Route::get('/admin/login', [AdminAuthController::class, 'getLogin'])->name('admin-Login');
 Route::post('login', [AdminAuthController::class, 'postLogin'])->name('adminLoginPost');
 

Route::group(['prefix' => 'admin', 'namespace' => 'Admin','middleware' => 'adminauth'], function () {

   
    Route::post('adminLogout',[AdminAuthController::class,'adminLogout'])->name('adminLogout');
    
    Route::get('/', function () { 
            return view('admin.dashboard');
        })->name('adminDashboard');
 
    Route::get('/dashboard', function(){
        return view('admin/dashboard');
    });

    /* Service List. */
    Route::get('/service/list', [ServiceController::class, 'list'])->name('list');
    Route::get('/service/user-requests', [ServiceController::class, 'userRequests'])->name('user-requests');
    Route::get('/service/request-view/{reqId}', [ServiceController::class, 'requestView'])->name('request-view');
    Route::get('/service/create', [ServiceController::class, 'create'])->name('service-create');
    Route::get('/service/edit/{serviceId}', [ServiceController::class, 'edit'])->name('service-edit');
    Route::get('/service/view/{serviceId}', [ServiceController::class, 'view'])->name('service-view');
    Route::post('/service/save', [ServiceController::class, 'save'])->name('service-save');
    Route::post('/service/delete', [ServiceController::class, 'delete'])->name('service-delete');
    
    
    
    /* Sub-Service List. */
    Route::get('/sub-service/list', [SubServicesController::class, 'list']);
    Route::get('/sub-service/product/list/{subServiceId}', [SubServicesController::class, 'productList']);
    Route::get('/sub-service/create', [SubServicesController::class, 'create'])->name('sub-service-create');
    Route::get('/sub-service/edit/{serviceId}', [SubServicesController::class, 'edit'])->name('sub-service-edit');
    Route::get('/sub-service/view/{subServiceId}', [SubServicesController::class, 'view']);
    Route::post('/sub-service/save', [SubServicesController::class, 'save'])->name('sub-service-save');
    Route::post('/sub-service/delete', [SubServicesController::class, 'delete'])->name('sub-service-delete');
    
    
    Route::get('/user/list', [UserController::class, 'userList'])->name('user-list');
    Route::get('/user/view/{userId}', [UserController::class, 'userView'])->name('user-view');
    Route::get('/profile/view', [AdminAuthController::class, 'profileView'])->name('profile-view');
    Route::get('/profile/edit', [AdminAuthController::class, 'profileEdit'])->name('profile-edit');
    Route::post('/profile/update', [AdminAuthController::class, 'profileUpdate'])->name('profile-update');
    
    
    Route::get('/provider/list', [UserController::class, 'providerList'])->name('provider-list');
    Route::get('/provider/view/{providerId}', [UserController::class, 'providerView'])->name('provider-view');
    Route::get('/provider/edit/{userId}', [UserController::class, 'edit'])->name('provider-edit');
    Route::post('/provider/save', [UserController::class, 'save'])->name('provider-save');
    Route::post('/company/delete', [UserController::class, 'delete'])->name('provider-delete');
    
    
    
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-Password');
    
    Route::get('/company/list', [CompanyController::class, 'list'])->name('company-list');
    Route::get('/company/view/{companyId}', [CompanyController::class, 'view'])->name('company-view');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company-create');
    Route::get('/company/edit/{companyId}', [CompanyController::class, 'edit'])->name('company-edit');
    Route::post('/company/save', [CompanyController::class, 'save'])->name('company-save');
    Route::post('/company/delete', [CompanyController::class, 'delete'])->name('company-delete');
    
    Route::get('/coupon/list', [CouponController::class, 'list'])->name('coupon-list');
    Route::get('/coupon/view/{couponId}', [CouponController::class, 'view'])->name('coupon-view');
    Route::get('/coupon/create', [CouponController::class, 'create'])->name('coupon-create');
    Route::get('/coupon/edit/{companyId}', [CouponController::class, 'edit'])->name('coupon-edit');
    Route::post('/coupon/save', [CouponController::class, 'save'])->name('coupon-save');
    Route::post('/coupon/delete', [CouponController::class, 'delete'])->name('coupon-delete');
    
    
    Route::get('/order/list', [OrderController::class, 'orderList'])->name('order-list');
    Route::get('/order/detail/{orderId}', [OrderController::class, 'detail'])->name('order-detail');
    Route::get('/order/bill-generate/{orderId}', [OrderController::class, 'billGeneratePDF']);


    Route::get('/product/list', [ProductController::class, 'list'])->name('product-list');
    Route::get('/product/view/{productId}', [ProductController::class, 'view'])->name('product-view');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product-create');
    Route::get('/product/edit/{companyId}', [ProductController::class, 'edit'])->name('product-edit');
    Route::post('/product/save', [ProductController::class, 'save'])->name('product-save');
    Route::post('/product/delete', [ProductController::class, 'delete'])->name('product-delete');
   
   
    Route::get('/notification/list', [NotificationController::class, 'list'])->name('notification-list');
    Route::get('/notification/view/{notiId}', [NotificationController::class, 'view'])->name('notification-view');
    
   
    Route::get('/product-category/list', [ProductCategoryController::class, 'list'])->name('product-category-list');
    Route::get('/product-category/view/{productCatId}', [ProductCategoryController::class, 'view'])->name('product-category-view');
    Route::get('/product-category/create', [ProductCategoryController::class, 'create'])->name('product-category-create');
    Route::get('/product-category/edit/{companyId}', [ProductCategoryController::class, 'edit'])->name('product-category-edit');
    Route::post('/product-category/save', [ProductCategoryController::class, 'save'])->name('product-category-save');
    Route::post('/product-category/delete', [ProductCategoryController::class, 'delete'])->name('product-category-delete');
    
    Route::get('/chat/members-chat-list', [ChatController::class, 'memberChats'])->name('member-chats');
    Route::get('/chat/support-chat-list', [ChatController::class, 'supportChats'])->name('support-chats');
    Route::get('/chat/view/{chatId}', [ChatController::class, 'view'])->name('chat-view');
    Route::get('/chat/make/{chatId}', [ChatController::class, 'make'])->name('chat-make');
    Route::post('/chat/delete/{chatId}', [ChatController::class, 'delete'])->name('chat-delete');
    Route::post('/chat/save', [ChatController::class, 'save'])->name('chat-save');
    
});



Route::prefix('api')->group(function () {
 
    /* Basic APIs */
    Route::post('/register', [UserController::class, 'register']);
    
    Route::get('/sendMail', [UserController::class, 'sendMail']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/updateDeviceDetails', [UserController::class, 'updateDeviceDetails']);
    Route::post('/forgetPassword', [UserController::class, 'forgetPassword']);
    Route::post('/resetPassword', [UserController::class, 'resetPassword']);
    Route::post('/changePassword', [UserController::class, 'changePassword']);
    Route::post('/updateAvailability', [UserController::class, 'updateAvailability']);
    Route::post('/changeLanguage', [UserController::class, 'changeLanguage']);  
    Route::post('/verifyEmailPhone', [UserController::class, 'verifyEmailPhone']);
    Route::post('/getProfileDetails', [UserController::class, 'getProfileDetails']);
    Route::post('/updateProfileDetails', [UserController::class, 'updateProfileDetails']);     
    Route::post('/makeUserFavorite', [UserController::class, 'makeUserFavorite']);
    Route::post('/makeUserServiceRequest', [UserController::class, 'makeUserServiceRequest']);
    Route::post('/getUserServiceRequestDetail', [UserController::class, 'getUserServiceRequestDetail']);
    Route::post('/getUserServiceRequestList', [UserController::class, 'getUserServiceRequestList']);
    Route::post('/updateServiceRequestStatus', [UserController::class, 'updateServiceRequestStatus']);
    
    
    
    Route::post('/getCompanyDetails', [CompanyController::class, 'getCompanyDetails']);
    Route::post('/updateCompanyDetails', [CompanyController::class, 'updateCompanyDetails']);
    Route::post('/updateProfileAlerts', [UserController::class, 'updateProfileAlerts']);
    Route::post('/updateCartSchedule', [CartController::class, 'updateCartSchedule']);    
    
    /* Service APIs*/
    Route::post('/getServiceList', [ServiceController::class, 'getServiceList']);
    Route::post('/getSubServiceList', [ServiceController::class, 'getSubServiceList']);    
    Route::post('/searchServiceList', [ServiceController::class, 'searchServiceList']);
    Route::post('/searchSubServiceList', [ServiceController::class, 'searchSubServiceList']);
    Route::post('/getServiceProductList', [ServiceController::class, 'getServiceProductList']);
    Route::post('/getProviderServiceDetails', [ServiceController::class, 'getProviderServiceDetails']);    
     Route::post('/getProviderSubServiceDetails', [ServiceController::class, 'getProviderSubServiceDetails']);    
    Route::post('/addSubService', [ServiceController::class, 'addSubService']);
    Route::post('/removeSubService', [ServiceController::class, 'removeSubService']); 
    Route::post('/requestSubService', [ServiceController::class, 'requestSubService']);    
    Route::post('/requestSubServiceList', [ServiceController::class, 'requestSubServiceList']);    
    Route::post('/requestSubServiceDetails', [ServiceController::class, 'requestSubServiceDetails']);    
    
    /* Order APIs*/
    Route::post('/addCartProducts', [CartController::class, 'addCartProducts']);
    Route::post('/updateCartProducts', [CartController::class, 'updateCartProducts']);
    Route::post('/removeCartProducts', [CartController::class, 'removeCartProducts']);
    Route::post('/emptyCart', [CartController::class, 'emptyCart']);
    Route::post('/getCartDetails', [CartController::class, 'getCartDetails']);
    Route::post('/getUserCartDetails', [CartController::class, 'getUserCartDetails']);
    Route::post('/proceedCartRequest', [CartController::class, 'proceedCartRequest']);
    Route::post('/orderList', [OrderController::class, 'list']);
    Route::post('/orderDetails', [OrderController::class, 'orderDetails']);
    Route::post('/orderProductDetails', [OrderController::class, 'orderProductDetails']);
    Route::get('/order/bill-generate/{orderId}', [OrderController::class, 'billGeneratePDF']);
    
    Route::post('/updateCartAddress', [CartController::class, 'updateCartAddress']);
    Route::post('/updateLongLat', [UserController::class, 'updateLongLat']);
    Route::post('/getLongLat', [UserController::class, 'getLongLat']);
    
    /* Notification section*/
    Route::post('/getNotificationList', [NotificationController::class, 'getNotificationList']);
    Route::post('/getNotificationsDetails', [NotificationController::class, 'details']);
    Route::post('/notificationRead', [NotificationController::class, 'read']);
    Route::post('/notificationStatusUpdate', [NotificationController::class, 'statusUpdate']);
    
    
    
    Route::post('/workHistory', [OrderController::class, 'workHistory']);
    Route::post('/providerOrderRequestResponse', [OrderController::class, 'providerRequestResponse']);
    Route::post('/userOrderResponse', [OrderController::class, 'userOrderResponse']);
    Route::post('/orderWorkStatusUpdate', [OrderController::class, 'orderWorkStatusUpdate']);
    
    Route::post('/getChatList', [ChatController::class, 'getChatList']);
    Route::post('/getChatDetails', [ChatController::class, 'getChatDetails']);
    Route::post('/getUserChats', [ChatController::class, 'getUserChats']);
    Route::post('/getOrderChats', [ChatController::class, 'getOrderChats']);
    Route::post('/sendOrderChatMsg', [ChatController::class, 'sendOrderChatMsg']);
    Route::post('/sendChatMsg', [ChatController::class, 'sendChatMsg']);
    Route::post('/receiveOrderChatMsg', [ChatController::class, 'receiveOrderChatMsg']);
    Route::post('/sendSupportChatMsg', [ChatController::class, 'sendSupportChatMsg']);
    Route::post('/receiveSupportChatMsg', [ChatController::class, 'receiveSupportChatMsg']);
    Route::post('/deleteChat', [ChatController::class, 'deleteChat'])->name('delete-chat');
    
    
    Route::get('/getCoupons', [CartController::class, 'getCoupons']);
    Route::post('/verifyCoupon', [CartController::class, 'verifyCoupon']);
    Route::post('/switchRole', [UserController::class, 'switchRole']);
    
    
    Route::post('/userOrderReport', [OrderController::class, 'userOrderReport']);
    Route::post('/orderProductRating', [OrderController::class, 'orderProductRating']);
    
    
     Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/save-payment-details', [PaymentController::class, 'savePaymentDetails']);
    Route::post('/get-all-transactions', [PaymentController::class, 'getAllTransactions']);
     Route::post('/get-transaction', [PaymentController::class, 'getTransaction']);
    
    
});
 Route::get('/page', [PageController::class, 'getPageContent']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/request/{equipment_url}', 'PagesController@request');
Route::post('/request/create', 'RequestsController@create');
Route::post('/request/accept', 'RequestsController@accept');