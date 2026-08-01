// To parse this JSON data, do
//
//     final educationSubmitResponseModel = educationSubmitResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';

EducationSubmitResponseModel educationSubmitResponseModelFromJson(String str) => EducationSubmitResponseModel.fromJson(json.decode(str));

String educationSubmitResponseModelToJson(EducationSubmitResponseModel data) => json.encode(data.toJson());

class EducationSubmitResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  EducationSubmitResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory EducationSubmitResponseModel.fromJson(Map<String, dynamic> json) => EducationSubmitResponseModel(
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
  ModuleGlobalSubmitTransactionModel? educationFee;

  Data({this.educationFee});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        educationFee: json["education_fee"] == null
            ? null
            : ModuleGlobalSubmitTransactionModel.fromJson(
                json["education_fee"],
              ),
      );

  Map<String, dynamic> toJson() => {"education_fee": educationFee?.toJson()};
}
