// To parse this JSON data, do
//
//     final donationHistoryResponseModel = donationHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/donation/donation_submit_response_model.dart';

DonationHistoryResponseModel donationHistoryResponseModelFromJson(String str) => DonationHistoryResponseModel.fromJson(json.decode(str));

String donationHistoryResponseModelToJson(DonationHistoryResponseModel data) => json.encode(data.toJson());

class DonationHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  DonationHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory DonationHistoryResponseModel.fromJson(Map<String, dynamic> json) => DonationHistoryResponseModel(
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
  History? history;

  Data({this.history});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        history: json["donations"] == null ? null : History.fromJson(json["donations"]),
      );

  Map<String, dynamic> toJson() => {"donations": history?.toJson()};
}

class History {
  List<DonationDataModel>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<DonationDataModel>.from(
                json["data"]!.map((x) => DonationDataModel.fromJson(x)),
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
