<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo()
    {
        $data['withdrawVerify'] = [
            'path' => 'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path'      => 'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      => 'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/default.png',
        ];
        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path'      => 'assets/images/logo_icon',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/extensions',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      => 'assets/images/user/profile',
            'size'      => '300x300',
        ];
        $data['agentProfile'] = [
            'path'      => 'assets/images/agent/profile',
            'size'      => '300x300',
        ];
        $data['merchantProfile'] = [
            'path'      => 'assets/images/merchant/profile',
            'size'      => '300x300',
        ];
        $data['adminProfile'] = [
            'path'      => 'assets/admin/images/profile',
            'size'      => '400x400',
        ];
        $data['push'] = [
            'path'      => 'assets/images/push_notification',
        ];
        $data['appPurchase'] = [
            'path'      => 'assets/in_app_purchase_config',
        ];
        $data['maintenance'] = [
            'path'      => 'assets/images/maintenance',
            'size'      => '600x600',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '80x80'
        ];
        $data['gateway'] = [
            'path' => 'assets/images/gateway',
            'size' => ''
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw_method',
            'size' => ''
        ];
        $data['pushConfig'] = [
            'path'      => 'assets/admin',
        ];
        $data['qr_code_template'] = [
            'path' => 'assets/images/qr_code_template',
            'size' => '794x1123',
        ];
        $data['temporary'] = [
            'path'      => 'assets/images/temporary'
        ];
        $data['utility'] = [
            'path' => 'assets/images/setup_utility',
            'size' => '100x100',
        ];
        $data['microfinance'] = [
            'path' => 'assets/images/microfinance',
            'size' => '100x100',
        ];
        $data['mobile_operator'] = [
            'path' => 'assets/images/mobile_operator',
            'size' => '100x100',
        ];
        $data['bank_transfer'] = [
            'path' => 'assets/images/bank_transfer',
            'size' => '100x100',
        ];
        $data['donation'] = [
            'path' => 'assets/images/donation',
            'size' => '100x100',
        ];
        $data['category'] = [
            'path' => 'assets/images/category',
            'size' => '100x100',
        ];
        $data['education_fee'] = [
            'path' => 'assets/images/education_fee',
            'size' => '100x100',
        ];
        $data['banner'] = [
            'path' => 'assets/images/banner',
            'size' => '945x300',
        ];
        $data['offer'] = [
            'path' => 'assets/images/offer',
            'size' => '400x250',
        ];
        $data['virtualCard'] = [
            'path' => 'assets/images/user/virtual_card'
        ];
        $data['preloader'] = [
            'path' => 'assets/images/preloader',
        ];
        $data['api'] = [
            'path' => 'assets/images/api',
            'size' => '200x200',
        ];
        return $data;
    }
}
