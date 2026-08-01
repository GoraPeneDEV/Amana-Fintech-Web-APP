// To parse this JSON data, do
//
//     final virtualCardInfoResponseModel = virtualCardInfoResponseModelFromJson(jsonString);

import 'dart:convert';

VirtualCardInfoResponseModel virtualCardInfoResponseModelFromJson(String str) => VirtualCardInfoResponseModel.fromJson(json.decode(str));

String virtualCardInfoResponseModelToJson(VirtualCardInfoResponseModel data) => json.encode(data.toJson());

class VirtualCardInfoResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  VirtualCardInfoResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory VirtualCardInfoResponseModel.fromJson(Map<String, dynamic> json) => VirtualCardInfoResponseModel(
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
  List<CardDataModel>? cards;

  Data({this.cards});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        cards: json["cards"] == null
            ? []
            : List<CardDataModel>.from(
                json["cards"]!.map((x) => CardDataModel.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "cards": cards == null ? [] : List<dynamic>.from(cards!.map((x) => x.toJson())),
      };
}

class CardDataModel {
  int? id;
  String? userId;
  String? last4;
  String? cardNumber;
  String? cvcNumber;
  String? expMonth;
  String? expYear;
  String? balance;
  String? brand;
  String? spendingLimit;
  String? currentSpend;
  String? cardholderId;
  String? cardId;
  String? cardType;
  String? chargedAt;
  String? status;
  String? createdAt;
  String? updatedAt;
  CardHolder? cardHolder;

  CardDataModel({
    this.id,
    this.userId,
    this.last4,
    this.cardNumber,
    this.cvcNumber,
    this.expMonth,
    this.expYear,
    this.balance,
    this.brand,
    this.spendingLimit,
    this.currentSpend,
    this.cardholderId,
    this.cardId,
    this.cardType,
    this.chargedAt,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.cardHolder,
  });

  factory CardDataModel.fromJson(Map<String, dynamic> json) => CardDataModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        last4: json["last4"]?.toString(),
        cardNumber: json["card_number"]?.toString(),
        cvcNumber: json["cvc_number"]?.toString(),
        expMonth: json["exp_month"]?.toString(),
        expYear: json["exp_year"]?.toString(),
        balance: json["balance"]?.toString(),
        brand: json["brand"]?.toString(),
        spendingLimit: json["spending_limit"]?.toString(),
        currentSpend: json["current_spend"]?.toString(),
        cardholderId: json["cardholder_id"]?.toString(),
        cardId: json["card_id"]?.toString(),
        cardType: json["card_type"]?.toString(),
        chargedAt: json["charged_at"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        cardHolder: json["card_holder"] == null ? null : CardHolder.fromJson(json["card_holder"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "last4": last4,
        "card_number": cardNumber,
        "cvc_number": cvcNumber,
        "exp_month": expMonth,
        "exp_year": expYear,
        "balance": balance,
        "brand": brand,
        "spending_limit": spendingLimit,
        "current_spend": currentSpend,
        "cardholder_id": cardholderId,
        "card_id": cardId,
        "card_type": cardType,
        "charged_at": chargedAt,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "card_holder": cardHolder?.toJson(),
      };
  String formatCardExpiry() {
    String month = expMonth?.padLeft(2, '0') ?? "00"; // Ensure two-digit format
    String year = expYear?.substring(2, 4) ?? "00"; // Get last two digits of the year
    return "$month/$year";
  }
}

class CardHolder {
  int? id;
  String? cardHolderId;
  String? userId;
  String? firstName;
  String? lastName;
  String? name;
  String? email;
  String? phoneNumber;
  String? address;
  String? state;
  String? postalCode;
  String? city;
  String? country;
  DateOfBirthModel? dob;
  String? documentFront;
  String? documentBack;
  String? createdAt;
  String? updatedAt;

  CardHolder({
    this.id,
    this.cardHolderId,
    this.userId,
    this.firstName,
    this.lastName,
    this.name,
    this.email,
    this.phoneNumber,
    this.address,
    this.state,
    this.postalCode,
    this.city,
    this.country,
    this.dob,
    this.documentFront,
    this.documentBack,
    this.createdAt,
    this.updatedAt,
  });

  factory CardHolder.fromJson(Map<String, dynamic> json) => CardHolder(
        id: json["id"],
        cardHolderId: json["card_holder_id"]?.toString(),
        userId: json["user_id"]?.toString(),
        firstName: json["first_name"]?.toString(),
        lastName: json["last_name"]?.toString(),
        name: json["name"]?.toString(),
        email: json["email"]?.toString(),
        phoneNumber: json["phone_number"]?.toString(),
        address: json["address"]?.toString(),
        state: json["state"]?.toString(),
        postalCode: json["postal_code"]?.toString(),
        city: json["city"]?.toString(),
        country: json["country"]?.toString(),
        dob: json["dob"] == null ? null : DateOfBirthModel.fromJson(json["dob"]),
        documentFront: json["document_front"]?.toString(),
        documentBack: json["document_back"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "card_holder_id": cardHolderId,
        "user_id": userId,
        "first_name": firstName,
        "last_name": lastName,
        "name": name,
        "email": email,
        "phone_number": phoneNumber,
        "address": address,
        "state": state,
        "postal_code": postalCode,
        "city": city,
        "country": country,
        "dob": dob?.toJson(),
        "document_front": documentFront,
        "document_back": documentBack,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class DateOfBirthModel {
  String? day;
  String? month;
  String? year;
  String? fullText;

  DateOfBirthModel({this.day, this.month, this.year, this.fullText});

  factory DateOfBirthModel.fromJson(Map<String, dynamic> json) => DateOfBirthModel(
        day: json["day"]?.toString(),
        month: json["month"]?.toString(),
        year: json["year"]?.toString(),
        fullText: json["full_text"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "day": day,
        "month": month,
        "year": year,
        "full_text": fullText,
      };
}
