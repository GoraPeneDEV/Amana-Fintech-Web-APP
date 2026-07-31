<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiProviderSettingController extends Controller
{
    public function providerConfiguration()
    {
        $pageTitle = 'API Providers';
        $providers = ApiProvider::get();

        return view('admin.api_config.api_provider', compact('pageTitle', 'providers'));
    }

    public function providerConfigurationUpdate(Request $request, $id)
    {


        $provider = ApiProvider::find($id);

        if (!$provider) {
            return apiResponse('not_found', 'error', ['The provider is not found']);
        }

        $configWithValue = [];
        foreach ($provider->config as $config) {
            $name                   = $config->name;
            if ($name === 'test_mode') {
                $validationRules[$name] = 'required|in:1,2';
            } else {
                $validationRules[$name] = $config->is_required ? 'required' : 'nullable';
            }
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return apiResponse('not_found', 'error', $validator->errors()->all());
        }

        foreach ($provider->config as $config) {
            $name              = $config->name;
            $config->value     = isset($request->$name) && !empty($request->$name) ? $request->$name : '';
            $configWithValue[] = $config;
        }

        $accessToken = collect($provider->access_token)->map(function ($item) {
            $item->value      = null;
            $item->expired_at = null;
            return $item;
        });

        $provider->config       = $configWithValue;
        $provider->access_token = $accessToken;
        $provider->status       = $request->status;
        $provider->save();

        return apiResponse('configuration_updated', 'success', ['The provider configuration updated successfully']);
    }


    private function supportedModules()
    {
        return ['utility_bill', 'airtime'];
    }
}
