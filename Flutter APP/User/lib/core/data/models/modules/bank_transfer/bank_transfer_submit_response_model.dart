// To parse this JSON data, do
//
//     final bankTransferSubmitResponseModel = bankTransferSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_info_response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';

BankTransferSubmitResponseModel bankTransferSubmitResponseModelFromJson(
  String str,
) =>
    BankTransferSubmitResponseModel.fromJson(json.decode(str));

String bankTransferSubmitResponseModelToJson(
  BankTransferSubmitResponseModel data,
) =>
    json.encode(data.toJson());

class BankTransferSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BankTransferSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BankTransferSubmitResponseModel.fromJson(Map<String, dynamic> json) => BankTransferSubmitResponseModel(
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
  ModuleGlobalSubmitTransactionModel? bankTransfer;

  Data({this.bankTransfer});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        bankTransfer: json["bank_transfer"] == null
            ? null
            : ModuleGlobalSubmitTransactionModel.fromJson(
                json["bank_transfer"],
              ),
      );

  Map<String, dynamic> toJson() => {"bank_transfer": bankTransfer?.toJson()};
}

class BankTransferSubmitModel {
  String? accountNumber;
  String? accountHolder;
  String? userId;
  String? bankId;
  String? amount;
  String? charge;
  String? total;
  String? trx;
  String? status;
  String? adminFeedback;
  List<UsersDynamicFormSubmittedDataModel>? userData;
  String? updatedAt;
  String? createdAt;
  String? id;
  String? statusBadge;
  BankDataModel? bank;

  BankTransferSubmitModel({
    this.accountNumber,
    this.accountHolder,
    this.userId,
    this.bankId,
    this.amount,
    this.charge,
    this.total,
    this.trx,
    this.status,
    this.userData,
    this.updatedAt,
    this.createdAt,
    this.id,
    this.statusBadge,
    this.adminFeedback,
    this.bank,
  });

  factory BankTransferSubmitModel.fromJson(Map<String, dynamic> json) => BankTransferSubmitModel(
        accountNumber: json["account_number"]?.toString(),
        accountHolder: json["account_holder"]?.toString(),
        userId: json["user_id"]?.toString(),
        bankId: json["bank_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        total: json["total"]?.toString(),
        trx: json["trx"]?.toString(),
        status: json["status"]?.toString(),
        userData: json["user_data"] == null
            ? []
            : List<UsersDynamicFormSubmittedDataModel>.from(
                json["user_data"]!.map(
                  (x) => UsersDynamicFormSubmittedDataModel.fromJson(x),
                ),
              ),
        updatedAt: json["updated_at"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        id: json["id"]?.toString(),
        statusBadge: json["status_badge"]?.toString(),
        adminFeedback: json["admin_feedback"]?.toString(),
        bank: json["bank"] == null ? null : BankDataModel.fromJson(json["bank"]),
      );

  Map<String, dynamic> toJson() => {
        "account_number": accountNumber,
        "account_holder": accountHolder,
        "user_id": userId,
        "bank_id": bankId,
        "amount": amount,
        "charge": charge,
        "total": total,
        "trx": trx,
        "status": status,
        "user_data": userData == null ? [] : List<dynamic>.from(userData!.map((x) => x.toJson())),
        "updated_at": updatedAt,
        "created_at": createdAt,
        "id": id,
        "status_badge": statusBadge,
        "admin_feedback": adminFeedback,
        "bank": bank,
      };
}
