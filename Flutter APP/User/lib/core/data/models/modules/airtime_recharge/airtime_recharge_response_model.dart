// To parse this JSON data, do
//
//     final airTimeRechargeResponseModel = airTimeRechargeResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_operators_response_model.dart';

AirTimeRechargeResponseModel airTimeRechargeResponseModelFromJson(String str) => AirTimeRechargeResponseModel.fromJson(json.decode(str));

String airTimeRechargeResponseModelToJson(AirTimeRechargeResponseModel data) => json.encode(data.toJson());

class AirTimeRechargeResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  AirTimeRechargeResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory AirTimeRechargeResponseModel.fromJson(Map<String, dynamic> json) => AirTimeRechargeResponseModel(
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
  List<CountryListModel>? countries;
  List<String>? otpType;
  String? currentBalance;

  Data({this.countries, this.otpType, this.currentBalance});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        countries: json["countries"] == null
            ? []
            : List<CountryListModel>.from(
                json["countries"]!.map((x) => CountryListModel.fromJson(x)),
              ),
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
      );

  Map<String, dynamic> toJson() => {
        "countries": countries == null ? [] : List<dynamic>.from(countries!.map((x) => x.toJson())),
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
      };

  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance?.toString() ?? "") ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class CountryListModel {
  int? id;
  String? name;
  String? isoName;
  String? continent;
  String? currencyCode;
  String? currencyName;
  String? currencySymbol;
  String? flagUrl;
  List<String>? callingCodes;
  String? status;
  String? isAirtime;
  String? isGiftCard;
  String? isUtilityBill;
  List<OperatorData>? operators;
  String? createdAt;
  String? updatedAt;

  CountryListModel({
    this.id,
    this.name,
    this.isoName,
    this.continent,
    this.currencyCode,
    this.currencyName,
    this.currencySymbol,
    this.flagUrl,
    this.callingCodes,
    this.status,
    this.isAirtime,
    this.isGiftCard,
    this.isUtilityBill,
    this.operators,
    this.createdAt,
    this.updatedAt,
  });

  factory CountryListModel.fromJson(Map<String, dynamic> json) => CountryListModel(
        id: json["id"],
        name: json["name"]?.toString(),
        isoName: json["iso_name"]?.toString(),
        continent: json["continent"]?.toString(),
        currencyCode: json["currency_code"]?.toString(),
        currencyName: json["currency_name"]?.toString(),
        currencySymbol: json["currency_symbol"]?.toString(),
        flagUrl: json["flag_url"]?.toString(),
        callingCodes: json["calling_codes"] == null ? [] : List<String>.from(json["calling_codes"]!.map((x) => x)),
        status: json["status"]?.toString(),
        isAirtime: json["is_airtime"]?.toString(),
        isGiftCard: json["is_gift_card"]?.toString(),
        isUtilityBill: json["is_utility_bill"]?.toString(),
        operators: json["operators"] == null
            ? []
            : List<OperatorData>.from(
                json["operators"]!.map((x) => OperatorData.fromJson(x)),
              ),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "iso_name": isoName,
        "continent": continent,
        "currency_code": currencyCode,
        "currency_name": currencyName,
        "currency_symbol": currencySymbol,
        "flag_url": flagUrl,
        "calling_codes": callingCodes == null ? [] : List<dynamic>.from(callingCodes!.map((x) => x)),
        "status": status,
        "is_airtime": isAirtime,
        "is_gift_card": isGiftCard,
        "is_utility_bill": isUtilityBill,
        "operators": operators == null ? [] : List<dynamic>.from(operators!.map((x) => x.toJson())),
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
