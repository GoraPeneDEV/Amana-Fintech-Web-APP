// To parse this JSON data, do
//
//     final makePaymentResponseModel = makePaymentResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/user/user_model.dart';

MakePaymentResponseModel makePaymentResponseModelFromJson(String str) => MakePaymentResponseModel.fromJson(json.decode(str));

String makePaymentResponseModelToJson(MakePaymentResponseModel data) => json.encode(data.toJson());

class MakePaymentResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  MakePaymentResponseModel({this.remark, this.status, this.message, this.data});

  factory MakePaymentResponseModel.fromJson(Map<String, dynamic> json) => MakePaymentResponseModel(
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
  List<String>? otpType;
  String? currentBalance;
  List<LatestMakePaymentHistory>? latestMakePaymentHistory;
  MakePaymentSubmitInfo? makePayment;
  Data({
    this.otpType,
    this.currentBalance,
    this.latestMakePaymentHistory,
    this.makePayment,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        latestMakePaymentHistory: json["latest_make_payments"] == null
            ? []
            : List<LatestMakePaymentHistory>.from(
                json["latest_make_payments"]!.map(
                  (x) => LatestMakePaymentHistory.fromJson(x),
                ),
              ),
        makePayment: json["payment"] == null ? null : MakePaymentSubmitInfo.fromJson(json["payment"]),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "latest_make_payments": latestMakePaymentHistory == null
            ? []
            : List<dynamic>.from(
                latestMakePaymentHistory!.map((x) => x.toJson()),
              ),
        "payment": makePayment?.toJson(),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class LatestMakePaymentHistory {
  int? id;
  String? userId;
  String? merchantId;
  String? amount;
  String? charge;
  String? merchantAmount;
  String? trx;
  String? userPostBalance;
  String? merchantPostBalance;
  String? userDetails;
  String? merchantDetails;
  String? createdAt;
  String? updatedAt;
  UserModel? merchant;

  LatestMakePaymentHistory({
    this.id,
    this.userId,
    this.merchantId,
    this.amount,
    this.charge,
    this.merchantAmount,
    this.trx,
    this.userPostBalance,
    this.merchantPostBalance,
    this.userDetails,
    this.merchantDetails,
    this.createdAt,
    this.updatedAt,
    this.merchant,
  });

  factory LatestMakePaymentHistory.fromJson(Map<String, dynamic> json) => LatestMakePaymentHistory(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        merchantId: json["merchant_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        merchantAmount: json["merchant_amount"]?.toString(),
        trx: json["trx"]?.toString(),
        userPostBalance: json["user_post_balance"]?.toString(),
        merchantPostBalance: json["merchant_post_balance"]?.toString(),
        userDetails: json["user_details"]?.toString(),
        merchantDetails: json["merchant_details"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        merchant: json["merchant"] == null ? null : UserModel.fromJson(json["merchant"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "merchant_id": merchantId,
        "amount": amount,
        "charge": charge,
        "merchant_amount": merchantAmount,
        "trx": trx,
        "user_post_balance": userPostBalance,
        "merchant_post_balance": merchantPostBalance,
        "user_details": userDetails,
        "merchant_details": merchantDetails,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "merchant": merchant?.toJson(),
      };
}

class MakePaymentSubmitInfo {
  String? userId;
  String? beforeCharge;
  String? amount;
  String? postBalance;
  String? charge;
  String? chargeType;
  String? trxType;
  String? remark;
  String? details;
  String? receiverId;
  String? receiverType;
  String? trx;
  String? updatedAt;
  String? createdAt;
  int? id;
  UserModel? receiverMerchant;

  MakePaymentSubmitInfo({
    this.userId,
    this.beforeCharge,
    this.amount,
    this.postBalance,
    this.charge,
    this.chargeType,
    this.trxType,
    this.remark,
    this.details,
    this.receiverId,
    this.receiverType,
    this.trx,
    this.updatedAt,
    this.createdAt,
    this.id,
    this.receiverMerchant,
  });

  factory MakePaymentSubmitInfo.fromJson(Map<String, dynamic> json) => MakePaymentSubmitInfo(
        userId: json["user_id"]?.toString(),
        beforeCharge: json["before_charge"]?.toString(),
        amount: json["amount"]?.toString(),
        postBalance: json["post_balance"]?.toString(),
        charge: json["charge"]?.toString(),
        chargeType: json["charge_type"]?.toString(),
        trxType: json["trx_type"]?.toString(),
        remark: json["remark"]?.toString(),
        details: json["details"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        receiverType: json["receiver_type"]?.toString(),
        trx: json["trx"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        id: json["id"],
        receiverMerchant: json["receiver_merchant"] == null ? null : UserModel.fromJson(json["receiver_merchant"]),
      );

  Map<String, dynamic> toJson() => {
        "user_id": userId,
        "before_charge": beforeCharge,
        "amount": amount,
        "post_balance": postBalance,
        "charge": charge,
        "charge_type": chargeType,
        "trx_type": trxType,
        "remark": remark,
        "details": details,
        "receiver_id": receiverId,
        "receiver_type": receiverType,
        "trx": trx,
        "updated_at": updatedAt,
        "created_at": createdAt,
        "id": id,
        "receiver_merchant": receiverMerchant?.toJson(),
      };
}
