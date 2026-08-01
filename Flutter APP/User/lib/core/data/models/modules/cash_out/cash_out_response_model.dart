// To parse this JSON data, do
//
//     final cashOutResponseModel = cashOutResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';

CashOutResponseModel cashOutResponseModelFromJson(String str) => CashOutResponseModel.fromJson(json.decode(str));

String cashOutResponseModelToJson(CashOutResponseModel data) => json.encode(data.toJson());

class CashOutResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  CashOutResponseModel({this.remark, this.status, this.message, this.data});

  factory CashOutResponseModel.fromJson(Map<String, dynamic> json) => CashOutResponseModel(
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
  GlobalChargeModel? cashOutCharge;
  List<LatestCashOutHistory>? latestCashOutHistory;
  CashOutSubmitInfo? cashOut;
  Data({
    this.otpType,
    this.currentBalance,
    this.cashOutCharge,
    this.latestCashOutHistory,
    this.cashOut,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        cashOutCharge: json["cash_out_charge"] == null ? null : GlobalChargeModel.fromJson(json["cash_out_charge"]),
        latestCashOutHistory: json["latest_cash_outs"] == null
            ? []
            : List<LatestCashOutHistory>.from(
                json["latest_cash_outs"]!.map(
                  (x) => LatestCashOutHistory.fromJson(x),
                ),
              ),
        cashOut: json["cash_out"] == null ? null : CashOutSubmitInfo.fromJson(json["cash_out"]),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "cash_out": cashOutCharge?.toJson(),
        "latest_cash_outs": latestCashOutHistory == null ? [] : List<dynamic>.from(latestCashOutHistory!.map((x) => x.toJson())),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class LatestCashOutHistory {
  int? id;
  String? userId;
  String? agentId;
  String? amount;
  String? charge;
  String? totalAmount;
  String? commission;
  String? userPostBalance;
  String? agentPostBalance;
  String? trx;
  String? userDetails;
  String? agentDetails;
  String? createdAt;
  String? updatedAt;
  UserModel? receiverAgent;

  LatestCashOutHistory({
    this.id,
    this.userId,
    this.agentId,
    this.amount,
    this.charge,
    this.totalAmount,
    this.commission,
    this.userPostBalance,
    this.agentPostBalance,
    this.trx,
    this.userDetails,
    this.agentDetails,
    this.createdAt,
    this.updatedAt,
    this.receiverAgent,
  });

  factory LatestCashOutHistory.fromJson(Map<String, dynamic> json) => LatestCashOutHistory(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        agentId: json["agent_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        totalAmount: json["total_amount"]?.toString(),
        commission: json["commission"]?.toString(),
        userPostBalance: json["user_post_balance"]?.toString(),
        agentPostBalance: json["agent_post_balance"]?.toString(),
        trx: json["trx"]?.toString(),
        userDetails: json["user_details"]?.toString(),
        agentDetails: json["agent_details"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        receiverAgent: json["receiver_agent"] == null ? null : UserModel.fromJson(json["receiver_agent"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "agent_id": agentId,
        "amount": amount,
        "charge": charge,
        "total_amount": totalAmount,
        "commission": commission,
        "user_post_balance": userPostBalance,
        "agent_post_balance": agentPostBalance,
        "trx": trx,
        "user_details": userDetails,
        "agent_details": agentDetails,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "receiver_agent": receiverAgent?.toJson(),
      };
}

class CashOutSubmitInfo {
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

  CashOutSubmitInfo({
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

  factory CashOutSubmitInfo.fromJson(Map<String, dynamic> json) => CashOutSubmitInfo(
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
