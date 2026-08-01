// To parse this JSON data, do
//
//     final userModel = userModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/utils/url_container.dart';

UserModel userModelFromJson(String str) => UserModel.fromJson(json.decode(str));

String userModelToJson(UserModel data) => json.encode(data.toJson());

class UserModel {
  int? id;
  String? firstname;
  String? lastname;
  String? username;
  String? balance;
  String? email;
  String? dialCode;
  String? mobile;
  String? refBy;
  String? image;
  String? countryName;
  String? countryCode;
  String? city;
  String? state;
  String? zip;
  String? address;
  String? status;
  String? kycRejectionReason;
  String? kv;
  String? ev;
  String? sv;
  String? en;
  String? pn;
  String? sn;
  String? isAllowPromotionalNotify;
  String? profileComplete;
  String? verCodeSendAt;
  String? ts;
  String? tv;
  String? tsc;
  String? banReason;
  String? provider;
  String? providerId;
  String? createdAt;
  String? updatedAt;
  String? imageUrl;

  UserModel({
    this.id,
    this.firstname,
    this.lastname,
    this.username,
    this.balance,
    this.email,
    this.dialCode,
    this.mobile,
    this.refBy,
    this.image,
    this.countryName,
    this.countryCode,
    this.city,
    this.state,
    this.zip,
    this.address,
    this.status,
    this.kycRejectionReason,
    this.kv,
    this.ev,
    this.sv,
    this.en,
    this.pn,
    this.sn,
    this.isAllowPromotionalNotify,
    this.profileComplete,
    this.verCodeSendAt,
    this.ts,
    this.tv,
    this.tsc,
    this.banReason,
    this.provider,
    this.providerId,
    this.createdAt,
    this.updatedAt,
    this.imageUrl,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) => UserModel(
        id: json["id"],
        firstname: json["firstname"]?.toString(),
        lastname: json["lastname"]?.toString(),
        username: json["username"]?.toString(),
        balance: json["balance"]?.toString(),
        email: json["email"]?.toString(),
        dialCode: json["dial_code"]?.toString(),
        mobile: json["mobile"]?.toString(),
        refBy: json["ref_by"]?.toString(),
        image: json["image"]?.toString(),
        countryName: json["country_name"]?.toString(),
        countryCode: json["country_code"]?.toString(),
        city: json["city"]?.toString(),
        state: json["state"]?.toString(),
        zip: json["zip"]?.toString(),
        address: json["address"]?.toString(),
        status: json["status"]?.toString(),
        kycRejectionReason: json["kyc_rejection_reason"]?.toString(),
        kv: json["kv"]?.toString(),
        ev: json["ev"]?.toString(),
        sv: json["sv"]?.toString(),
        en: json["en"]?.toString(),
        pn: json["pn"]?.toString(),
        sn: json["sn"]?.toString(),
        isAllowPromotionalNotify: json["is_allow_promotional_notify"]?.toString(),
        profileComplete: json["profile_complete"]?.toString(),
        verCodeSendAt: json["ver_code_send_at"]?.toString(),
        ts: json["ts"]?.toString(),
        tv: json["tv"]?.toString(),
        tsc: json["tsc"]?.toString(),
        banReason: json["ban_reason"]?.toString(),
        provider: json["provider"]?.toString(),
        providerId: json["provider_id"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        imageUrl: json["image_src"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "firstname": firstname,
        "lastname": lastname,
        "username": username,
        "balance": balance,
        "email": email,
        "dial_code": dialCode,
        "mobile": mobile,
        "ref_by": refBy,
        "image": image,
        "country_name": countryName,
        "country_code": countryCode,
        "city": city,
        "state": state,
        "zip": zip,
        "address": address,
        "status": status,
        "kyc_rejection_reason": kycRejectionReason,
        "kv": kv,
        "ev": ev,
        "sv": sv,
        "en": en,
        "pn": pn,
        "sn": sn,
        "is_allow_promotional_notify": isAllowPromotionalNotify,
        "profile_complete": profileComplete,
        "ver_code_send_at": verCodeSendAt,
        "ts": ts,
        "tv": tv,
        "tsc": tsc,
        "ban_reason": banReason,
        "provider": provider,
        "provider_id": providerId,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "image_src": imageUrl,
      };

  String getFullName() {
    return "${firstname?.trim() ?? ""}${firstname?.toString() == "" ? "" : " "}${lastname?.trim() ?? ""}";
  }

  String getUserMobileNo({bool withCountryCode = false}) {
    if (withCountryCode) {
      return "${dialCode ?? ""}${mobile?.trim() ?? ""}";
    }
    return mobile?.trim() ?? "";
  }

  String? getUserImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.userImagePath}/$image';
      return imageUrl;
    }
  }

  String? getAgentImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.agentImagePath}/$image';
      return imageUrl;
    }
  }

  String? getMerchantImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.merchantImagePath}/$image';
      return imageUrl;
    }
  }
}
