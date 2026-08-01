// To parse this JSON data, do
//
//     final requestMoneyInfoResponseModel = requestMoneyInfoResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/modules/request_money/request_money_history_model.dart';

RequestMoneyInfoResponseModel requestMoneyInfoResponseModelFromJson(
  String str,
) =>
    RequestMoneyInfoResponseModel.fromJson(json.decode(str));

String requestMoneyInfoResponseModelToJson(
  RequestMoneyInfoResponseModel data,
) =>
    json.encode(data.toJson());

class RequestMoneyInfoResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  RequestMoneyInfoResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory RequestMoneyInfoResponseModel.fromJson(Map<String, dynamic> json) => RequestMoneyInfoResponseModel(
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
  List<RequestMoneyHistoryDataModel>? latestRequestMoney;
  List<String>? otpType;
  String? currentBalance;
  String? pendingRequestCounter;
  GlobalChargeModel? charge;
  Data({
    this.latestRequestMoney,
    this.otpType,
    this.currentBalance,
    this.pendingRequestCounter,
    this.charge,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        latestRequestMoney: json["latest_request_money"] == null
            ? []
            : List<RequestMoneyHistoryDataModel>.from(
                json["latest_request_money"]!.map(
                  (x) => RequestMoneyHistoryDataModel.fromJson(x),
                ),
              ),
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString(),
        pendingRequestCounter: json["pending_request"]?.toString(),
        charge: json["charge"] == null ? null : GlobalChargeModel.fromJson(json["charge"]),
      );

  Map<String, dynamic> toJson() => {
        "latest_request_money": latestRequestMoney == null ? [] : List<dynamic>.from(latestRequestMoney!.map((x) => x.toJson())),
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "pending_request": pendingRequestCounter,
        "charge": charge?.toJson(),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}
