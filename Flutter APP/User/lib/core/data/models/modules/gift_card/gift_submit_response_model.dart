// To parse this JSON data, do
//
//     final giftSubmitResponseModel = giftSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/gift_card/gift_card_history_response_model.dart';

import '../global/module_transaction_model.dart';

GiftSubmitResponseModel giftSubmitResponseModelFromJson(String str) => GiftSubmitResponseModel.fromJson(json.decode(str));

class GiftSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  GiftSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory GiftSubmitResponseModel.fromJson(Map<String, dynamic> json) => GiftSubmitResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: json["message"] == null ? [] : List<String>.from(json["message"]!.map((x) => x)),
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );
}

class Data {
  String? redirectType;
  String? redirectUrl;
  GiftCardPurchases? giftCardPurchase;
  ModuleGlobalSubmitTransactionModel? transaction;

  Data({
    this.redirectType,
    this.redirectUrl,
    this.giftCardPurchase,
    this.transaction,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        redirectType: json["redirect_type"],
        redirectUrl: json["redirect_url"],
        giftCardPurchase: json["gift_card_purchase"] == null ? null : GiftCardPurchases.fromJson(json["gift_card_purchase"]),
        transaction: json["transaction"] == null ? null : ModuleGlobalSubmitTransactionModel.fromJson(json["transaction"]),
      );
}
