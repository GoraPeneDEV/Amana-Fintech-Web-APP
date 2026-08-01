// To parse this JSON data, do
//
//     final offersResponseModel = offersResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/utils/url_container.dart';

OffersResponseModel offersResponseModelFromJson(String str) => OffersResponseModel.fromJson(json.decode(str));

String offersResponseModelToJson(OffersResponseModel data) => json.encode(data.toJson());

class OffersResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  OffersResponseModel({this.remark, this.status, this.message, this.data});

  factory OffersResponseModel.fromJson(Map<String, dynamic> json) => OffersResponseModel(
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
  Offers? offers;

  Data({this.offers});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        offers: json["offers"] == null ? null : Offers.fromJson(json["offers"]),
      );

  Map<String, dynamic> toJson() => {"offers": offers?.toJson()};
}

class Offers {
  List<OfferModel>? data;

  String? nextPageUrl;
  String? path;

  Offers({this.data, this.nextPageUrl, this.path});

  factory Offers.fromJson(Map<String, dynamic> json) => Offers(
        data: json["data"] == null
            ? []
            : List<OfferModel>.from(
                json["data"]!.map((x) => OfferModel.fromJson(x)),
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

class OfferModel {
  int? id;
  String? merchantId;
  String? name;
  String? discountType;
  String? amount;
  String? minPayment;
  String? maximumDiscount;
  String? startDate;
  String? endDate;
  String? description;
  String? link;
  String? image;
  String? status;
  String? createdAt;
  String? updatedAt;
  UserModel? merchant;

  OfferModel({
    this.id,
    this.merchantId,
    this.name,
    this.discountType,
    this.amount,
    this.minPayment,
    this.maximumDiscount,
    this.startDate,
    this.endDate,
    this.description,
    this.link,
    this.image,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.merchant,
  });

  factory OfferModel.fromJson(Map<String, dynamic> json) => OfferModel(
        id: json["id"],
        merchantId: json["merchant_id"]?.toString(),
        name: json["name"]?.toString(),
        discountType: json["discount_type"]?.toString(),
        amount: json["amount"]?.toString(),
        minPayment: json["min_payment"]?.toString(),
        maximumDiscount: json["maximum_discount"]?.toString(),
        startDate: json["start_date"]?.toString(),
        endDate: json["end_date"]?.toString(),
        description: json["description"]?.toString(),
        link: json["link"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        merchant: json["merchant"] == null ? null : UserModel.fromJson(json["merchant"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "merchant_id": merchantId,
        "name": name,
        "discount_type": discountType,
        "amount": amount,
        "min_payment": minPayment,
        "maximum_discount": maximumDiscount,
        "start_date": startDate,
        "end_date": endDate,
        "description": description,
        "link": link,
        "image": image,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "merchant": merchant?.toJson(),
      };

  String? getOfferImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/offer/$image';
      return imageUrl;
    }
  }
}
