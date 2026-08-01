// To parse this JSON data, do
//
//     final depositInsertResponseModel = depositInsertResponseModelFromJson(jsonString);

import 'dart:convert';

DepositInsertResponseModel depositInsertResponseModelFromJson(String str) => DepositInsertResponseModel.fromJson(json.decode(str));

String depositInsertResponseModelToJson(DepositInsertResponseModel data) => json.encode(data.toJson());

class DepositInsertResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  DepositInsertResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory DepositInsertResponseModel.fromJson(Map<String, dynamic> json) => DepositInsertResponseModel(
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
  Deposit? deposit;
  String? redirectUrl;

  Data({this.deposit, this.redirectUrl});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        deposit: json["deposit"] == null ? null : Deposit.fromJson(json["deposit"]),
        redirectUrl: json["redirect_url"],
      );

  Map<String, dynamic> toJson() => {
        "deposit": deposit?.toJson(),
        "redirect_url": redirectUrl,
      };
}

class Deposit {
  String? successUrl;
  String? failedUrl;

  int? id;

  Deposit({this.successUrl, this.failedUrl, this.id});

  factory Deposit.fromJson(Map<String, dynamic> json) => Deposit(
        successUrl: json["success_url"],
        failedUrl: json["failed_url"],
        id: json["id"],
      );

  Map<String, dynamic> toJson() => {
        "success_url": successUrl,
        "failed_url": failedUrl,
        "id": id,
      };
}
