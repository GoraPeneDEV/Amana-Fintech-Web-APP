// To parse this JSON data, do
//
//     final virtualCardHolderResponseModel = virtualCardHolderResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_response_model.dart' show CardHolder;

VirtualCardHolderResponseModel virtualCardHolderResponseModelFromJson(
  String str,
) =>
    VirtualCardHolderResponseModel.fromJson(json.decode(str));

String virtualCardHolderResponseModelToJson(
  VirtualCardHolderResponseModel data,
) =>
    json.encode(data.toJson());

class VirtualCardHolderResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  VirtualCardHolderResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory VirtualCardHolderResponseModel.fromJson(Map<String, dynamic> json) => VirtualCardHolderResponseModel(
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
  List<CardHolder>? cardHolders;
  List<String>? supportedFileFormat;
  String? maxFileSize;

  Data({this.cardHolders, this.supportedFileFormat, this.maxFileSize});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        cardHolders: json["card_holders"] == null
            ? []
            : List<CardHolder>.from(
                json["card_holders"]!.map((x) => CardHolder.fromJson(x)),
              ),
        supportedFileFormat: json["supported_file_format"] == null ? [] : List<String>.from(json["supported_file_format"]!.map((x) => x)),
        maxFileSize: json["max_file_size"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "card_holders": cardHolders == null ? [] : List<dynamic>.from(cardHolders!.map((x) => x.toJson())),
        "supported_file_format": supportedFileFormat == null ? [] : List<dynamic>.from(supportedFileFormat!.map((x) => x)),
        "max_file_size": maxFileSize,
      };
}
