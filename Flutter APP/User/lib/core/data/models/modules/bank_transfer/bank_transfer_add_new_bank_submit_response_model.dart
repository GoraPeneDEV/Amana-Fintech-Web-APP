// To parse this JSON data, do
//
//     final bankTransferAddNewBankResponseModel = bankTransferAddNewBankResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_info_response_model.dart';

BankTransferAddNewBankSubmitResponseModel bankTransferAddNewBankResponseModelFromJson(String str) => BankTransferAddNewBankSubmitResponseModel.fromJson(json.decode(str));

String bankTransferAddNewBankResponseModelToJson(
  BankTransferAddNewBankSubmitResponseModel data,
) =>
    json.encode(data.toJson());

class BankTransferAddNewBankSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BankTransferAddNewBankSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BankTransferAddNewBankSubmitResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      BankTransferAddNewBankSubmitResponseModel(
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
  MyAddedBank? bank;

  Data({this.bank});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        bank: json["bank"] == null ? null : MyAddedBank.fromJson(json["bank"]),
      );

  Map<String, dynamic> toJson() => {"bank": bank?.toJson()};
}
