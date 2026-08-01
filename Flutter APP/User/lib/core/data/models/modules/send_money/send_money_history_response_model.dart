// To parse this JSON data, do
//
//     final sendMoneyHistoryResponseModel = sendMoneyHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/send_money/send_money_response_model.dart';

SendMoneyHistoryResponseModel sendMoneyHistoryResponseModelFromJson(
  String str,
) =>
    SendMoneyHistoryResponseModel.fromJson(json.decode(str));

String sendMoneyHistoryResponseModelToJson(
  SendMoneyHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class SendMoneyHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  SendMoneyHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory SendMoneyHistoryResponseModel.fromJson(Map<String, dynamic> json) => SendMoneyHistoryResponseModel(
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
  History? sendMoneysHistory;

  Data({this.sendMoneysHistory});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        sendMoneysHistory: json["send_moneys"] == null ? null : History.fromJson(json["send_moneys"]),
      );

  Map<String, dynamic> toJson() => {"send_moneys": sendMoneysHistory?.toJson()};
}

class History {
  List<LatestSendMoneyHistory>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<LatestSendMoneyHistory>.from(
                json["data"]!.map((x) => LatestSendMoneyHistory.fromJson(x)),
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
