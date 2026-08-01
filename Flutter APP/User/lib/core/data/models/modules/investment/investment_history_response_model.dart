// To parse this JSON data, do
//
//     final investmentHistoryResponseModel = investmentHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/investment/investment_plan_response_model.dart';

InvestmentHistoryResponseModel investmentHistoryResponseModelFromJson(String str) => InvestmentHistoryResponseModel.fromJson(json.decode(str));

String investmentHistoryResponseModelToJson(InvestmentHistoryResponseModel data) => json.encode(data.toJson());

class InvestmentHistoryResponseModel {
  final String? remark;
  final String? status;
  final List<String>? message;
  final Data? data;

  InvestmentHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory InvestmentHistoryResponseModel.fromJson(Map<String, dynamic> json) => InvestmentHistoryResponseModel(
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
  final Investments? investments;

  Data({
    this.investments,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        investments: json["investments"] == null ? null : Investments.fromJson(json["investments"]),
      );

  Map<String, dynamic> toJson() => {
        "investments": investments?.toJson(),
      };
}

class Investments {
  final String? currentPage;
  final List<InvestmentData>? data;
  final String? firstPageUrl;
  final String? from;
  final String? lastPage;
  final String? lastPageUrl;
  final String? nextPageUrl;
  final String? path;
  final String? perPage;
  final String? prevPageUrl;
  final String? to;
  final String? total;

  Investments({
    this.currentPage,
    this.data,
    this.firstPageUrl,
    this.from,
    this.lastPage,
    this.lastPageUrl,
    this.nextPageUrl,
    this.path,
    this.perPage,
    this.prevPageUrl,
    this.to,
    this.total,
  });

  factory Investments.fromJson(Map<String, dynamic> json) => Investments(
        currentPage: json["current_page"].toString(),
        data: json["data"] == null ? [] : List<InvestmentData>.from(json["data"]!.map((x) => InvestmentData.fromJson(x))),
        firstPageUrl: json["first_page_url"].toString(),
        from: json["from"].toString(),
        lastPage: json["last_page"].toString(),
        lastPageUrl: json["last_page_url"].toString(),
        nextPageUrl: json["next_page_url"].toString(),
        path: json["path"].toString(),
        perPage: json["per_page"].toString(),
        prevPageUrl: json["prev_page_url"].toString(),
        to: json["to"].toString(),
        total: json["total"].toString(),
      );

  Map<String, dynamic> toJson() => {
        "current_page": currentPage,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "first_page_url": firstPageUrl,
        "from": from,
        "last_page": lastPage,
        "last_page_url": lastPageUrl,
        "next_page_url": nextPageUrl,
        "path": path,
        "per_page": perPage,
        "prev_page_url": prevPageUrl,
        "to": to,
        "total": total,
      };
}

class InvestmentData {
  int? id;
  String? userId;
  String? planId;
  String? perInterestAmount;
  String? investAmount;
  String? totalInterestAmount;
  String? totalInterestAmountGet;
  String? trx;
  String? status;
  String? lastReturnAt;
  String? nextReturnAt;
  String? capitalBack;
  String? totalRepeat;
  String? totalRepeatGet;
  String? createdAt;
  String? updatedAt;
  PlanData? plan;

  InvestmentData({
    this.id,
    this.userId,
    this.planId,
    this.perInterestAmount,
    this.investAmount,
    this.totalInterestAmount,
    this.totalInterestAmountGet,
    this.trx,
    this.status,
    this.lastReturnAt,
    this.nextReturnAt,
    this.capitalBack,
    this.totalRepeat,
    this.totalRepeatGet,
    this.createdAt,
    this.updatedAt,
    this.plan,
  });

  factory InvestmentData.fromJson(Map<String, dynamic> json) => InvestmentData(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        planId: json["plan_id"]?.toString(),
        perInterestAmount: json["per_interest_amount"]?.toString(),
        investAmount: json["invest_amount"]?.toString(),
        totalInterestAmount: json["total_interest_amount"]?.toString(),
        totalInterestAmountGet: json["total_interest_amount_get"]?.toString(),
        trx: json["trx"]?.toString(),
        status: json["status"]?.toString(),
        lastReturnAt: json["last_return_at"]?.toString(),
        nextReturnAt: json["next_return_at"]?.toString(),
        capitalBack: json["capital_back"]?.toString(),
        totalRepeat: json["total_repeat"]?.toString(),
        totalRepeatGet: json["total_repeat_get"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        plan: json["plan"] == null ? null : PlanData.fromJson(json["plan"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "plan_id": planId,
        "per_interest_amount": perInterestAmount,
        "invest_amount": investAmount,
        "total_interest_amount": totalInterestAmount,
        "total_interest_amount_get": totalInterestAmountGet,
        "trx": trx,
        "status": status,
        "last_return_at": lastReturnAt,
        "next_return_at": nextReturnAt,
        "capital_back": capitalBack,
        "total_repeat": totalRepeat,
        "total_repeat_get": totalRepeatGet,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "plan": plan?.toJson(),
      };
}
