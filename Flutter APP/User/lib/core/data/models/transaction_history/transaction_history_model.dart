// To parse this JSON data, do
//
//     final transactionHistoryResponseModel = transactionHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

TransactionHistoryResponseModel transactionHistoryResponseModelFromJson(
  String str,
) =>
    TransactionHistoryResponseModel.fromJson(json.decode(str));

String transactionHistoryResponseModelToJson(
  TransactionHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class TransactionHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  TransactionHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory TransactionHistoryResponseModel.fromJson(Map<String, dynamic> json) => TransactionHistoryResponseModel(
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
  Transactions? transactions;
  List<TransactionsRemark>? remarks;

  Data({this.transactions, this.remarks});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        transactions: json["transactions"] == null ? null : Transactions.fromJson(json["transactions"]),
        remarks: json["remarks"] == null
            ? []
            : List<TransactionsRemark>.from(
                json["remarks"]!.map((x) => TransactionsRemark.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "transactions": transactions?.toJson(),
        "remarks": remarks == null ? [] : List<dynamic>.from(remarks!.map((x) => x.toJson())),
      };
}

class TransactionsRemark {
  String? remark;

  TransactionsRemark({this.remark});

  factory TransactionsRemark.fromJson(Map<String, dynamic> json) => TransactionsRemark(remark: json["remark"]);

  Map<String, dynamic> toJson() => {"remark": remark};
}

class Transactions {
  List<TransactionHistoryModel>? historyData;

  String? nextPageUrl;
  String? path;

  Transactions({this.historyData, this.nextPageUrl, this.path});

  factory Transactions.fromJson(Map<String, dynamic> json) => Transactions(
        historyData: json["data"] == null
            ? []
            : List<TransactionHistoryModel>.from(
                json["data"]!.map((x) => TransactionHistoryModel.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"],
        path: json["path"],
      );

  Map<String, dynamic> toJson() => {
        "data": historyData == null ? [] : List<dynamic>.from(historyData!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
        "path": path,
      };
}

class TransactionHistoryModel {
  int? id;
  String? trx;
  String? trxType;
  String? amount;
  String? charge;
  String? totalAmount;
  String? remark;
  TrxOtherData? otherData;
  String? details;
  String? createdAt;
  String? createdAtDiff;

  TransactionHistoryModel({
    this.id,
    this.trx,
    this.trxType,
    this.amount,
    this.charge,
    this.totalAmount,
    this.remark,
    this.otherData,
    this.details,
    this.createdAt,
    this.createdAtDiff,
  });

  factory TransactionHistoryModel.fromJson(Map<String, dynamic> json) => TransactionHistoryModel(
        id: json["id"],
        trx: json["trx"]?.toString(),
        trxType: json["trx_type"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        totalAmount: json["total_amount"]?.toString(),
        remark: json["remark"]?.toString(),
        otherData: json["other_data"] == null ? null : TrxOtherData.fromJson(json["other_data"]),
        details: json["details"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        createdAtDiff: json["created_at_diff"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "trx": trx,
        "trx_type": trxType,
        "amount": amount,
        "charge": charge,
        "total_amount": totalAmount,
        "remark": remark,
        "other_data": otherData?.toJson(),
        "details": details,
        "created_at": createdAt,
        "created_at_diff": createdAtDiff,
      };
}

class TrxOtherData {
  String? title;
  String? subtitle;
  String? feedback;
  String? note;
  String? imageSrc;

  TrxOtherData({
    this.title,
    this.subtitle,
    this.feedback,
    this.note,
    this.imageSrc,
  });

  factory TrxOtherData.fromJson(Map<String, dynamic> json) => TrxOtherData(
        title: json["title"]?.toString(),
        subtitle: json["subtitle"]?.toString(),
        feedback: json["feedback"]?.toString(),
        note: json["note"]?.toString(),
        imageSrc: json["image_src"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "title": title,
        "subtitle": subtitle,
        "feedback": feedback,
        "note": note,
        "image_src": imageSrc,
      };
}
