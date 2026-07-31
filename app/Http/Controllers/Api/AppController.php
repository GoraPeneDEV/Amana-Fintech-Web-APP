<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\ModuleSetting;
use App\Models\OperatingCountry;

class AppController extends Controller
{
    public function generalSetting()
    {
        $notify[] = 'General setting data';
        return apiResponse("general_setting", "success", $notify, [
            'general_setting'        => gs()->makeHidden('firebase_config', 'socialite_credentials'),
            'kyc_content'            => getContent('kyc.content', true),
            'maintenance_content'    => Frontend::where('data_keys', 'maintenance.data')->first(),
            'maintenance_image_path' => getFilePath('maintenance')
        ]);
    }

    public function getCountries()
    {
        $countries  = OperatingCountry::active()->orderby('name')->get();
        $notify[]   = 'Country List';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);

        return apiResponse("countries", "success", $notify, [
            'countries'             => $countries,
            'selected_country_code' => $mobileCode
        ]);
    }

    public function getLanguage($code)
    {
        $languages     = Language::get();
        $languageCodes = $languages->pluck('code')->toArray();

        if (!in_array($code, $languageCodes)) {
            $notify[] = 'Invalid code given';
            return apiResponse("invalid_code", "error", $notify);
        }

        $jsonFile = file_get_contents(resource_path('lang/' . $code . '.json'));
        $notify[] = 'Language';

        return apiResponse("language", "success", $notify, [
            'languages'  => $languages,
            'file'       => json_decode($jsonFile) ?? [],
            'image_path' => getFilePath('language')
        ]);
    }

    public function policies()
    {
        $policies = getContent('policy_pages.element', orderById: true);
        $notify[] = 'All policies';

        return apiResponse("policy_data", "success", $notify, [
            'policies' => $policies,
        ]);
    }


    public function faq()
    {
        $faq      = getContent('faq.element', orderById: true);
        $notify[] = 'FAQ';
        return apiResponse("faq", "success", $notify, [
            'faq' => $faq,
        ]);
    }

    public function moduleSetting()
    {
        $notify[] = 'Module setting data';

        $userModules     = ModuleSetting::where('user_type', 'USER')->get();
        $agentModules    = ModuleSetting::where('user_type', 'AGENT')->get();
        $merchantModules = ModuleSetting::where('user_type', 'MERCHANT')->get();

        return response()->json([
            'remark'  => 'module_setting',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'module_setting' => [
                    'user'     => $userModules,
                    'agent'    => $agentModules,
                    'merchant' => $merchantModules,
                ],
            ],
        ]);
    }
}
