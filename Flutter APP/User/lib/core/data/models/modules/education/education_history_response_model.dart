// To parse this JSON data, do
//
//     final educationHistoryResponseModel = educationHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/modules/education/education_response_model.dart';

EducationHistoryResponseModel educationHistoryResponseModelFromJson(
  String str,
) =>
    EducationHistoryResponseModel.fromJson(json.decode(str));

String educationHistoryResponseModelToJson(
  EducationHistoryResponseModel data,
) =>
    json.encode(data.toJson());

class EducationHistoryResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  EducationHistoryResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory EducationHistoryResponseModel.fromJson(Map<String, dynamic> json) => EducationHistoryResponseModel(
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
        history: json["education_fees"] == null ? null : History.fromJson(json["education_fees"]),
      );

  Map<String, dynamic> toJson() => {"education_fees": history?.toJson()};
}

class History {
  List<LatestEducationFeeHistory>? data;

  String? nextPageUrl;
  String? path;

  History({this.data, this.nextPageUrl, this.path});

  factory History.fromJson(Map<String, dynamic> json) => History(
        data: json["data"] == null
            ? []
            : List<LatestEducationFeeHistory>.from(
                json["data"]!.map((x) => LatestEducationFeeHistory.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"]?.toString(),
        path: json["path"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
        "path": path,
      };
}

class LatestEducationFeeHistory {
  int? id;
  String? userId;
  String? institutionId;
  String? amount;
  String? charge;
  String? total;
  String? trx;
  List<UsersDynamicFormSubmittedDataModel>? userData;
  String? adminFeedback;
  String? status;
  String? createdAt;
  String? updatedAt;
  String? statusBadge;
  InstituteModel? institution;

  LatestEducationFeeHistory({
    this.id,
    this.userId,
    this.institutionId,
    this.amount,
    this.charge,
    this.total,
    this.trx,
    this.userData,
    this.adminFeedback,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.statusBadge,
    this.institution,
  });

  factory LatestEducationFeeHistory.fromJson(Map<String, dynamic> json) => LatestEducationFeeHistory(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        institutionId: json["institution_id"]?.toString(),
        amount: json["amount"]?.toString(),
        charge: json["charge"]?.toString(),
        total: json["total"]?.toString(),
        trx: json["trx"]?.toString(),
        userData: json["user_data"] == null
            ? []
            : List<UsersDynamicFormSubmittedDataModel>.from(
                json["user_data"]!.map(
                  (x) => UsersDynamicFormSubmittedDataModel.fromJson(x),
                ),
              ),
        adminFeedback: json["admin_feedback"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        statusBadge: json["status_badge"]?.toString(),
        institution: json["institution"] == null ? null : InstituteModel.fromJson(json["institution"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "institution_id": institutionId,
        "amount": amount,
        "charge": charge,
        "total": total,
        "trx": trx,
        "user_data": userData == null ? [] : List<dynamic>.from(userData!.map((x) => x.toJson())),
        "admin_feedback": adminFeedback,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "status_badge": statusBadge,
        "institution": institution?.toJson(),
      };
}
