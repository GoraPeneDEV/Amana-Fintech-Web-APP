// To parse this JSON data, do
//
//     final microFinanceHistoryResponseModel = microFinanceHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/microfinance/micro_finance_response_model.dart';

MicroFinanceHistoryResponseModel microFinanceHistoryResponseModelFromJson(
  String str,
) =>
    MicroFinanceHistoryResponseModel.fromJson(json.decode(str));

String microFinanceHistoryResponseModelToJson(
  MicroFinanceHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class MicroFinanceHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  MicroFinanceHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory MicroFinanceHistoryResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      MicroFinanceHistoryResponseModel(
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
        history: json["micro_finances"] == null ? null : History.fromJson(json["micro_finances"]),
      );

  Map<String, dynamic> toJson() => {"micro_finances": history?.toJson()};
}

class History {
  List<LatestMicrofinancePayHistoryModel>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<LatestMicrofinancePayHistoryModel>.from(
                json["data"]!.map(
                  (x) => LatestMicrofinancePayHistoryModel.fromJson(x),
                ),
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
