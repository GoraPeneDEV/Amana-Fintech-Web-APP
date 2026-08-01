// To parse this JSON data, do
//
//     final requestMoneyHistoryResponseModel = requestMoneyHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/user/user_model.dart';

RequestMoneyHistoryResponseModel requestMoneyHistoryResponseModelFromJson(
  String str,
) =>
    RequestMoneyHistoryResponseModel.fromJson(json.decode(str));

String requestMoneyHistoryResponseModelToJson(
  RequestMoneyHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class RequestMoneyHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  RequestMoneyHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory RequestMoneyHistoryResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      RequestMoneyHistoryResponseModel(
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
  Requests? requestMoneys;
  Requests? requestedMoneys;

  Data({this.requestMoneys, this.requestedMoneys});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        requestMoneys: json["request_moneys"] == null ? null : Requests.fromJson(json["request_moneys"]),
        requestedMoneys: json["requested_moneys"] == null ? null : Requests.fromJson(json["requested_moneys"]),
      );

  Map<String, dynamic> toJson() => {
        "request_moneys": requestMoneys?.toJson(),
        "requested_moneys": requestedMoneys?.toJson(),
      };
}

class Requests {
  int? currentPage;
  List<RequestMoneyHistoryDataModel>? data;

  String? lastPageUrl;
  String? nextPageUrl;
  String? path;

  Requests({
    this.currentPage,
    this.data,
    this.lastPageUrl,
    this.nextPageUrl,
    this.path,
  });

  factory Requests.fromJson(Map<String, dynamic> json) => Requests(
        currentPage: json["current_page"],
        data: json["data"] == null
            ? []
            : List<RequestMoneyHistoryDataModel>.from(
                json["data"]!.map(
                  (x) => RequestMoneyHistoryDataModel.fromJson(x),
                ),
              ),
        lastPageUrl: json["last_page_url"],
        nextPageUrl: json["next_page_url"],
        path: json["path"],
      );

  Map<String, dynamic> toJson() => {
        "current_page": currentPage,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "last_page_url": lastPageUrl,
        "next_page_url": nextPageUrl,
        "path": path,
      };
}

class RequestMoneyHistoryDataModel {
  int? id;
  String? senderId;
  String? receiverId;
  String? amount;
  String? note;
  String? status;
  String? createdAt;
  String? updatedAt;
  UserModel? requestReceiver;
  UserModel? requestSender;
  RequestMoneyHistoryDataModel({
    this.id,
    this.senderId,
    this.receiverId,
    this.amount,
    this.note,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.requestReceiver,
    this.requestSender,
  });

  factory RequestMoneyHistoryDataModel.fromJson(Map<String, dynamic> json) => RequestMoneyHistoryDataModel(
        id: json["id"],
        senderId: json["sender_id"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        amount: json["amount"]?.toString(),
        note: json["note"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        requestReceiver: json["request_receiver"] == null ? null : UserModel.fromJson(json["request_receiver"]),
        requestSender: json["request_sender"] == null ? null : UserModel.fromJson(json["request_sender"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "sender_id": senderId,
        "receiver_id": receiverId,
        "amount": amount,
        "note": note,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "request_receiver": requestReceiver?.toJson(),
        "request_sender": requestSender?.toJson(),
      };
}
