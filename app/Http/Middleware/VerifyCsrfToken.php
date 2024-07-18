<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        /*User API's */
        'api/register','api/login','api/forgetPassword','api/resetPassword','api/changePassword','api/updateAvailability',
    'api/changeLanguage','api/verifyEmailPhone','api/getProfileDetails','api/updateProfileDetails','api/makeUserFavorite',
    
    /* Company API's */
    'api/getCompanyDetails','api/updateCompanyDetails',
    
    /* Service API's*/
    'api/getSubServiceList','api/getServiceProductList','api/searchServiceList','api/searchSubServiceList',
    
    /*Profile settings */
    'api/updateProfileAlerts','api/updateDeviceDetails','api/updateLongLat','api/getLongLat','api/getServiceList',
    
    /* Chat API's*/
    'api/getChatList','api/getChatDetails','api/sendOrderChatMsg','api/sendChatMsg','api/getUserChats','/api/deleteChat',
    
    /*Cart API's*/
    'api/addCartProducts','api/updateCartProducts','api/removeCartProducts','api/emptyCart','api/getCartDetails','api/updateCartSchedule','api/getUserCartDetails','api/receiveOrderChatMsg','api/getOrderChats',
    
    /*Order API's*/
    'api/userOrderReport','api/orderProductRating','api/sendSupportChatMsg','api/receiveSupportChatMsg','api/sendChatMsg','api/verifyCoupon','api/getProviderServiceDetails','api/getProviderSubServiceDetails','api/addSubService','api/removeSubService','api/requestSubService','api/proceedCartRequest','api/cartOrderRequestList','api/switchRole','api/requestSubServiceList','api/requestSubServiceDetails','api/updateCartAddress','api/orderList','api/orderDetails','api/orderProductDetails','api/providerOrderRequestResponse','api/userOrderResponse','api/orderWorkStatusUpdate','api/workHistory',
    
    /* Notification API's*/
    'api/getNotificationList','api/getNotificationsDetails','api/notificationRead','api/statusUpdate',
    
    /* Service Request API's*/
    '/api/makeUserServiceRequest','/api/getUserServiceRequestDetail','/api/getUserServiceRequestList','/api/updateServiceRequestStatus',
    
    '/api/create-payment-intent','/api/save-payment-details','/api/get-all-transactions','/api/get-transaction'
    ];
    
}
