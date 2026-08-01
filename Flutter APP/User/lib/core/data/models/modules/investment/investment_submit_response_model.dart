// To parse this JSON data, do
//
//     final investmentSubmitResponseModel = investmentSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/user/user_model.dart';

InvestmentSubmitResponseModel investmentSubmitResponseModelFromJson(String str) => InvestmentSubmitResponseModel.fromJson(json.decode(str));

String investmentSubmitResponseModelToJson(InvestmentSubmitResponseModel data) => json.encode(data.toJson());

class InvestmentSubmitResponseModel {
  final String? remark;
  final String? status;
  final List<String>? message;
  final Data? data;

  InvestmentSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory InvestmentSubmitResponseModel.fromJson(Map<String, dynamic> json) => InvestmentSubmitResponseModel(
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
  final String? redirectType;
  final String? redirectUrl;
  final InvestmentModel? investment;

  Data({
    this.redirectType,
    this.redirectUrl,
    this.investment,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        redirectType: json["redirect_type"],
        redirectUrl: json["redirect_url"],
        investment: json["investment"] == null ? null : InvestmentModel.fromJson(json["investment"]),
      );

  Map<String, dynamic> toJson() => {
        "redirect_type": redirectType,
        "redirect_url": redirectUrl,
        "investment": investment?.toJson(),
      };
}

class InvestmentModel {
  final int? userId;
  final String? planId;
  final String? investAmount;
  final String? amountPerInterest;
  final String? totalInterestAmount;
  final String? totalAmount;
  final String? totalPercentAmount;
  final String? trx;
  final String? status;
  final String? nextReturnAt;
  final String? updatedAt;
  final String? createdAt;
  final String? id;
  final UserModel? user;

  InvestmentModel({
    this.userId,
    this.planId,
    this.investAmount,
    this.amountPerInterest,
    this.totalInterestAmount,
    this.totalAmount,
    this.totalPercentAmount,
    this.trx,
    this.status,
    this.nextReturnAt,
    this.updatedAt,
    this.createdAt,
    this.id,
    this.user,
  });

  factory InvestmentModel.fromJson(Map<String, dynamic> json) => InvestmentModel(
        userId: json["user_id"],
        planId: json["plan_id"]?.toString(),
        investAmount: json["invest_amount"]?.toString(),
        amountPerInterest: json["amount_per_interest"]?.toString(),
        totalInterestAmount: json["total_interest_amount"]?.toString(),
        totalAmount: json["total_amount"]?.toString(),
        totalPercentAmount: json["total_percent_amount"]?.toString(),
        trx: json["trx"]?.toString(),
        status: json["status"]?.toString(),
        nextReturnAt: json["next_return_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        id: json["id"]?.toString(),
        user: json["user"] == null ? null : UserModel.fromJson(json["user"]),
      );

  Map<String, dynamic> toJson() => {
        "user_id": userId,
        "plan_id": planId,
        "invest_amount": investAmount,
        "amount_per_interest": amountPerInterest,
        "total_interest_amount": totalInterestAmount,
        "total_amount": totalAmount,
        "total_percent_amount": totalPercentAmount,
        "trx": trx,
        "status": status,
        "next_return_at": nextReturnAt,
        "updated_at": updatedAt,
        "created_at": createdAt,
        "id": id,
        "user": user?.toJson(),
      };
}
