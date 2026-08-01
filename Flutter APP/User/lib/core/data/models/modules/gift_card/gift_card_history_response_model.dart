// To parse this JSON data, do
//
//     final giftCardHistoryResponseModel = giftCardHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/user/user_model.dart';

import 'all_gift_card_response_model.dart';

GiftCardHistoryResponseModel giftCardHistoryResponseModelFromJson(String str) => GiftCardHistoryResponseModel.fromJson(json.decode(str));

class GiftCardHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  GiftCardHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory GiftCardHistoryResponseModel.fromJson(Map<String, dynamic> json) => GiftCardHistoryResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: json["message"] == null ? [] : List<String>.from(json["message"]!.map((x) => x)),
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );
}

class Data {
  GiftCardPurchases? giftCardPurchases;

  Data({
    this.giftCardPurchases,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        giftCardPurchases: json["gift_card_purchases"] == null ? null : GiftCardPurchases.fromJson(json["gift_card_purchases"]),
      );
}

class GiftCardPurchases {
  String? currentPage;
  List<GiftCardHistory>? data;
  String? firstPageUrl;
  String? from;
  String? lastPage;
  String? lastPageUrl;
  List<Link>? links;
  dynamic nextPageUrl;
  String? path;
  String? perPage;
  dynamic prevPageUrl;
  String? to;
  String? total;

  GiftCardPurchases({
    this.currentPage,
    this.data,
    this.firstPageUrl,
    this.from,
    this.lastPage,
    this.lastPageUrl,
    this.links,
    this.nextPageUrl,
    this.path,
    this.perPage,
    this.prevPageUrl,
    this.to,
    this.total,
  });

  factory GiftCardPurchases.fromJson(Map<String, dynamic> json) => GiftCardPurchases(
        currentPage: json["current_page"]?.toString(),
        data: json["data"] == null ? [] : List<GiftCardHistory>.from(json["data"]!.map((x) => GiftCardHistory.fromJson(x))),
        firstPageUrl: json["first_page_url"]?.toString(),
        from: json["from"]?.toString(),
        lastPage: json["last_page"]?.toString(),
        lastPageUrl: json["last_page_url"]?.toString(),
        links: json["links"] == null ? [] : List<Link>.from(json["links"]!.map((x) => Link.fromJson(x))),
        nextPageUrl: json["next_page_url"]?.toString(),
        path: json["path"]?.toString(),
        perPage: json["per_page"]?.toString(),
        prevPageUrl: json["prev_page_url"]?.toString(),
        to: json["to"]?.toString(),
        total: json["total"]?.toString(),
      );
}

class GiftCardHistory {
  int? id;
  int? userId;
  int? productId;
  String? amount;
  String? charge;
  String? discount;
  String? rate;
  String? unitPrice;
  String? subTotal;
  String? quantity;
  String? total;
  String? trx;
  String? recipientEmail;
  String? status;
  String? apiProviderId;
  String? apiProviderTrx;
  String? cronOrdering;
  DateTime? createdAt;
  String? updatedAt;
  GiftCard? giftCard;
  UserModel? user;

  GiftCardHistory({
    this.id,
    this.userId,
    this.productId,
    this.amount,
    this.charge,
    this.discount,
    this.rate,
    this.unitPrice,
    this.subTotal,
    this.quantity,
    this.total,
    this.trx,
    this.recipientEmail,
    this.status,
    this.apiProviderId,
    this.apiProviderTrx,
    this.cronOrdering,
    this.createdAt,
    this.updatedAt,
    this.giftCard,
    this.user,
  });

  factory GiftCardHistory.fromJson(Map<String, dynamic> json) => GiftCardHistory(
        id: json["id"],
        userId: json["user_id"],
        productId: json["product_id"],
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        discount: json["discount"]?.toString(),
        rate: json["rate"]?.toString(),
        unitPrice: json["unit_price"]?.toString(),
        subTotal: json["sub_total"]?.toString(),
        quantity: json["quantity"]?.toString(),
        total: json["total"]?.toString(),
        trx: json["trx"]?.toString(),
        recipientEmail: json["recipient_email"]?.toString(),
        status: json["status"]?.toString(),
        apiProviderId: json["api_provider_id"]?.toString(),
        apiProviderTrx: json["api_provider_trx"]?.toString(),
        cronOrdering: json["cron_ordering"]?.toString(),
        createdAt: json["created_at"] == null ? null : DateTime.parse(json["created_at"]),
        updatedAt: json["updated_at"]?.toString(),
        giftCard: json["gift_card"] == null ? null : GiftCard.fromJson(json["gift_card"]),
        user: json["user"] == null ? null : UserModel.fromJson(json["user"]),
      );
}
