// To parse this JSON data, do
//
//     final countryModel = countryModelFromJson(jsonString);

import 'dart:convert';

CountryModel countryModelFromJson(String str) => CountryModel.fromJson(json.decode(str));

String countryModelToJson(CountryModel data) => json.encode(data.toJson());

class CountryModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  CountryModel({this.remark, this.status, this.message, this.data});

  factory CountryModel.fromJson(Map<String, dynamic> json) => CountryModel(
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
  List<CountryData>? countries;
  String? selectedCountryCode;
  Data({this.countries, this.selectedCountryCode});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        countries: json["countries"] == null
            ? []
            : List<CountryData>.from(
                json["countries"]!.map((x) => CountryData.fromJson(x)),
              ),
        selectedCountryCode: json["selected_country_code"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "countries": countries == null ? [] : List<dynamic>.from(countries!.map((x) => x.toJson())),
        "selected_country_code": selectedCountryCode,
      };
}

class CountryData {
  int? id;
  String? name;
  String? code;
  String? isoName;
  String? dialCode;
  String? mobileNumberDigit;
  String? status;
  String? createdAt;
  String? updatedAt;
  String? imageSrc;

  CountryData({this.id, this.name, this.code, this.dialCode, this.mobileNumberDigit, this.status, this.createdAt, this.updatedAt, this.imageSrc, this.isoName});

  factory CountryData.fromJson(Map<String, dynamic> json) => CountryData(
        id: json["id"],
        name: json["name"]?.toString(),
        code: json["code"]?.toString(),
        dialCode: json["dial_code"]?.toString(),
        mobileNumberDigit: json["mobile_number_digit"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        imageSrc: json["image_src"]?.toString(),
        isoName: json["iso_name"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "code": code,
        "dial_code": dialCode,
        "mobile_number_digit": mobileNumberDigit,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "image_src": imageSrc,
      };
  int getMobileNumberDigit() {
    try {
      return int.tryParse(mobileNumberDigit ?? "") ?? 0;
    } catch (e) {
      return 0;
    }
  }
}
