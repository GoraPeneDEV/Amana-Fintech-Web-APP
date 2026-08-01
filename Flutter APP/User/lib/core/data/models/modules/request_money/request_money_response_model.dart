// To parse this JSON data, do
//
//     final requestMoneyResponseModel = requestMoneyResponseModelFromJson(jsonString);

import 'dart:convert';

RequestMoneyResponseModel requestMoneyResponseModelFromJson(String str) => RequestMoneyResponseModel.fromJson(json.decode(str));

String requestMoneyResponseModelToJson(RequestMoneyResponseModel data) => json.encode(data.toJson());

class RequestMoneyResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  RequestMoneyResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory RequestMoneyResponseModel.fromJson(Map<String, dynamic> json) => RequestMoneyResponseModel(
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
  MoneyRequestSubmitInfoModel? moneyRequest;
  MoneyRequestSubmitInfoModel? requestMoney;

  Data({this.moneyRequest, this.requestMoney});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        moneyRequest: json["money_request"] == null ? null : MoneyRequestSubmitInfoModel.fromJson(json["money_request"]),
        requestMoney: json["request_money"] == null ? null : MoneyRequestSubmitInfoModel.fromJson(json["request_money"]),
      );

  Map<String, dynamic> toJson() => {
        "money_request": moneyRequest?.toJson(),
        "request_money": requestMoney?.toJson(),
      };
}

class MoneyRequestSubmitInfoModel {
  String? senderId;
  String? receiverId;
  String? amount;
  String? note;
  String? trx;
  String? status;
  String? updatedAt;
  String? createdAt;
  int? id;

  MoneyRequestSubmitInfoModel({
    this.senderId,
    this.receiverId,
    this.amount,
    this.note,
    this.trx,
    this.status,
    this.updatedAt,
    this.createdAt,
    this.id,
  });

  factory MoneyRequestSubmitInfoModel.fromJson(Map<String, dynamic> json) => MoneyRequestSubmitInfoModel(
        senderId: json["sender_id"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        amount: json["amount"]?.toString(),
        note: json["note"]?.toString(),
        trx: json["trx"]?.toString(),
        status: json["status"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        id: json["id"],
      );

  Map<String, dynamic> toJson() => {
        "sender_id": senderId,
        "receiver_id": receiverId,
        "amount": amount,
        "note": note,
        "trx": trx,
        "status": status,
        "updated_at": updatedAt,
        "created_at": createdAt,
        "id": id,
      };
}
