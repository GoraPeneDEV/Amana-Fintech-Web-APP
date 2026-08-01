// To parse this JSON data, do
//
//     final bankTransferHistoryResponseModel = bankTransferHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_info_response_model.dart';

BankTransferHistoryResponseModel bankTransferHistoryResponseModelFromJson(
  String str,
) =>
    BankTransferHistoryResponseModel.fromJson(json.decode(str));

String bankTransferHistoryResponseModelToJson(
  BankTransferHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class BankTransferHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BankTransferHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BankTransferHistoryResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      BankTransferHistoryResponseModel(
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
        history: json["bank_transfers"] == null ? null : History.fromJson(json["bank_transfers"]),
      );

  Map<String, dynamic> toJson() => {"bank_transfers": history?.toJson()};
}

class History {
  List<BankTransferDataModel>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<BankTransferDataModel>.from(
                json["data"]!.map((x) => BankTransferDataModel.fromJson(x)),
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

class BankTransferDataModel {
  int? id;
  String? userId;
  String? bankId;
  String? accountHolder;
  String? accountNumber;
  String? amount;
  String? charge;
  String? total;
  String? trx;
  List<UsersDynamicFormSubmittedDataModel>? userData;
  String? adminFeedback;
  String? status;
  String? createdAt;
  String? updatedAt;
  String? statusBadge;
  BankDataModel? bank;

  BankTransferDataModel({
    this.id,
    this.userId,
    this.bankId,
    this.accountHolder,
    this.accountNumber,
    this.amount,
    this.charge,
    this.total,
    this.trx,
    this.userData,
    this.adminFeedback,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.statusBadge,
    this.bank,
  });

  factory BankTransferDataModel.fromJson(Map<String, dynamic> json) => BankTransferDataModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        bankId: json["bank_id"]?.toString(),
        accountHolder: json["account_holder"]?.toString(),
        accountNumber: json["account_number"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        total: json["total"]?.toString(),
        trx: json["trx"]?.toString(),
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
        bank: json["bank"] == null ? null : BankDataModel.fromJson(json["bank"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "bank_id": bankId,
        "account_holder": accountHolder,
        "account_number": accountNumber,
        "amount": amount,
        "charge": charge,
        "total": total,
        "trx": trx,
        "user_data": userData == null ? [] : List<dynamic>.from(userData!.map((x) => x.toJson())),
        "admin_feedback": adminFeedback,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "status_badge": statusBadge,
        "bank": bank?.toJson(),
      };
}
