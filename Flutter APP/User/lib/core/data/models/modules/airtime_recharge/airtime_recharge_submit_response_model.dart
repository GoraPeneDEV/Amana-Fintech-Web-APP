// To parse this JSON data, do
//
//     final airTimeRechargeSubmitResponseModel = airTimeRechargeSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';

AirTimeRechargeSubmitResponseModel airTimeRechargeSubmitResponseModelFromJson(
  String str,
) =>
    AirTimeRechargeSubmitResponseModel.fromJson(json.decode(str));

String airTimeRechargeSubmitResponseModelToJson(
  AirTimeRechargeSubmitResponseModel data,
) =>
    json.encode(data.toJson());

class AirTimeRechargeSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  AirTimeRechargeSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory AirTimeRechargeSubmitResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      AirTimeRechargeSubmitResponseModel(
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
  String? redirectType;
  ModuleGlobalSubmitTransactionModel? airtime;

  Data({this.redirectType, this.airtime});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        redirectType: json["redirect_type"],
        airtime: json["airtime"] == null ? null : ModuleGlobalSubmitTransactionModel.fromJson(json["airtime"]),
      );

  Map<String, dynamic> toJson() => {
        "redirect_type": redirectType,
        "airtime": airtime?.toJson(),
      };
}
