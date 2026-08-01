// To parse this JSON data, do
//
//     final sendMoneyResponseModel = sendMoneyResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/helper/string_format_helper.dart';

SendMoneyResponseModel sendMoneyResponseModelFromJson(String str) => SendMoneyResponseModel.fromJson(json.decode(str));

String sendMoneyResponseModelToJson(SendMoneyResponseModel data) => json.encode(data.toJson());

class SendMoneyResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  SendMoneyResponseModel({this.remark, this.status, this.message, this.data});

  factory SendMoneyResponseModel.fromJson(Map<String, dynamic> json) => SendMoneyResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: json['message'] != null ? (json['message'] as List<dynamic>).toStringList() : [],
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
  GlobalChargeModel? sendMoneyCharge;
  List<LatestSendMoneyHistory>? latestSendMoneyHistory;
  SendMoneySubmitInfoModel? sendMoney;
  Data({
    this.otpType,
    this.currentBalance,
    this.sendMoneyCharge,
    this.latestSendMoneyHistory,
    this.sendMoney,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        sendMoneyCharge: json["send_money_charge"] == null ? null : GlobalChargeModel.fromJson(json["send_money_charge"]),
        latestSendMoneyHistory: json["latest_send_money"] == null
            ? []
            : List<LatestSendMoneyHistory>.from(
                json["latest_send_money"]!.map(
                  (x) => LatestSendMoneyHistory.fromJson(x),
                ),
              ),
        sendMoney: json["send_money"] == null ? null : SendMoneySubmitInfoModel.fromJson(json["send_money"]),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "send_money_charge": sendMoneyCharge?.toJson(),
        "latest_send_money": latestSendMoneyHistory == null
            ? []
            : List<dynamic>.from(
                latestSendMoneyHistory!.map((x) => x.toJson()),
              ),
        "send_money": sendMoney?.toJson(),
      };

  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class SendMoneySubmitInfoModel {
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
  UserModel? receiverUser;

  SendMoneySubmitInfoModel({
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
    this.receiverUser,
  });

  factory SendMoneySubmitInfoModel.fromJson(Map<String, dynamic> json) => SendMoneySubmitInfoModel(
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
        receiverUser: json["receiver_user"] == null ? null : UserModel.fromJson(json["receiver_user"]),
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
        "receiver_user": receiverUser?.toJson(),
      };
}

class LatestSendMoneyHistory {
  int? id;
  String? senderId;
  String? receiverId;
  String? amount;
  String? charge;
  String? totalAmount;
  String? senderPostBalance;
  String? receiverPostBalance;
  String? trx;
  String? senderDetails;
  String? receiverDetails;
  String? createdAt;
  String? updatedAt;
  UserModel? receiverUser;

  LatestSendMoneyHistory({
    this.id,
    this.senderId,
    this.receiverId,
    this.amount,
    this.charge,
    this.totalAmount,
    this.senderPostBalance,
    this.receiverPostBalance,
    this.trx,
    this.senderDetails,
    this.receiverDetails,
    this.createdAt,
    this.updatedAt,
    this.receiverUser,
  });

  factory LatestSendMoneyHistory.fromJson(Map<String, dynamic> json) => LatestSendMoneyHistory(
        id: json["id"],
        senderId: json["sender_id"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        totalAmount: json["total_amount"]?.toString(),
        senderPostBalance: json["sender_post_balance"]?.toString(),
        receiverPostBalance: json["receiver_post_balance"]?.toString(),
        trx: json["trx"]?.toString(),
        senderDetails: json["sender_details"]?.toString(),
        receiverDetails: json["receiver_details"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        receiverUser: json["receiver_user"] == null ? null : UserModel.fromJson(json["receiver_user"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "sender_id": senderId,
        "receiver_id": receiverId,
        "amount": amount,
        "charge": charge,
        "total_amount": totalAmount,
        "sender_post_balance": senderPostBalance,
        "receiver_post_balance": receiverPostBalance,
        "trx": trx,
        "sender_details": senderDetails,
        "receiver_details": receiverDetails,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "receiver_user": receiverUser?.toJson(),
      };
}
