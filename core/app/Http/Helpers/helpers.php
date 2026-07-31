<?php

use App\Constants\Status;
use App\Events\QrCodeLogin;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\Export\ExportManager;
use App\Lib\FileManager;
use App\Models\Agent;
use App\Models\Merchant;
use App\Models\ModuleSetting;
use App\Models\OperatingCountry;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAction;
use App\Models\UserLogin;
use App\Notify\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

function systemDetails()
{
    $system['name']                = 'Amana';
    $system['web_version']         = '1.6';
    $system['admin_panel_version'] = '1.0.1';
    $system['mobile_app_version']  = '1.6';
    $system['android_version']     = '7.0';
    $system['ios_version']         = '16.0';
    $system['flutter_version']     = '3.27.2';

    return $system;
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function activeTemplate($asset = false)
{
    $template = session('template') ?? gs('active_template');
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null)
{
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}
function siteFavicon()
{
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = null, $separate = true, $exceptZeros = false, $currencyFormat = true, $separator = '')
{
    if (!$decimal) {
        $decimal = gs('allow_precision');
    }

    if ($separate && !$separator) {
        $separator = str_replace(['space', 'none'], [' ', ''], gs('thousand_separator'));
    }

    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } elseif (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
        }
    }
    return $printAmount;
}

function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}
function cryptoQRqw($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}

function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = "#";
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}

function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null, $isAvatar = false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($isAvatar) {
        return asset('assets/images/avatar.jpg');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null)
{
    $notSendTemplate = [
        'DEPOSIT_COMPLETE',
        'ADMIN_SUPPORT_REPLY',
        'CASH_IN_COMMISSION_AGENT',
        'CASH_IN',
        'CASH_IN_AGENT',
        'SEND_MONEY',
        'CASH_OUT',
        'MONEY_REQUESTED',
        'MONEY_REQUESTED',
        'MICROFINANCE_PAY_APPROVE',
        'MICROFINANCE_PAY_REJECT',
        'DONATION',
        'MAKE_PAYMENT_RECEIVE',
        'MERCHANT_PAYMENT_COMPLETE',
        'UTILITY_BILL_REJECT',
        'UTILITY_BILL_APPROVE',
        'EDUCATION_FEE_REJECT',
        'EDUCATION_FEE_APPROVE',
        'BANK_TRANSFER_REJECT',
        'BANK_TRANSFER_APPROVE',
        'BANK_TRANSFER_APPROVE',
        'CASH_OUT_TO_AGENT',
        'CASH_OUT_COMMISSION_AGENT',
        'RECEIVED_MONEY',
        'SEND_MONEY',
    ];


    if (is_null($sendVia)) {
        $sendNotificationChannel = [
            'email' => 'email',
            'sms'   => 'sms',
            'push'  => 'push',
        ];
    } else {
        $sendNotificationChannel = $sendVia;
    }

    $isSendToEmail  = is_null($sendVia) ? true : in_array('email', $sendVia);
    $isSendToMobile = is_null($sendVia) ? true : in_array('sms', $sendVia);
    $isSendToPush   = is_null($sendVia) ? true : in_array('push', $sendVia);

    if (@$user->en == Status::DISABLE && $isSendToEmail && in_array($templateName, $notSendTemplate)) {
        unset($sendNotificationChannel['email']);
    }

    if (@$user->sn == Status::DISABLE && $isSendToMobile && in_array($templateName, $notSendTemplate)) {
        unset($sendNotificationChannel['sms']);
    }

    if (@$user->pn == Status::DISABLE && $isSendToPush && in_array($templateName, $notSendTemplate)) {
        unset($sendNotificationChannel['push']);
    }

    if (!count($sendNotificationChannel)) return;

    $globalShortCodes = [
        'site_name'       => gs('site_name'),
        'site_currency'   => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify               = new Notify($sendNotificationChannel);
    $notify->templateName = $templateName;
    $notify->shortCodes   = $shortCodes;
    $notify->user         = $user;
    $notify->createLog    = $createLog;
    $notify->pushImage    = $pushImage;
    $notify->userColumn   = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}


function getPaginate($paginate = null)
{
    if (!$paginate) {
        $paginate = request()->paginate ??   gs('paginate_number');
    }
    return $paginate;
}

function getOrderBy($orderBy = null)
{
    if (!$orderBy) {
        $orderBy = request()->order_by ?? 'desc';
    }
    return $orderBy;
}

function paginateLinks($data, $view = null)
{
    $paginationHtml = $data->appends(request()->all())->links($view);
    echo '<div class="pagination-wrapper w-100">' . $paginationHtml . '</div>';
}

function menuActive($routeName, $param = null, $className = 'active')
{

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $className;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $className;
            else return;
        }
        return $className;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function checkSpecialRegex($string)
{
    $regex = '/[+\-*\/%==!=<>]=?|&&|\|\||\.\.|::|->|@|\$|\^|~|\[|\]|\{|\}|\(|\)|;|,|=>|:]/';
    return preg_match($regex, $string);
}

function showDateTime($date, $format = null, $lang = null)
{
    if (!$date) {
        return '-';
    }
    if (!$lang) {
        $lang = session()->get('lang');
        if (!$lang) {
            $lang = getDefaultLang();
        }
    }

    if (!$format) {
        $format = gs('date_format') . ' ' . gs('time_format');
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getDefaultLang()
{
    return config('app.local') ?? 'en';
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}

function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) return @$general->$key;
    return $general;
}

function isImage($string)
{
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);
    return in_array($fileExtension, $allowedExtensions);
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

function convertToReadableSize($size)
{
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int)$matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}


function frontendImage($sectionName, $image, $size = null, $seo = false)
{
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function apiResponse(string $remark, string $status, array $message = [], array $data = [], $statusCode = 200): JsonResponse
{
    $response = [
        'remark'  => $remark,
        'status'  => $status
    ];

    if (count($message)) $response['message'] = $message;
    if (count($data)) $response['data'] = $data;

    return response()->json($response, $statusCode);
}

function exportData($baseQuery, $exportType, $modelName, $printPageSize = "A4 portrait")
{
    try {
        return (new ExportManager($baseQuery, $modelName, $exportType, $printPageSize))->export();
    } catch (Exception $ex) {
        $notify[] = ['error', $ex->getMessage()];
        return back()->withNotify($notify);
    }
}

function os(): array
{
    return [
        'windows',
        'windows 10',
        'windows 7',
        'windows 8',
        'windows xp' . 'linux',
        'apple',
        'android',
        'ubuntu',
    ];
}

function supportedDateFormats(): array
{
    return  [
        'Y-m-d',
        'd-m-Y',
        'd/m/Y',
        'm-d-Y',
        'm/d/Y',
        'D, M j, Y',
        'l, F j, Y',
        'F j, Y',
        'M j, Y'
    ];
}

function supportedTimeFormats(): array
{
    return  [
        'H:i:s',
        'H:i',
        'h:i A',
        'g:i a',
        'g:i:s a'
    ];
}

function supportedThousandSeparator(): array
{
    return  [
        ","     => "Comma",
        "."     => "Dot",
        "'"     => "Apostrophe",
        "space" => "Space",
        "none"  => "None",
    ];
}

function agent()
{
    return isApiRequest() ? auth()->user() : auth()->guard('agent')->user();
}

function merchant()
{
    return isApiRequest() ? auth()->user() : auth()->guard('merchant')->user();
}

function keyGenerator($length = 50)
{
    $characters = 'abcdefghijklmnpqrstuvwxyz0123456789';
    $string     = '';
    $max        = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    return $string;
}

function module($key, $moduleQuery = null)
{
    $userType = "USER";

    if (isApiRequest()) {
        if (request()->is('api/agent/*')) {
            $userType = "AGENT";
        }
    } else {
        if (request()->is('agent/*')) {
            $userType = "AGENT";
        }
    }

    $moduleQuery = $moduleQuery ?: ModuleSetting::query();
    return ModuleSetting::where('user_type', $userType)
        ->where('slug', $key)
        ->first();
}

function userGuard()
{
    if (auth()->check()) {
        $user = auth()->user();
    } elseif (auth()->guard('agent')->check()) {
        $user = auth()->guard('agent')->user();
    } else {
        $user = auth()->guard('merchant')->user();
    }

    if (!$user) {
        return null;
    }

    $userType = strtoupper(substr($user->getTable(), 0, -1));

    return [
        'user_type' => $userType,
    ];
}

function userGuardType()
{
    if (auth()->check()) {
        $userType = 'USER';
        $user     = auth()->user();
    } elseif (auth()->guard('agent')->check()) {
        $userType = 'AGENT';
        $user     = auth()->guard('agent')->user();
    } elseif (auth()->guard('merchant')->check()) {
        $userType = 'MERCHANT';
        $user     = auth()->guard('merchant')->user();
    }

    return [
        'user'  => @$user,
        'type'  => @$userType,
    ];
}

function logoutAnother($currentUser)
{
    $user = ['user', 'agent', 'merchant'];

    foreach ($user as $name) {
        if ($name != $currentUser) {
            if ($name == 'user') {
                auth()->logout();
            } else {
                auth()->guard($name)->logout();
            }
        }
    }
}

function sendOtp($user)
{
    $user->ver_code         = verificationCode(6);
    $user->ver_code_send_at = Carbon::now();
    $user->save();

    notify($user, 'SVER_CODE', [
        'code' => $user->ver_code
    ], ['sms']);
}


function showBadge($status)
{
    $class = 'badge badge--';

    if ($status) {
        $class .= 'success';
        $text = trans('Yes');
    } else {
        $class .= 'danger';
        $text = trans('No');
    }

    return '<span class="' . $class . '">' . $text . '</span>';
}

function getOtpRemark(): array
{
    return [
        'send_money',
        'make_payment',
        'cash_out',
        'request_money',
        'request_money_received',
        'donation',
        'utility_bill',
        'education_fee',
        'microfinance',
        'bank_transfer',
        'cash_in',
        'payment_capture',
        'gift_card',
        'mobile_recharge',
        'investment'
    ];
}

function getOtpValidationRules()
{
    if (!gs('otp_verification')) return [];

    return [
        'verification_type' => 'required|in:' . implode(',', gs('supported_otp_type') ?? [])
    ];
}

function getAvailableOtpVerificationType()
{
    return ['email', 'sms'];
}

function storeAuthorizedTransactionData($remark, $details = [], $user = null)
{

    $user = $user ?? auth()->user();

    if (gs('otp_verification')) {
        $otp       = verificationCode(6);
        $expiredAt = now()->addSeconds((int) gs('otp_expiration'));
        $nextStep  = "otp";
        $sentVia   = request()->verification_type;
        $message[] = "The verification code sent";
        notify(
            $user,
            'SEND_OTP',
            ['code' => $otp],
            [request()->verification_type ?? 'email']
        );

        if (request()->verification_type == 'email') {
            $codeSentMessage = "Please provide the verification code below, We have sent  6-digit verification codes to your email at " . showEmailAddress($user->email);
        } else {
            $codeSentMessage = "Please provide the verification code below, We have sent  6-digit verification codes to your phone number at " . showMobileNumber($user->mobileNumber);
        }
    } else {
        $otp             = null;
        $sentVia         = null;
        $nextStep        = "pin";
        $expiredAt       = null;
        $message[]       = "Please provide the pin";
        $codeSentMessage = null;
    }

    if (isApiRequest()) {
        $platform = Status::PLATFORM_APP;
    } else {
        $platform = Status::PLATFORM_WEB;
    }

    $userAction             = new UserAction();
    $userAction->user_id    = $user->id;
    $userAction->remark     = $remark;
    $userAction->otp        = $otp;
    $userAction->details    = $details;
    $userAction->expired_at = $expiredAt;
    $userAction->platform   = $platform;
    $userAction->sent_via   = $sentVia;
    $userAction->save();

    info('nana');
    return apiResponse($nextStep, 'success', $message, [
        'next_step'         => $nextStep,
        'code_sent_message' => $codeSentMessage,
    ]);
}

function mobileNumberValidationRule($userSelectCountry = null, $digitValidation = true)
{
    if ($digitValidation) {
        $digit = is_null($userSelectCountry) ? @getUserSelectCountry()->mobile_number_digit : $userSelectCountry->mobile_number_digit;
        return ['mobile_number' => 'required|numeric|digits:' . $digit];
    } else {
        return ['mobile_number' => 'required|numeric'];
    }
}

function getUserSelectCountry()
{
    $country =  OperatingCountry::active()->where('id', request()->country ?? 0)->first();

    if (!$country) {
        throw ValidationException::withMessages(['error' => 'The requested country does not exist']);
    }

    return $country;
}

function pinValidationRule($isConfirm = false)
{
    if ($isConfirm) {
        return ['pin' => 'required|confirmed|numeric|digits:' . gs('user_pin_digits')];
    } else {
        return ['pin' => 'required|numeric|digits:' . gs('user_pin_digits')];
    }
}

function isApiRequest()
{
    return request()->is('api/*');
}

function responseManager(string $remark, string $message, string $responseType = 'error', array $responseData = [], array $igNoreOnApi = [])
{
    $isApi = isApiRequest();

    if ($isApi) {
        $notify[]     = $message;
        $ignoreForApi = array_merge($igNoreOnApi, ['view', 'pageTitle']);
        $responseData = array_diff_key(
            $responseData,
            array_flip($ignoreForApi)
        );
        $responseDataToSnake = array_combine(
            array_map(function ($key) {
                return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            }, array_keys($responseData)),
            array_values($responseData)
        );

        if (in_array($remark, getOtpRemark())) {
            if (gs('otp_verification')) {
                $supportedOtpTypes = [];
                foreach (gs('supported_otp_type') ?? [] as $otpType) {
                    $supportedOtpTypes[] = $otpType;
                }
                $responseDataToSnake['supported_otp_types'] = $supportedOtpTypes;
            }
            $responseDataToSnake['current_balance']     = auth()->user()->balance;
        }
        return apiResponse($remark, $responseType, $notify, $responseDataToSnake);
    }

    if (array_key_exists('view', $responseData)) {
        return view($responseData['view'], $responseData);
    }

    $notify[] = [$responseType, $message];
    return back()->withNotify($notify);
}

function moduleIsEnable($moduleName, $enableModules)
{
    return in_array($moduleName, $enableModules);
}

function getQrCodeUrl($guard = 'user')
{

    if ($guard ==  'user') {
        $user       = auth()->user();
        $columnName = 'user_id';
    }
    if ($guard ==  'merchant') {
        $user       = isApiRequest() ? auth()->user() :  auth('merchant')->user();
        $columnName = 'merchant_id';
    }
    if ($guard ==  'agent') {
        $user       = isApiRequest() ? auth()->user() :  auth('agent')->user();
        $columnName = 'agent_id';
    }

    $qrCode   = $user->qrCode;

    if (!$qrCode) {
        $qrCode              = new QrCode();
        $qrCode->$columnName = $user->id;
        $qrCode->unique_code = keyGenerator(15);
        $qrCode->save();
    }

    $uniqueCode = $qrCode->unique_code;
    $qrCode     = cryptoQR($uniqueCode);

    return $qrCode;
}
function getQrCodeUrlForLogin($guard = "user", $checkExists = true)
{
    $columnName = "for_" . $guard . "_login";

    if ($checkExists) {
        $qrCode = QrCode::where($columnName, Status::YES)->first();
    } else {
        $qrCode = null;
    }

    if (!$qrCode) {
        $qrCode              = new QrCode();
        $qrCode->$columnName   = Status::YES;
        $qrCode->unique_code = keyGenerator(15);
        $qrCode->save();
    }

    $code = base64_encode($qrCode->unique_code);

    return $code;
}


function generateUniqueTrxNumber()
{
    $length = 12;

    while (0 == 0) {
        $trx    = getTrx($length);
        $exists = Transaction::where('trx', $trx)->first();
        if ($exists) {
            $length++;
        } else {
            break;
        };
    }

    return $trx;
}

function printVirtualCardNumber($card)
{
    return '**** **** **** ' . $card->last4;
}

function findUserWithUsernameOrMobile($notFoundMessage = null)
{
    $search = request()->user;
    $user   = User::whereRaw("CONCAT(dial_code, mobile) = ?", [$search])->get();

    if ($user->isEmpty()) {
        $user = User::where("mobile", $search)->get();
    }

    if ($user->count() > 1) {
        throw ValidationException::withMessages(['error' => 'Please enter the mobile number with dial code']);
    }

    if ($user->isEmpty()) {
        $user = User::active()->where('username', $search)->get();
    }

    $user = $user->first();

    if (!$user) {
        throw ValidationException::withMessages(['error' => $notFoundMessage ?? "The user is not found"]);
    }

    if ($user->status != Status::USER_ACTIVE || $user->kv != Status::KYC_VERIFIED) {
        throw ValidationException::withMessages(['error' => $notFoundMessage ?? "The user is not found"]);
    }
    return $user;
}
function findAgentWithUsernameOrMobile($notFoundMessage = null)
{
    $search = request()->agent;
    $agent   = Agent::whereRaw("CONCAT(dial_code, mobile) = ?", [$search])->get();

    if ($agent->isEmpty()) {
        $agent = Agent::where("mobile", $search)->get();
    }

    if ($agent->count() > 1) {
        throw ValidationException::withMessages(['error' => 'Please enter the mobile number with dial code']);
    }

    if ($agent->isEmpty()) {
        $agent = Agent::active()->where('username', $search)->get();
    }

    $agent = $agent->first();

    if (!$agent) {
        throw ValidationException::withMessages(['error' => $notFoundMessage ?? "The agent is not found"]);
    }

    if ($agent->status != Status::USER_ACTIVE || $agent->kv != Status::KYC_VERIFIED) {
        throw ValidationException::withMessages(['error' => $notFoundMessage ?? "The agent is not found"]);
    }
    return $agent;
}
function findMerchantWithUsernameOrMobile($notFoundMessage = null)
{
    $search   = request()->merchant;
    $merchant = Merchant::whereRaw("CONCAT(dial_code, mobile) = ?", [$search])->get();

    if ($merchant->isEmpty()) {
        $merchant = Merchant::where("mobile", $search)->get();
    }

    if ($merchant->count() > 1) {
        throw ValidationException::withMessages(['error' => 'Please enter the mobile number with dial code']);
    }

    if ($merchant->isEmpty()) {
        $merchant = Merchant::active()->where('username', $search)->get();
    }

    $merchant = $merchant->first();

    if (!$merchant) {
        throw ValidationException::withMessages(['error' => $notFoundMessage ?? "The merchant is not found"]);
    }

    if ($merchant->status != Status::USER_ACTIVE || $merchant->kv != Status::KYC_VERIFIED) {
        throw ValidationException::withMessages(['error' => $notFoundMessage ?? "The merchant is not found"]);
    }
    return $merchant;
}

function getAdmin($column = null)
{
    $admin = isApiRequest() ? auth()->user() : auth('admin')->user();
    if (is_null($column)) return $admin;

    return $admin->$column;
}

function qrCodeLoginAttempt($guard, $encodeId, $encodedCode)
{
    try {
        $code = base64_decode($encodedCode);
    } catch (Exception $ex) {
        $notify[] = "The something went to wrong";
        return apiResponse('exception', "error", $notify);
    }

    $columnName = "for_" . $guard . "_login";
    $qrCode = QrCode::where($columnName, Status::YES)->where('unique_code', $code)->first();

    if (!$qrCode) {
        $message[] = "The qr code token is mismatch, Please try again";
        return apiResponse('expired', 'error', $message);
    }

    try {
        $id = base64_decode($encodeId);
    } catch (Exception $ex) {
        $notify[] = "The something went to wrong";
        return apiResponse('exception', "error", $notify);
    }

    $guardData = [
        'user'     => [
            'model_class' => User::class,
            'column_name' => 'user_id'
        ],
        'agent'    => [
            'model_class' => Agent::class,
            'column_name' => 'agent_id'
        ],
        'merchant' => [
            'model_class' => Merchant::class,
            'column_name' => 'merchant_id'
        ],
    ][$guard];

    $model = $guardData['model_class'];
    $user  = $model::find($id);

    if (!$user) {
        $notify[] = "The $guard account is not found";
        return apiResponse('not_found', "error", $notify);
    }

    if ($user->status == Status::USER_BAN) {
        $notify[] = "Your account is banned";
        return apiResponse('banned', "error", $notify);
    }

    if ($user->status == Status::USER_DELETE) {
        $notify[] = "Your account is deleted";
        return apiResponse('banned', "error", $notify);
    }

    //check the token 
    $rawToken = base64_decode(request()->s_token);

    if (!$rawToken) {
        $notify[] = "Something went to wrong. Please try again";
        return apiResponse('exception', "error", $notify);
    }

    try {
        $rawToken = base64_decode(request()->s_token);
    } catch (Exception $ex) {
        $notify[] = "Something went to wrong. Please try again";
        return apiResponse('exception', "error", $notify);
    }

    [$tokenId, $token] = explode('|', $rawToken, 2);
    $accessToken = PersonalAccessToken::find($tokenId);

    if (!$accessToken) {
        $notify[] = "Something went to wrong. Please try again";
        return apiResponse('error', "error", $notify);
    }

    if (!hash_equals($accessToken->token, hash('sha256', $token))) {
        $notify[] = "Something went to wrong. Please try again";
        return apiResponse('error', "error", $notify);
    }

    $tokenName = $guard . "_token";

    if ($tokenName != $accessToken->name) {
        $notify[] = "Something went to wrong. Please try again";
        return apiResponse('error', "error", $notify);
    }

    $tokenUser = @$accessToken->tokenable;

    if (@$tokenUser->username != @$user->username) {
        $notify[] = "Something went to wrong. Please try again";
        return apiResponse('error', "error", $notify);
    }

    if ($guard == 'user') {
        Auth::loginUsingId($id);
    } else {
        Auth::guard($guard)->loginUsingId($id);
    }

    //save ip data 
    $ip        = getRealIP();
    $exist     = UserLogin::where('user_ip', $ip)->first();
    $userLogin = new UserLogin();

    if ($exist) {
        $userLogin->longitude    = $exist->longitude;
        $userLogin->latitude     = $exist->latitude;
        $userLogin->city         = $exist->city;
        $userLogin->country_code = $exist->country_code;
        $userLogin->country      = $exist->country;
    } else {
        $info                    = json_decode(json_encode(getIpInfo()), true);
        $userLogin->longitude    = @implode(',', $info['long']);
        $userLogin->latitude     = @implode(',', $info['lat']);
        $userLogin->city         = @implode(',', $info['city']);
        $userLogin->country_code = @implode(',', $info['code']);
        $userLogin->country      = @implode(',', $info['country']);
    }

    $columnName = $guardData['column_name'];

    $userAgent              = osBrowser();
    $userLogin->$columnName = $user->id;
    $userLogin->user_ip     = $ip;

    $userLogin->browser = @$userAgent['browser'];
    $userLogin->os      = @$userAgent['os_platform'];
    $userLogin->save();

    $notify[] = "Login successfully";
    return apiResponse('success', "success", $notify);
}

function verifyQrCodeForLogin($encodedCode, $guard)
{
    try {
        $code = base64_decode($encodedCode);
    } catch (Exception $ex) {
        $notify[] = "The something went to wrong";
        return apiResponse('exception', "error", $notify);
    }

    $columnName = "for_" . $guard . "_login";
    $qrCode     = QrCode::where($columnName, Status::YES)->where('unique_code', $code)->first();

    if (!$qrCode) {
        $message[] = "The qr code is not available, Please try again";
        return apiResponse('expired', 'error', $message);
    }

    $user  = auth()->user();
    $token = request()->bearerToken();

    event(new QrCodeLogin("$guard-qr-code-login", [
        "user"    => base64_encode($user->id),
        "s_token" => base64_encode($token),
        'qr_code' => $encodedCode,
    ]));

    $message[] = "Qr code login successfully";
    return  apiResponse('success', 'success', $message);
}
