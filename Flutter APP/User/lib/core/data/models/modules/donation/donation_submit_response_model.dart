// To parse this JSON data, do
//
//     final donationSubmitResponseModel = donationSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/donation/donation_response_model.dart';

DonationSubmitResponseModel donationSubmitResponseModelFromJson(String str) => DonationSubmitResponseModel.fromJson(json.decode(str));

String donationSubmitResponseModelToJson(DonationSubmitResponseModel data) => json.encode(data.toJson());

class DonationSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  DonationSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory DonationSubmitResponseModel.fromJson(Map<String, dynamic> json) => DonationSubmitResponseModel(
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
  DonationDataModel? donation;

  Data({this.donation});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        donation: json["donation"] == null ? null : DonationDataModel.fromJson(json["donation"]),
      );

  Map<String, dynamic> toJson() => {"donation": donation?.toJson()};
}

class DonationDataModel {
  String? charityId;
  String? userId;
  String? beforeCharge;
  String? amount;
  String? postBalance;
  String? charge;
  String? chargeType;
  String? trxType;
  String? remark;
  String? details;
  String? receiverId;
  String? receiverType;
  String? trx;
  String? reference;
  String? hideIdentity;
  String? updatedAt;
  String? createdAt;
  String? id;
  DonationOrganizationModel? donationFor;

  DonationDataModel({
    this.charityId,
    this.userId,
    this.beforeCharge,
    this.amount,
    this.postBalance,
    this.charge,
    this.chargeType,
    this.trxType,
    this.remark,
    this.details,
    this.receiverId,
    this.receiverType,
    this.trx,
    this.reference,
    this.hideIdentity,
    this.updatedAt,
    this.createdAt,
    this.id,
    this.donationFor,
  });

  factory DonationDataModel.fromJson(Map<String, dynamic> json) => DonationDataModel(
        charityId: json["charity_id"]?.toString(),
        userId: json["user_id"]?.toString(),
        beforeCharge: json["before_charge"]?.toString(),
        amount: json["amount"]?.toString(),
        postBalance: json["post_balance"]?.toString(),
        charge: json["charge"]?.toString(),
        chargeType: json["charge_type"]?.toString(),
        trxType: json["trx_type"]?.toString(),
        remark: json["remark"]?.toString(),
        details: json["details"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        receiverType: json["receiver_type"]?.toString(),
        trx: json["trx"]?.toString(),
        reference: json["reference"]?.toString(),
        hideIdentity: json["hide_identity"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        id: json["id"]?.toString(),
        donationFor: json["donation_for"] == null ? null : DonationOrganizationModel.fromJson(json["donation_for"]),
      );

  Map<String, dynamic> toJson() => {
        "charity_id": charityId,
        "user_id": userId,
        "before_charge": beforeCharge,
        "amount": amount,
        "post_balance": postBalance,
        "charge": charge,
        "charge_type": chargeType,
        "trx_type": trxType,
        "remark": remark,
        "details": details,
        "receiver_id": receiverId,
        "receiver_type": receiverType,
        "trx": trx,
        "reference": reference,
        "hide_identity": hideIdentity,
        "updated_at": updatedAt,
        "created_at": createdAt,
        "id": id,
        "donation_for": donationFor?.toJson(),
      };
}
