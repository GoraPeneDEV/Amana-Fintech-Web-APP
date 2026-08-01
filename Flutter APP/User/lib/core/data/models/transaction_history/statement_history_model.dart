//     final statementHistoryResponseModel = statementHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

StatementHistoryResponseModel statementHistoryResponseModelFromJson(
  String str,
) =>
    StatementHistoryResponseModel.fromJson(json.decode(str));

String statementHistoryResponseModelToJson(
  StatementHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class StatementHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  StatementsData? data;

  StatementHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory StatementHistoryResponseModel.fromJson(Map<String, dynamic> json) => StatementHistoryResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: json["message"] == null ? [] : List<String>.from(json["message"]!.map((x) => x)),
        data: json["data"] == null ? null : StatementsData.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "remark": remark,
        "status": status,
        "message": message == null ? [] : List<dynamic>.from(message!.map((x) => x)),
        "data": data?.toJson(),
      };
}

class StatementsData {
  String? startingBalance;
  String? totalTransactionAmount;
  String? totalTransactionCount;
  String? totalCommission;
  String? currentBalance;

  StatementsData({
    this.startingBalance,
    this.totalTransactionAmount,
    this.totalTransactionCount,
    this.totalCommission,
    this.currentBalance,
  });

  factory StatementsData.fromJson(Map<String, dynamic> json) => StatementsData(
        startingBalance: json["starting_balance"]?.toString(),
        totalTransactionAmount: json["total_transaction_amount"]?.toString(),
        totalTransactionCount: json["total_transaction_count"]?.toString(),
        totalCommission: json["total_commission"]?.toString(),
        currentBalance: json["current_balance"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "starting_balance": startingBalance,
        "total_transaction_amount": totalTransactionAmount,
        "total_transaction_count": totalTransactionCount,
        "total_commission": totalCommission,
      };
}
