// To parse this JSON data, do
//
//     final microFinanceSubmitResponseModel = microFinanceSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';

MicroFinanceSubmitResponseModel microFinanceSubmitResponseModelFromJson(
  String str,
) =>
    MicroFinanceSubmitResponseModel.fromJson(json.decode(str));

String microFinanceSubmitResponseModelToJson(
  MicroFinanceSubmitResponseModel data,
) =>
    json.encode(data.toJson());

class MicroFinanceSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  MicroFinanceSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory MicroFinanceSubmitResponseModel.fromJson(Map<String, dynamic> json) => MicroFinanceSubmitResponseModel(
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
  ModuleGlobalSubmitTransactionModel? microfinancePayData;

  Data({this.microfinancePayData});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        microfinancePayData: json["microfinance"] == null ? null : ModuleGlobalSubmitTransactionModel.fromJson(json["microfinance"]),
      );

  Map<String, dynamic> toJson() => {
        "microfinance": microfinancePayData?.toJson(),
      };
}
