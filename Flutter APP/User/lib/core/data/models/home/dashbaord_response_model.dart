// To parse this JSON data, do
//
//     final dashboardResponseModel = dashboardResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/home/offers_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/utils/url_container.dart';

DashboardResponseModel dashboardResponseModelFromJson(String str) => DashboardResponseModel.fromJson(json.decode(str));

String dashboardResponseModelToJson(DashboardResponseModel data) => json.encode(data.toJson());

class DashboardResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  DashboardResponseModel({this.remark, this.status, this.message, this.data});

  factory DashboardResponseModel.fromJson(Map<String, dynamic> json) => DashboardResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: json["message"] == null ? [] : List<String>.from(json["message"]!.map((x) => x)),
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "remark": remark,
        "status": status,
        "message": message == null ? [] : List<dynamic>.from(message!.map((x) => x)),
        "data": data?.toJson(),
      };
}

class Data {
  UserModel? user;
  List<UsersDynamicFormSubmittedDataModel>? kycData;
  List<BannerModel>? banners;
  List<OfferModel>? offers;

  Data({this.user, this.kycData, this.banners, this.offers});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        user: json["user"] == null ? null : UserModel.fromJson(json["user"]),
        kycData: json["kyc_data"] == null
            ? []
            : List<UsersDynamicFormSubmittedDataModel>.from(
                json["kyc_data"]!.map(
                  (x) => UsersDynamicFormSubmittedDataModel.fromJson(x),
                ),
              ),
        banners: json["banners"] == null
            ? []
            : List<BannerModel>.from(
                json["banners"]!.map((x) => BannerModel.fromJson(x)),
              ),
        offers: json["offers"] == null
            ? []
            : List<OfferModel>.from(
                json["offers"]!.map((x) => OfferModel.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "user": user?.toJson(),
        "kyc_data": kycData == null ? [] : List<dynamic>.from(kycData!.map((x) => x.toJson())),
        "banners": banners == null ? [] : List<dynamic>.from(banners!.map((x) => x.toJson())),
        "offers": offers == null ? [] : List<dynamic>.from(offers!.map((x) => x.toJson())),
      };
}

class BannerModel {
  int? id;
  String? type;
  String? description;
  String? link;
  String? image;
  String? status;
  String? createdAt;
  String? updatedAt;

  BannerModel({
    this.id,
    this.type,
    this.description,
    this.link,
    this.image,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory BannerModel.fromJson(Map<String, dynamic> json) => BannerModel(
        id: json["id"],
        type: json["type"]?.toString(),
        description: json["description"]?.toString(),
        link: json["link"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "type": type,
        "description": description,
        "link": link,
        "image": image,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };

  String? getBannerImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/banner/$image';
      return imageUrl;
    }
  }
}
