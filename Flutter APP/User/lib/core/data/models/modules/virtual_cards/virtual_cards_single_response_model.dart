// To parse this JSON data, do
//
//     final virtualCardSingleResponseModel = virtualCardSingleResponseModelFromJson(jsonString);

import 'dart:convert';
import 'dart:typed_data';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_response_model.dart';

VirtualCardSingleResponseModel virtualCardSingleResponseModelFromJson(
  String str,
) =>
    VirtualCardSingleResponseModel.fromJson(json.decode(str));

String virtualCardSingleResponseModelToJson(
  VirtualCardSingleResponseModel data,
) =>
    json.encode(data.toJson());

class VirtualCardSingleResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  VirtualCardSingleResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory VirtualCardSingleResponseModel.fromJson(Map<String, dynamic> json) => VirtualCardSingleResponseModel(
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
  String? number;
  String? cvc;
  CardDataModel? card;
  List<VirtualCardTransactionModel>? transactions;
  String? currentBalance;
  GlobalChargeModel? charge;

  Data({
    this.number,
    this.cvc,
    this.card,
    this.transactions,
    this.currentBalance,
    this.charge,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        number: json["number"]?.toString(),
        cvc: json["cvc"]?.toString(),
        card: json["card"] == null ? null : CardDataModel.fromJson(json["card"]),
        transactions: json["transactions"] == null
            ? []
            : List<VirtualCardTransactionModel>.from(
                json["transactions"]!.map(
                  (x) => VirtualCardTransactionModel.fromJson(x),
                ),
              ),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        charge: json["charge"] == null ? null : GlobalChargeModel.fromJson(json["charge"]),
      );

  Map<String, dynamic> toJson() => {
        "number": number,
        "cvc": cvc,
        "card": card?.toJson(),
        "transactions": transactions == null ? [] : List<dynamic>.from(transactions!.map((x) => x.toJson())),
        "current_balance": currentBalance,
        "charge": charge?.toJson(),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }

  String decodedAccountNumber() {
    try {
      Uint8List decodedBytes = base64Decode(number ?? "");
      return utf8.decode(decodedBytes);
    } catch (e) {
      return "4242424242424242";
    }
  }

  String decodedCvc() {
    try {
      Uint8List decodedBytes = base64Decode(cvc ?? "");
      return utf8.decode(decodedBytes);
    } catch (e) {
      return "***";
    }
  }
}
