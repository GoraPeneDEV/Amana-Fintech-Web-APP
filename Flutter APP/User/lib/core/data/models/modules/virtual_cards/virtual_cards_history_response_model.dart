// To parse this JSON data, do
//
//     final virtualCardHistoryResponseModel = virtualCardHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

VirtualCardHistoryResponseModel virtualCardHistoryResponseModelFromJson(
  String str,
) =>
    VirtualCardHistoryResponseModel.fromJson(json.decode(str));

String virtualCardHistoryResponseModelToJson(
  VirtualCardHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class VirtualCardHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  VirtualCardHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory VirtualCardHistoryResponseModel.fromJson(Map<String, dynamic> json) => VirtualCardHistoryResponseModel(
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

  Data({this.transactions});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        transactions: json["transactions"] == null ? null : Transactions.fromJson(json["transactions"]),
      );

  Map<String, dynamic> toJson() => {"transactions": transactions?.toJson()};
}

class Transactions {
  int? currentPage;
  List<VirtualCardTransactionModel>? data;

  String? nextPageUrl;

  Transactions({this.currentPage, this.data, this.nextPageUrl});

  factory Transactions.fromJson(Map<String, dynamic> json) => Transactions(
        currentPage: json["current_page"],
        data: json["data"] == null
            ? []
            : List<VirtualCardTransactionModel>.from(
                json["data"]!.map((x) => VirtualCardTransactionModel.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"],
      );

  Map<String, dynamic> toJson() => {
        "current_page": currentPage,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
      };
}

class VirtualCardTransactionModel {
  int? id;
  String? userId;
  String? agentId;
  String? merchantId;
  String? amount;
  String? charge;
  String? postBalance;
  String? trxType;
  String? trx;
  String? details;
  String? remark;
  String? virtualCardId;
  String? createdAt;
  String? updatedAt;
  String? totalAmount;
  VirtualCard? virtualCard;

  VirtualCardTransactionModel({
    this.id,
    this.userId,
    this.agentId,
    this.merchantId,
    this.amount,
    this.charge,
    this.postBalance,
    this.trxType,
    this.trx,
    this.details,
    this.remark,
    this.virtualCardId,
    this.createdAt,
    this.updatedAt,
    this.totalAmount,
    this.virtualCard,
  });

  factory VirtualCardTransactionModel.fromJson(Map<String, dynamic> json) => VirtualCardTransactionModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        agentId: json["agent_id"]?.toString(),
        merchantId: json["merchant_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        postBalance: json["post_balance"]?.toString(),
        trxType: json["trx_type"]?.toString(),
        trx: json["trx"]?.toString(),
        details: json["details"]?.toString(),
        remark: json["remark"]?.toString(),
        virtualCardId: json["virtual_card_id"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        totalAmount: json["total_amount"]?.toString(),
        virtualCard: json["virtual_card"] == null ? null : VirtualCard.fromJson(json["virtual_card"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "agent_id": agentId,
        "merchant_id": merchantId,
        "amount": amount,
        "charge": charge,
        "post_balance": postBalance,
        "trx_type": trxType,
        "trx": trx,
        "details": details,
        "remark": remark,
        "virtual_card_id": virtualCardId,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "total_amount": totalAmount,
        "virtual_card": virtualCard?.toJson(),
      };
}

class VirtualCard {
  int? id;
  String? userId;
  String? last4;
  String? expMonth;
  String? expYear;
  String? balance;
  String? brand;
  String? spendingLimit;
  String? currentSpend;
  String? cardholderId;
  String? cardId;
  String? cardType;
  String? status;
  String? createdAt;
  String? updatedAt;

  VirtualCard({
    this.id,
    this.userId,
    this.last4,
    this.expMonth,
    this.expYear,
    this.balance,
    this.brand,
    this.spendingLimit,
    this.currentSpend,
    this.cardholderId,
    this.cardId,
    this.cardType,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory VirtualCard.fromJson(Map<String, dynamic> json) => VirtualCard(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        last4: json["last4"]?.toString(),
        expMonth: json["exp_month"]?.toString(),
        expYear: json["exp_year"]?.toString(),
        balance: json["balance"]?.toString(),
        brand: json["brand"]?.toString(),
        spendingLimit: json["spending_limit"]?.toString(),
        currentSpend: json["current_spend"]?.toString(),
        cardholderId: json["cardholder_id"]?.toString(),
        cardId: json["card_id"]?.toString(),
        cardType: json["card_type"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "last4": last4,
        "exp_month": expMonth,
        "exp_year": expYear,
        "balance": balance,
        "brand": brand,
        "spending_limit": spendingLimit,
        "current_spend": currentSpend,
        "cardholder_id": cardholderId,
        "card_id": cardId,
        "card_type": cardType,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
