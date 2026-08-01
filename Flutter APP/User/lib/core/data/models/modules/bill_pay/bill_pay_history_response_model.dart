// To parse this JSON data, do
//
//     final billPayHistoryResponseModel = billPayHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/modules/bill_pay/bill_pay_response_model.dart';

BillPayHistoryResponseModel billPayHistoryResponseModelFromJson(String str) => BillPayHistoryResponseModel.fromJson(json.decode(str));

String billPayHistoryResponseModelToJson(BillPayHistoryResponseModel data) => json.encode(data.toJson());

class BillPayHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BillPayHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BillPayHistoryResponseModel.fromJson(Map<String, dynamic> json) => BillPayHistoryResponseModel(
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
  History? history;

  Data({this.history});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        history: json["utility_bills"] == null ? null : History.fromJson(json["utility_bills"]),
      );

  Map<String, dynamic> toJson() => {"utility_bills": history?.toJson()};
}

class History {
  List<LatestPayBillHistoryModel>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<LatestPayBillHistoryModel>.from(
                json["data"]!.map((x) => LatestPayBillHistoryModel.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"],
        path: json["path"],
      );

  Map<String, dynamic> toJson() => {
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
        "path": path,
      };
}

class LatestPayBillHistoryModel {
  int? id;
  String? userId;
  String? companyId;
  String? amount;
  String? charge;
  String? total;
  String? trx;
  String? token;
  List<UsersDynamicFormSubmittedDataModel>? userData;
  String? adminFeedback;
  String? status;
  String? createdAt;
  String? updatedAt;
  String? statusBadge;
  BillPayCompany? company;

  LatestPayBillHistoryModel({
    this.id,
    this.userId,
    this.companyId,
    this.amount,
    this.charge,
    this.total,
    this.trx,
    this.token,
    this.userData,
    this.adminFeedback,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.statusBadge,
    this.company,
  });

  factory LatestPayBillHistoryModel.fromJson(Map<String, dynamic> json) => LatestPayBillHistoryModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        companyId: json["company_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        total: json["total"]?.toString(),
        trx: json["trx"]?.toString(),
        token: json["token"]?.toString(),
        userData: json["user_data"] == null
            ? []
            : List<UsersDynamicFormSubmittedDataModel>.from(
                json["user_data"]!.map(
                  (x) => UsersDynamicFormSubmittedDataModel.fromJson(x),
                ),
              ),
        adminFeedback: json["admin_feedback"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        statusBadge: json["status_badge"]?.toString(),
        company: json["company"] == null ? null : BillPayCompany.fromJson(json["company"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "company_id": companyId,
        "amount": amount,
        "charge": charge,
        "total": total,
        "trx": trx,
        "token": token,
        "user_data": userData == null ? [] : List<dynamic>.from(userData!.map((x) => x.toJson())),
        "admin_feedback": adminFeedback,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "status_badge": statusBadge,
        "company": company?.toJson(),
      };
}
