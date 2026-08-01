// To parse this JSON data, do
//
//     final scanQrCodeResponseModel = scanQrCodeResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';

ScanQrCodeResponseModel scanQrCodeResponseModelFromJson(String str) => ScanQrCodeResponseModel.fromJson(json.decode(str));

String scanQrCodeResponseModelToJson(ScanQrCodeResponseModel data) => json.encode(data.toJson());

class ScanQrCodeResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  ScanQrCodeResponseModel({this.remark, this.status, this.message, this.data});

  factory ScanQrCodeResponseModel.fromJson(Map<String, dynamic> json) => ScanQrCodeResponseModel(
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
  String? userType;
  UserModel? userData;
  List<GlobalChargeModel>? transactionCharge;

  Data({this.userType, this.userData, this.transactionCharge});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        userType: json["user_type"],
        userData: json["user_data"] == null ? null : UserModel.fromJson(json["user_data"]),
        transactionCharge: json["transaction_charge"] == null
            ? []
            : List<GlobalChargeModel>.from(
                json["transaction_charge"]!.map(
                  (x) => GlobalChargeModel.fromJson(x),
                ),
              ),
      );

  Map<String, dynamic> toJson() => {
        "user_type": userType,
        "user_data": userData?.toJson(),
        "transaction_charge": transactionCharge == null ? [] : List<dynamic>.from(transactionCharge!.map((x) => x.toJson())),
      };
}
