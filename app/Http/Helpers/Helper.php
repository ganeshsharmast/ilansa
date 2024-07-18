<?php
namespace App\Http\Helpers;

use App\Model\Status;

class Helper
{
    /*Generate referral code. */
    public static function generateReferralCode() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeLength = 9;
        $referralCode = '';
    
        for ($i = 0; $i < $codeLength; $i++) {
            $referralCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $referralCode;
    }
    
    /*Generate OTP code. */
    public static function generateOTPCode() {
        $characters = '0123456789';
        $codeLength = 6;
        $referralCode = '';
    
        for ($i = 0; $i < $codeLength; $i++) {
            $referralCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $referralCode;
    }
    
    /*Get Status List. */
    public static function status() {
        echo "--<pre>";
        print_r((new Status())->get()->all());
        die;
        $characters = '0123456789';
        $codeLength = 6;
        $referralCode = '';
    
        for ($i = 0; $i < $codeLength; $i++) {
            $referralCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $referralCode;
    }

}


?>