// To parse this JSON data, do
//
//     final makePaymentHistoryResponseModel = makePaymentHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/make_payment/make_payment_response_model.dart';

MakePaymentHistoryResponseModel makePaymentHistoryResponseModelFromJson(
  String str,
) =>
    MakePaymentHistoryResponseModel.fromJson(json.decode(str));

String makePaymentHistoryResponseModelToJson(
  MakePaymentHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class MakePaymentHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  MakePaymentHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory MakePaymentHistoryResponseModel.fromJson(Map<String, dynamic> json) => MakePaymentHistoryResponseModel(
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
        history: json["make_payments"] == null ? null : History.fromJson(json["make_payments"]),
      );

  Map<String, dynamic> toJson() => {"make_payments": history?.toJson()};
}

class History {
  List<LatestMakePaymentHistory>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<LatestMakePaymentHistory>.from(
                json["data"]!.map((x) => LatestMakePaymentHistory.fromJson(x)),
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
