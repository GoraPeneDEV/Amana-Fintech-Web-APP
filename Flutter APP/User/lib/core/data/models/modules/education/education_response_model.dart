// To parse this JSON data, do
//
//     final educationResponseModel = educationResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_forms_map.dart';
import 'package:ovopay/core/utils/url_container.dart';

EducationResponseModel educationResponseModelFromJson(String str) => EducationResponseModel.fromJson(json.decode(str));

String educationResponseModelToJson(EducationResponseModel data) => json.encode(data.toJson());

class EducationResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  EducationResponseModel({this.remark, this.status, this.message, this.data});

  factory EducationResponseModel.fromJson(Map<String, dynamic> json) => EducationResponseModel(
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
  List<EducationCategory>? categories;
  GlobalChargeModel? educationFeeGlobalCharge;
  List<String>? otpType;
  String? currentBalance;

  Data({
    this.categories,
    this.educationFeeGlobalCharge,
    this.otpType,
    this.currentBalance,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        categories: json["categories"] == null
            ? []
            : List<EducationCategory>.from(
                json["categories"]!.map((x) => EducationCategory.fromJson(x)),
              ),
        educationFeeGlobalCharge: json["education_charge"] == null ? null : GlobalChargeModel.fromJson(json["education_charge"]),
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
      );

  Map<String, dynamic> toJson() => {
        "categories": categories == null ? [] : List<dynamic>.from(categories!.map((x) => x.toJson())),
        "education_charge": educationFeeGlobalCharge?.toJson(),
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class EducationCategory {
  int? id;
  String? name;
  String? image;
  String? status;
  String? createdAt;
  String? updatedAt;
  List<InstituteModel>? institute;

  EducationCategory({
    this.id,
    this.name,
    this.image,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.institute,
  });

  factory EducationCategory.fromJson(Map<String, dynamic> json) => EducationCategory(
        id: json["id"],
        name: json["name"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        institute: json["institute"] == null
            ? []
            : List<InstituteModel>.from(
                json["institute"]!.map((x) => InstituteModel.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "image": image,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "institute": institute == null ? [] : List<dynamic>.from(institute!.map((x) => x.toJson())),
      };
  String? getCategoryImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/category/$image';
      return imageUrl;
    }
  }
}

class InstituteModel {
  int? id;
  String? categoryId;
  String? name;
  String? fixedCharge;
  String? percentCharge;
  String? image;
  String? formId;
  String? status;
  String? createdAt;
  String? updatedAt;
  DynamicFormsMap? form;

  InstituteModel({
    this.id,
    this.categoryId,
    this.name,
    this.fixedCharge,
    this.percentCharge,
    this.image,
    this.formId,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.form,
  });

  factory InstituteModel.fromJson(Map<String, dynamic> json) => InstituteModel(
        id: json["id"],
        categoryId: json["category_id"]?.toString(),
        name: json["name"]?.toString(),
        fixedCharge: json["fixed_charge"]?.toString(),
        percentCharge: json["percent_charge"]?.toString(),
        image: json["image"]?.toString(),
        formId: json["form_id"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        form: json["form"] == null ? null : DynamicFormsMap.fromJson(json["form"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "category_id": categoryId,
        "name": name,
        "fixed_charge": fixedCharge,
        "percent_charge": percentCharge,
        "image": image,
        "form_id": formId,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "form": form?.toJson(),
      };

  String? getInstituteImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/education_fee/$image';
      return imageUrl;
    }
  }
}
