// To parse this JSON data, do
//
//     final generalSettingResponseModel = generalSettingResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/helper/string_format_helper.dart';

GeneralSettingResponseModel generalSettingResponseModelFromJson(String str) => GeneralSettingResponseModel.fromJson(json.decode(str));

String generalSettingResponseModelToJson(GeneralSettingResponseModel data) => json.encode(data.toJson());

class GeneralSettingResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  GeneralSettingResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory GeneralSettingResponseModel.fromJson(Map<String, dynamic> json) => GeneralSettingResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: (json["message"] as List<dynamic>).toStringList(),
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "remark": remark,
        "status": status,
        "message": message,
        "data": data?.toJson(),
      };
}

class Data {
  GeneralSetting? generalSetting;
  String? socialLoginRedirect;
  KycContent? kycContent;
  MaintenanceContent? maintenanceContent;
  String? maintenanceImagePath;
  Data({
    this.generalSetting,
    this.socialLoginRedirect,
    this.kycContent,
    this.maintenanceContent,
    this.maintenanceImagePath,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        generalSetting: json["general_setting"] == null ? null : GeneralSetting.fromJson(json["general_setting"]),
        socialLoginRedirect: json["social_login_redirect"]?.toString(),
        kycContent: json["kyc_content"] == null ? null : KycContent.fromJson(json["kyc_content"]),
        maintenanceContent: json["maintenance_content"] == null ? null : MaintenanceContent.fromJson(json["maintenance_content"]),
        maintenanceImagePath: json["maintenance_image_path"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "general_setting": generalSetting?.toJson(),
        "social_login_redirect": socialLoginRedirect,
        "kyc_content": kycContent?.toJson(),
        "maintenance_content": maintenanceContent?.toJson(),
        "maintenance_image_path": maintenanceImagePath,
      };
}

class GeneralSetting {
  int? id;
  String? siteName;
  List<String>? quickAmounts;
  String? curText;
  String? curSym;
  String? emailFrom;
  String? emailFromName;
  String? smsTemplate;
  String? qrCodeTemplate;
  String? smsFrom;
  String? pushTitle;
  String? pushTemplate;
  String? baseColor;
  String? secondaryColor;
  FirebaseConfig? firebaseConfig;
  GlobalShortcodes? globalShortcodes;
  String? kv;
  String? agentKv;
  String? merchantKv;
  String? ev;
  String? en;
  String? sv;
  String? sn;
  String? pn;
  String? otpVerification;
  String? otpExpiration;
  String? forceSsl;
  String? inAppPayment;
  String? maintenanceMode;
  String? securePassword;
  String? agree;
  String? multiLanguage;
  String? registration;
  String? agentRegistration;
  String? merchantRegistration;
  String? agentVerification;
  String? merchantVerification;
  String? activeTemplate;
  String? socialiteCredentials;
  String? lastCron;
  String? availableVersion;
  String? systemCustomized;
  String? paginateNumber;
  String? currencyFormat;
  String? timeFormat;
  String? dateFormat;
  String? timezone;
  String? allowPrecision;
  String? thousandSeparator;
  List<String>? supportedOtpType;
  String? userPinDigits;
  String? agentPanelColor;
  String? merchantPanelColor;
  String? qrcodeLogin;
  String? createdAt;
  String? updatedAt;

  GeneralSetting({
    this.id,
    this.siteName,
    this.quickAmounts,
    this.curText,
    this.curSym,
    this.emailFrom,
    this.emailFromName,
    this.smsTemplate,
    this.qrCodeTemplate,
    this.smsFrom,
    this.pushTitle,
    this.pushTemplate,
    this.baseColor,
    this.secondaryColor,
    this.firebaseConfig,
    this.globalShortcodes,
    this.kv,
    this.agentKv,
    this.merchantKv,
    this.ev,
    this.en,
    this.sv,
    this.sn,
    this.pn,
    this.otpVerification,
    this.otpExpiration,
    this.forceSsl,
    this.inAppPayment,
    this.maintenanceMode,
    this.securePassword,
    this.agree,
    this.multiLanguage,
    this.registration,
    this.agentRegistration,
    this.merchantRegistration,
    this.agentVerification,
    this.merchantVerification,
    this.activeTemplate,
    this.socialiteCredentials,
    this.lastCron,
    this.availableVersion,
    this.systemCustomized,
    this.paginateNumber,
    this.currencyFormat,
    this.timeFormat,
    this.dateFormat,
    this.timezone,
    this.allowPrecision,
    this.thousandSeparator,
    this.supportedOtpType,
    this.userPinDigits,
    this.agentPanelColor,
    this.merchantPanelColor,
    this.qrcodeLogin,
    this.createdAt,
    this.updatedAt,
  });

  factory GeneralSetting.fromJson(Map<String, dynamic> json) => GeneralSetting(
        id: json["id"],
        siteName: json["site_name"]?.toString(),
        quickAmounts: json["quick_amounts"] == null ? [] : List<String>.from(json["quick_amounts"]!.map((x) => x)),
        curText: json["cur_text"]?.toString(),
        curSym: json["cur_sym"]?.toString(),
        emailFrom: json["email_from"]?.toString(),
        emailFromName: json["email_from_name"]?.toString(),
        smsTemplate: json["sms_template"]?.toString(),
        qrCodeTemplate: json["qr_code_template"]?.toString(),
        smsFrom: json["sms_from"]?.toString(),
        pushTitle: json["push_title"]?.toString(),
        pushTemplate: json["push_template"]?.toString(),
        baseColor: json["base_color"]?.toString(),
        secondaryColor: json["secondary_color"]?.toString(),
        firebaseConfig: json["firebase_config"] == null ? null : FirebaseConfig.fromJson(json["firebase_config"]),
        globalShortcodes: json["global_shortcodes"] == null ? null : GlobalShortcodes.fromJson(json["global_shortcodes"]),
        kv: json["kv"]?.toString(),
        agentKv: json["agent_kv"]?.toString(),
        merchantKv: json["merchant_kv"]?.toString(),
        ev: json["ev"]?.toString(),
        en: json["en"]?.toString(),
        sv: json["sv"]?.toString(),
        sn: json["sn"]?.toString(),
        pn: json["pn"]?.toString(),
        otpVerification: json["otp_verification"]?.toString(),
        otpExpiration: json["otp_expiration"]?.toString(),
        forceSsl: json["force_ssl"]?.toString(),
        inAppPayment: json["in_app_payment"]?.toString(),
        maintenanceMode: json["maintenance_mode"]?.toString(),
        securePassword: json["secure_password"]?.toString(),
        agree: json["agree"]?.toString(),
        multiLanguage: json["multi_language"]?.toString(),
        registration: json["registration"]?.toString(),
        agentRegistration: json["agent_registration"]?.toString(),
        merchantRegistration: json["merchant_registration"]?.toString(),
        agentVerification: json["agent_verification"]?.toString(),
        merchantVerification: json["merchant_verification"]?.toString(),
        activeTemplate: json["active_template"]?.toString(),
        socialiteCredentials: json["socialite_credentials"]?.toString(),
        lastCron: json["last_cron"]?.toString(),
        availableVersion: json["available_version"]?.toString(),
        systemCustomized: json["system_customized"]?.toString(),
        paginateNumber: json["paginate_number"]?.toString(),
        currencyFormat: json["currency_format"]?.toString(),
        timeFormat: json["time_format"]?.toString(),
        dateFormat: json["date_format"]?.toString(),
        timezone: json["timezone"]?.toString(),
        allowPrecision: json["allow_precision"]?.toString(),
        thousandSeparator: json["thousand_separator"]?.toString(),
        supportedOtpType: json["supported_otp_type"] == null ? [] : List<String>.from(json["supported_otp_type"]!.map((x) => x)),
        userPinDigits: json["user_pin_digits"]?.toString(),
        agentPanelColor: json["agent_panel_color"]?.toString(),
        merchantPanelColor: json["merchant_panel_color"]?.toString(),
        qrcodeLogin: json["qrcode_login"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "site_name": siteName,
        "quick_amounts": quickAmounts == null ? [] : List<dynamic>.from(quickAmounts!.map((x) => x)),
        "cur_text": curText,
        "cur_sym": curSym,
        "email_from": emailFrom,
        "email_from_name": emailFromName,
        "sms_template": smsTemplate,
        "qr_code_template": qrCodeTemplate,
        "sms_from": smsFrom,
        "push_title": pushTitle,
        "push_template": pushTemplate,
        "base_color": baseColor,
        "secondary_color": secondaryColor,
        "firebase_config": firebaseConfig?.toJson(),
        "global_shortcodes": globalShortcodes?.toJson(),
        "kv": kv,
        "agent_kv": agentKv,
        "merchant_kv": merchantKv,
        "ev": ev,
        "en": en,
        "sv": sv,
        "sn": sn,
        "pn": pn,
        "otp_verification": otpVerification,
        "otp_expiration": otpExpiration,
        "force_ssl": forceSsl,
        "in_app_payment": inAppPayment,
        "maintenance_mode": maintenanceMode,
        "secure_password": securePassword,
        "agree": agree,
        "multi_language": multiLanguage,
        "registration": registration,
        "agent_registration": agentRegistration,
        "merchant_registration": merchantRegistration,
        "agent_verification": agentVerification,
        "merchant_verification": merchantVerification,
        "active_template": activeTemplate,
        "socialite_credentials": socialiteCredentials,
        "last_cron": lastCron,
        "available_version": availableVersion,
        "system_customized": systemCustomized,
        "paginate_number": paginateNumber,
        "currency_format": currencyFormat,
        "time_format": timeFormat,
        "date_format": dateFormat,
        "timezone": timezone,
        "allow_precision": allowPrecision,
        "thousand_separator": thousandSeparator,
        "supported_otp_type": supportedOtpType == null ? [] : List<dynamic>.from(supportedOtpType!.map((x) => x)),
        "user_pin_digits": userPinDigits,
        "agent_panel_color": agentPanelColor,
        "merchant_panel_color": merchantPanelColor,
        "qrcode_login": qrcodeLogin,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class FirebaseConfig {
  String? apiKey;
  String? authDomain;
  String? projectId;
  String? storageBucket;
  String? messagingSenderId;
  String? appId;
  String? measurementId;
  String? serverKey;

  FirebaseConfig({
    this.apiKey,
    this.authDomain,
    this.projectId,
    this.storageBucket,
    this.messagingSenderId,
    this.appId,
    this.measurementId,
    this.serverKey,
  });

  factory FirebaseConfig.fromJson(Map<String, dynamic> json) => FirebaseConfig(
        apiKey: json["apiKey"]?.toString(),
        authDomain: json["authDomain"]?.toString(),
        projectId: json["projectId"]?.toString(),
        storageBucket: json["storageBucket"]?.toString(),
        messagingSenderId: json["messagingSenderId"]?.toString(),
        appId: json["appId"]?.toString(),
        measurementId: json["measurementId"]?.toString(),
        serverKey: json["serverKey"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "apiKey": apiKey,
        "authDomain": authDomain,
        "projectId": projectId,
        "storageBucket": storageBucket,
        "messagingSenderId": messagingSenderId,
        "appId": appId,
        "measurementId": measurementId,
        "serverKey": serverKey,
      };
}

class GlobalShortcodes {
  String? siteName;
  String? siteCurrency;
  String? currencySymbol;

  GlobalShortcodes({this.siteName, this.siteCurrency, this.currencySymbol});

  factory GlobalShortcodes.fromJson(Map<String, dynamic> json) => GlobalShortcodes(
        siteName: json["site_name"]?.toString(),
        siteCurrency: json["site_currency"]?.toString(),
        currencySymbol: json["currency_symbol"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "site_name": siteName,
        "site_currency": siteCurrency,
        "currency_symbol": currencySymbol,
      };
}

class SocialiteCredentials {
  SocialiteCredentialsValue? google;
  SocialiteCredentialsValue? facebook;
  SocialiteCredentialsValue? linkedin;

  SocialiteCredentials({this.google, this.facebook, this.linkedin});

  factory SocialiteCredentials.fromJson(Map<String, dynamic> json) => SocialiteCredentials(
        google: json["google"] == null ? null : SocialiteCredentialsValue.fromJson(json["google"]),
        facebook: json["facebook"] == null ? null : SocialiteCredentialsValue.fromJson(json["facebook"]),
        linkedin: json["linkedin"] == null ? null : SocialiteCredentialsValue.fromJson(json["linkedin"]),
      );

  Map<String, dynamic> toJson() => {
        "google": google?.toJson(),
        "facebook": facebook?.toJson(),
        "linkedin": linkedin?.toJson(),
      };
}

class SocialiteCredentialsValue {
  String? clientId;
  String? clientSecret;
  String? status;

  SocialiteCredentialsValue({this.clientId, this.clientSecret, this.status});

  factory SocialiteCredentialsValue.fromJson(Map<String, dynamic> json) => SocialiteCredentialsValue(
        clientId: json["client_id"]?.toString(),
        clientSecret: json["client_secret"]?.toString(),
        status: json["status"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "client_id": clientId,
        "client_secret": clientSecret,
        "status": status,
      };
}

class KycContent {
  String? id;
  String? dataKeys;
  KycContentDataValues? dataValues;
  String? seoContent;
  String? tempName;
  String? slug;
  String? createdAt;
  String? updatedAt;

  KycContent({
    this.id,
    this.dataKeys,
    this.dataValues,
    this.seoContent,
    this.tempName,
    this.slug,
    this.createdAt,
    this.updatedAt,
  });

  factory KycContent.fromJson(Map<String, dynamic> json) => KycContent(
        id: json["id"]?.toString(),
        dataKeys: json["data_keys"]?.toString(),
        dataValues: json["data_values"] == null ? null : KycContentDataValues.fromJson(json["data_values"]),
        seoContent: json["seo_content"]?.toString(),
        tempName: json["tempname"]?.toString(),
        slug: json["slug"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "data_keys": dataKeys,
        "data_values": dataValues?.toJson(),
        "seo_content": seoContent,
        "tempname": tempName,
        "slug": slug,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class KycContentDataValues {
  String? required;
  String? pending;
  String? reject;

  KycContentDataValues({this.required, this.pending, this.reject});

  factory KycContentDataValues.fromJson(Map<String, dynamic> json) => KycContentDataValues(
        required: json["required"]?.toString(),
        pending: json["pending"]?.toString(),
        reject: json["reject"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "required": required,
        "pending": pending,
        "reject": reject,
      };
}

class MaintenanceContent {
  String? id;
  String? dataKeys;
  MaintenanceContentDataValues? dataValues;
  String? seoContent;
  String? tempName;
  String? slug;
  String? createdAt;
  String? updatedAt;

  MaintenanceContent({
    this.id,
    this.dataKeys,
    this.dataValues,
    this.seoContent,
    this.tempName,
    this.slug,
    this.createdAt,
    this.updatedAt,
  });

  factory MaintenanceContent.fromJson(Map<String, dynamic> json) => MaintenanceContent(
        id: json["id"]?.toString(),
        dataKeys: json["data_keys"]?.toString(),
        dataValues: json["data_values"] == null ? null : MaintenanceContentDataValues.fromJson(json["data_values"]),
        seoContent: json["seo_content"]?.toString(),
        tempName: json["tempname"]?.toString(),
        slug: json["slug"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "data_keys": dataKeys,
        "data_values": dataValues?.toJson(),
        "seo_content": seoContent,
        "tempname": tempName,
        "slug": slug,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class MaintenanceContentDataValues {
  String? description;
  String? image;

  MaintenanceContentDataValues({this.description, this.image});

  factory MaintenanceContentDataValues.fromJson(Map<String, dynamic> json) => MaintenanceContentDataValues(
        description: json["description"]?.toString(),
        image: json["image"]?.toString(),
      );

  Map<String, dynamic> toJson() => {"description": description, "image": image};
}
