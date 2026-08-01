// To parse this JSON data, do
//
//     final airtimeRechargeHistoryResponseModel = airtimeRechargeHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_operators_response_model.dart';

AirtimeRechargeHistoryResponseModel airtimeRechargeHistoryResponseModelFromJson(
  String str,
) =>
    AirtimeRechargeHistoryResponseModel.fromJson(json.decode(str));

String airtimeRechargeHistoryResponseModelToJson(
  AirtimeRechargeHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class AirtimeRechargeHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  AirtimeRechargeHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory AirtimeRechargeHistoryResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      AirtimeRechargeHistoryResponseModel(
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
  TopUps? topUps;

  Data({this.topUps});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        topUps: json["top_ups"] == null ? null : TopUps.fromJson(json["top_ups"]),
      );

  Map<String, dynamic> toJson() => {"top_ups": topUps?.toJson()};
}

class TopUps {
  List<AirtimeDataModel>? data;

  String? nextPageUrl;

  TopUps({this.data, this.nextPageUrl});

  factory TopUps.fromJson(Map<String, dynamic> json) => TopUps(
        data: json["data"] == null
            ? []
            : List<AirtimeDataModel>.from(
                json["data"]!.map((x) => AirtimeDataModel.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
      };
}

class AirtimeDataModel {
  int? id;
  String? userId;
  String? operatorId;
  String? amount;
  String? charge;
  String? postBalance;
  String? trx;
  String? details;
  String? dialCode;
  String? mobileNumber;
  String? createdAt;
  String? updatedAt;
  OperatorData? operator;

  AirtimeDataModel({
    this.id,
    this.userId,
    this.operatorId,
    this.amount,
    this.charge,
    this.postBalance,
    this.trx,
    this.details,
    this.dialCode,
    this.mobileNumber,
    this.createdAt,
    this.updatedAt,
    this.operator,
  });

  factory AirtimeDataModel.fromJson(Map<String, dynamic> json) => AirtimeDataModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        operatorId: json["operator_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        postBalance: json["post_balance"]?.toString(),
        trx: json["trx"]?.toString(),
        details: json["details"]?.toString(),
        dialCode: json["dial_code"]?.toString(),
        mobileNumber: json["mobile_number"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        operator: json["operator"] == null ? null : OperatorData.fromJson(json["operator"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "operator_id": operatorId,
        "amount": amount,
        "charge": charge,
        "post_balance": postBalance,
        "trx": trx,
        "details": details,
        "dial_code": dialCode,
        "mobile_number": mobileNumber,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "operator": operator?.toJson(),
      };
}

class Link {
  String? url;
  String? label;
  bool? active;

  Link({this.url, this.label, this.active});

  factory Link.fromJson(Map<String, dynamic> json) => Link(url: json["url"], label: json["label"], active: json["active"]);

  Map<String, dynamic> toJson() => {
        "url": url,
        "label": label,
        "active": active,
      };
}
