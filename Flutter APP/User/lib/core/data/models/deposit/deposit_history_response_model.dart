// To parse this JSON data, do
//
//     final depositHistoryResponseModel = depositHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/utils/url_container.dart';

DepositHistoryResponseModel depositHistoryResponseModelFromJson(String str) => DepositHistoryResponseModel.fromJson(json.decode(str));

String depositHistoryResponseModelToJson(DepositHistoryResponseModel data) => json.encode(data.toJson());

class DepositHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  DepositHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory DepositHistoryResponseModel.fromJson(Map<String, dynamic> json) => DepositHistoryResponseModel(
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
  Histories? histories;

  Data({this.histories});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        histories: json["histories"] == null ? null : Histories.fromJson(json["histories"]),
      );

  Map<String, dynamic> toJson() => {"histories": histories?.toJson()};
}

class Histories {
  List<DepositHistoryListModel>? data;

  String? nextPageUrl;
  String? path;

  Histories({this.data, this.nextPageUrl, this.path});

  factory Histories.fromJson(Map<String, dynamic> json) => Histories(
        data: json["data"] == null
            ? []
            : List<DepositHistoryListModel>.from(
                json["data"]!.map((x) => DepositHistoryListModel.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"],
        path: json["path"],
      );

  Map<String, dynamic> toJson() => {
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
        "path": path,
      };
}

class DepositHistoryListModel {
  String? id;
  String? userId;
  String? agentId;
  String? merchantId;
  String? methodCode;
  String? amount;
  String? methodCurrency;
  String? charge;
  String? rate;
  String? finalAmount;
  String? btcAmount;
  String? btcWallet;
  String? trx;
  String? paymentTry;
  String? status;
  String? fromApi;
  String? adminFeedback;
  String? successUrl;
  String? failedUrl;
  String? lastCron;
  String? createdAt;
  String? updatedAt;
  Gateway? gateway;

  DepositHistoryListModel({
    this.id,
    this.userId,
    this.agentId,
    this.merchantId,
    this.methodCode,
    this.amount,
    this.methodCurrency,
    this.charge,
    this.rate,
    this.finalAmount,
    this.btcAmount,
    this.btcWallet,
    this.trx,
    this.paymentTry,
    this.status,
    this.fromApi,
    this.adminFeedback,
    this.successUrl,
    this.failedUrl,
    this.lastCron,
    this.createdAt,
    this.updatedAt,
    this.gateway,
  });

  factory DepositHistoryListModel.fromJson(Map<String, dynamic> json) => DepositHistoryListModel(
        id: json["id"]?.toString(),
        userId: json["user_id"]?.toString(),
        agentId: json["agent_id"]?.toString(),
        merchantId: json["merchant_id"]?.toString(),
        methodCode: json["method_code"]?.toString(),
        amount: json["amount"]?.toString(),
        methodCurrency: json["method_currency"]?.toString(),
        charge: json["charge"]?.toString(),
        rate: json["rate"]?.toString(),
        finalAmount: json["final_amount"]?.toString(),
        btcAmount: json["btc_amount"]?.toString(),
        btcWallet: json["btc_wallet"]?.toString(),
        trx: json["trx"]?.toString(),
        paymentTry: json["payment_try"]?.toString(),
        status: json["status"]?.toString(),
        fromApi: json["from_api"]?.toString(),
        adminFeedback: json["admin_feedback"]?.toString(),
        successUrl: json["success_url"]?.toString(),
        failedUrl: json["failed_url"]?.toString(),
        lastCron: json["last_cron"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        gateway: json["gateway"] == null ? null : Gateway.fromJson(json["gateway"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "agent_id": agentId,
        "merchant_id": merchantId,
        "method_code": methodCode,
        "amount": amount,
        "method_currency": methodCurrency,
        "charge": charge,
        "rate": rate,
        "final_amount": finalAmount,
        "btc_amount": btcAmount,
        "btc_wallet": btcWallet,
        "trx": trx,
        "payment_try": paymentTry,
        "status": status,
        "from_api": fromApi,
        "admin_feedback": adminFeedback,
        "success_url": successUrl,
        "failed_url": failedUrl,
        "last_cron": lastCron,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "gateway": gateway?.toJson(),
      };
}

class Gateway {
  String? id;
  String? formId;
  String? code;
  String? name;
  String? alias;
  String? image;
  String? status;
  SupportedCurrencies? supportedCurrencies;
  String? crypto;
  String? description;
  String? createdAt;
  String? updatedAt;

  Gateway({
    this.id,
    this.formId,
    this.code,
    this.name,
    this.alias,
    this.image,
    this.status,
    this.supportedCurrencies,
    this.crypto,
    this.description,
    this.createdAt,
    this.updatedAt,
  });

  factory Gateway.fromJson(Map<String, dynamic> json) => Gateway(
        id: json["id"]?.toString(),
        formId: json["form_id"]?.toString(),
        code: json["code"]?.toString(),
        name: json["name"]?.toString(),
        alias: json["alias"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        supportedCurrencies: json["supported_currencies"] == null
            ? null
            : json["supported_currencies"].toString() == "[]"
                ? null
                : SupportedCurrencies.fromJson(json["supported_currencies"]),
        crypto: json["crypto"]?.toString(),
        description: json["description"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "form_id": formId,
        "code": code,
        "name": name,
        "alias": alias,
        "image": image,
        "status": status,
        "supported_currencies": supportedCurrencies?.toJson(),
        "crypto": crypto,
        "description": description,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };

  String? getImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/gateway/$image';
      return imageUrl;
    }
  }
}

class SupportedCurrencies {
  final Map<String, String> currencies;

  SupportedCurrencies({required this.currencies});

  // Factory method to create an instance from a JSON map
  factory SupportedCurrencies.fromJson(Map<String, dynamic> json) {
    return SupportedCurrencies(currencies: Map<String, String>.from(json));
  }

  // Method to convert the object back to a JSON map
  Map<String, dynamic> toJson() {
    return currencies;
  }
}
