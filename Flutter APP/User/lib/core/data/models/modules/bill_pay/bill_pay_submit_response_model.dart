// To parse this JSON data, do
//
//     final billPaySubmitResponseModel = billPaySubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';

BillPaySubmitResponseModel billPaySubmitResponseModelFromJson(String str) => BillPaySubmitResponseModel.fromJson(json.decode(str));

String billPaySubmitResponseModelToJson(BillPaySubmitResponseModel data) => json.encode(data.toJson());

class BillPaySubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BillPaySubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BillPaySubmitResponseModel.fromJson(Map<String, dynamic> json) => BillPaySubmitResponseModel(
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
  ModuleGlobalSubmitTransactionModel? billData;

  Data({this.billData});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        billData: json["bill"] == null ? null : ModuleGlobalSubmitTransactionModel.fromJson(json["bill"]),
      );

  Map<String, dynamic> toJson() => {"bill": billData?.toJson()};
}
