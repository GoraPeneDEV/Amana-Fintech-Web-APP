// To parse this JSON data, do
//
//     final donationResponseModel = donationResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/donation/donation_submit_response_model.dart';
import 'package:ovopay/core/utils/url_container.dart';

DonationResponseModel donationResponseModelFromJson(String str) => DonationResponseModel.fromJson(json.decode(str));

String donationResponseModelToJson(DonationResponseModel data) => json.encode(data.toJson());

class DonationResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  DonationResponseModel({this.remark, this.status, this.message, this.data});

  factory DonationResponseModel.fromJson(Map<String, dynamic> json) => DonationResponseModel(
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
  List<String>? otpType;
  String? currentBalance;
  List<DonationOrganizationModel>? donationOrganizations;
  List<DonationDataModel>? latestDonationHistory;

  Data({
    this.otpType,
    this.currentBalance,
    this.donationOrganizations,
    this.latestDonationHistory,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        donationOrganizations: json["all_organization"] == null
            ? []
            : List<DonationOrganizationModel>.from(
                json["all_organization"]!.map(
                  (x) => DonationOrganizationModel.fromJson(x),
                ),
              ),
        latestDonationHistory: json["latest_donation"] == null
            ? []
            : List<DonationDataModel>.from(
                json["latest_donation"]!.map(
                  (x) => DonationDataModel.fromJson(x),
                ),
              ),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "all_organization": donationOrganizations == null ? [] : List<dynamic>.from(donationOrganizations!.map((x) => x.toJson())),
        "latest_donation": latestDonationHistory == null ? [] : List<dynamic>.from(latestDonationHistory!.map((x) => x.toJson())),
      };

  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class DonationOrganizationModel {
  int? id;
  String? name;
  String? details;
  String? image;
  String? status;
  String? createdAt;
  String? updatedAt;

  DonationOrganizationModel({
    this.id,
    this.name,
    this.details,
    this.image,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory DonationOrganizationModel.fromJson(Map<String, dynamic> json) => DonationOrganizationModel(
        id: json["id"],
        name: json["name"]?.toString(),
        details: json["details"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "details": details,
        "image": image,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
  String? getOrgImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/donation/$image';
      return imageUrl;
    }
  }
}
