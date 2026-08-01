// To parse this JSON data, do
//
//     final allGiftCardResponseModel = allGiftCardResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/country_model/country_model.dart';

AllGiftCardResponseModel allGiftCardResponseModelFromJson(String str) => AllGiftCardResponseModel.fromJson(json.decode(str));

class AllGiftCardResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  AllGiftCardResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory AllGiftCardResponseModel.fromJson(Map<String, dynamic> json) => AllGiftCardResponseModel(
        remark: json["remark"],
        status: json["status"],
        message: json["message"] == null ? [] : List<String>.from(json["message"]!.map((x) => x)),
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );
}

class Data {
  List<CountryData>? countries;
  List<GiftCategory>? categories;
  Products? products;
  List<String>? supportedOtpTypes;
  String? currentBalance;

  Data({
    this.countries,
    this.categories,
    this.products,
    this.supportedOtpTypes,
    this.currentBalance,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        countries: json["countries"] == null ? [] : List<CountryData>.from(json["countries"]!.map((x) => CountryData.fromJson(x))),
        categories: json["categories"] == null ? [] : List<GiftCategory>.from(json["categories"]!.map((x) => GiftCategory.fromJson(x))),
        products: json["products"] == null ? null : Products.fromJson(json["products"]),
        supportedOtpTypes: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString(),
      );
}

class GiftCategory {
  int? id;
  String? uniqueId;
  String? name;
  String? status;
  String? createdAt;
  String? updatedAt;

  GiftCategory({
    this.id,
    this.uniqueId,
    this.name,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory GiftCategory.fromJson(Map<String, dynamic> json) => GiftCategory(
        id: json["id"],
        uniqueId: json["unique_id"]?.toString(),
        name: json["name"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "unique_id": uniqueId,
        "name": name,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class Products {
  int? currentPage;
  List<GiftCard>? data;
  String? firstPageUrl;
  int? from;
  int? lastPage;
  String? lastPageUrl;
  List<Link>? links;
  dynamic nextPageUrl;
  String? path;
  int? perPage;
  dynamic prevPageUrl;
  int? to;
  int? total;

  Products({
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

  factory Products.fromJson(Map<String, dynamic> json) => Products(
        currentPage: json["current_page"],
        data: json["data"] == null ? [] : List<GiftCard>.from(json["data"]!.map((x) => GiftCard.fromJson(x))),
        firstPageUrl: json["first_page_url"],
        from: json["from"],
        lastPage: json["last_page"],
        lastPageUrl: json["last_page_url"],
        links: json["links"] == null ? [] : List<Link>.from(json["links"]!.map((x) => Link.fromJson(x))),
        nextPageUrl: json["next_page_url"],
        path: json["path"],
        perPage: json["per_page"],
        prevPageUrl: json["prev_page_url"],
        to: json["to"],
        total: json["total"],
      );
}

class GiftCard {
  int? id;
  String? productId;
  String? countryId;
  String? productName;
  String? isGlobal;
  String? status;
  String? supportsPreOrder;
  String? senderFee;
  String? senderFeePercentage;
  String? discountPercentage;
  String? denominationType;
  String? recipientCurrencyCode;
  String? minRecipientDenomination;
  String? maxRecipientDenomination;
  String? senderCurrencyCode;
  String? minSenderDenomination;
  String? maxSenderDenomination;
  List<String>? fixedRecipientDenominations;
  List<double>? fixedSenderDenominations;
  Map<String, double>? fixedRecipientToSenderMap;
  List<String>? logoUrls;
  String? brandId;
  String? brandName;
  String? categoryId;
  String? userIdRequired;
  String? recipientToSenderExchangeRate;
  DateTime? createdAt;
  DateTime? updatedAt;

  GiftCard({
    this.id,
    this.productId,
    this.countryId,
    this.productName,
    this.isGlobal,
    this.status,
    this.supportsPreOrder,
    this.senderFee,
    this.senderFeePercentage,
    this.discountPercentage,
    this.denominationType,
    this.recipientCurrencyCode,
    this.minRecipientDenomination,
    this.maxRecipientDenomination,
    this.senderCurrencyCode,
    this.minSenderDenomination,
    this.maxSenderDenomination,
    this.fixedRecipientDenominations,
    this.fixedSenderDenominations,
    this.fixedRecipientToSenderMap,
    this.logoUrls,
    this.brandId,
    this.brandName,
    this.categoryId,
    this.userIdRequired,
    this.recipientToSenderExchangeRate,
    this.createdAt,
    this.updatedAt,
  });

  factory GiftCard.fromJson(Map<String, dynamic> json) => GiftCard(
        id: json["id"],
        productId: json["product_id"]?.toString(),
        countryId: json["country_id"]?.toString(),
        productName: json["product_name"]?.toString(),
        isGlobal: json["is_global"]?.toString(),
        status: json["status"]?.toString(),
        supportsPreOrder: json["supports_pre_order"]?.toString(),
        senderFee: json["sender_fee"]?.toString(),
        senderFeePercentage: json["sender_fee_percentage"],
        discountPercentage: json["discount_percentage"],
        denominationType: json["denomination_type"]?.toString(),
        recipientCurrencyCode: json["recipient_currency_code"]?.toString(),
        minRecipientDenomination: json["min_recipient_denomination"],
        maxRecipientDenomination: json["max_recipient_denomination"],
        senderCurrencyCode: json["sender_currency_code"]?.toString(),
        minSenderDenomination: json["min_sender_denomination"]?.toString(),
        maxSenderDenomination: json["max_sender_denomination"]?.toString(),
        fixedRecipientDenominations: json["fixed_recipient_denominations"] == null ? [] : List<String>.from(json["fixed_recipient_denominations"]!.map((x) => x?.toString())),
        fixedSenderDenominations: json["fixed_sender_denominations"] == null ? [] : List<double>.from(json["fixed_sender_denominations"]!.map((x) => x?.toDouble())),
        fixedRecipientToSenderMap: (json["fixed_recipient_to_sender_map"] != null) ? Map.from(json["fixed_recipient_to_sender_map"]).map((k, v) => MapEntry<String, double>(k, (v as num).toDouble())) : {},
        logoUrls: json["logo_urls"] == null ? [] : List<String>.from(json["logo_urls"]!.map((x) => x)),
        brandId: json["brand_id"]?.toString(),
        brandName: json["brand_name"]?.toString(),
        categoryId: json["category_id"]?.toString(),
        userIdRequired: json["user_id_required"]?.toString(),
        recipientToSenderExchangeRate: json["recipient_to_sender_exchange_rate"]?.toString(),
        createdAt: json["created_at"] == null ? null : DateTime.parse(json["created_at"]),
        updatedAt: json["updated_at"] == null ? null : DateTime.parse(json["updated_at"]),
      );
}

class Link {
  String? url;
  String? label;
  bool? active;

  Link({
    this.url,
    this.label,
    this.active,
  });

  factory Link.fromJson(Map<String, dynamic> json) => Link(
        url: json["url"],
        label: json["label"],
        active: json["active"],
      );

  Map<String, dynamic> toJson() => {
        "url": url,
        "label": label,
        "active": active,
      };
}

class EnumValues<T> {
  Map<String, T> map;
  late Map<T, String> reverseMap;

  EnumValues(this.map);

  Map<T, String> get reverse {
    reverseMap = map.map((k, v) => MapEntry(v, k));
    return reverseMap;
  }
}
