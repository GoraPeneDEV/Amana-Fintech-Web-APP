// To parse this JSON data, do
//
//     final cashOutHistoryResponseModel = cashOutHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/cash_out/cash_out_response_model.dart';

CashOutHistoryResponseModel cashOutHistoryResponseModelFromJson(String str) => CashOutHistoryResponseModel.fromJson(json.decode(str));

String cashOutHistoryResponseModelToJson(CashOutHistoryResponseModel data) => json.encode(data.toJson());

class CashOutHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  CashOutHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory CashOutHistoryResponseModel.fromJson(Map<String, dynamic> json) => CashOutHistoryResponseModel(
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
        history: json["cash_outs"] == null ? null : History.fromJson(json["cash_outs"]),
      );

  Map<String, dynamic> toJson() => {"cash_outs": history?.toJson()};
}

class History {
  List<LatestCashOutHistory>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<LatestCashOutHistory>.from(
                json["data"]!.map((x) => LatestCashOutHistory.fromJson(x)),
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
