// To parse this JSON data, do
//
//     final bankTransferInfoResponseModel = bankTransferInfoResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_forms_map.dart';
import 'package:ovopay/core/utils/url_container.dart';

BankTransferInfoResponseModel bankTransferInfoResponseModelFromJson(
  String str,
) =>
    BankTransferInfoResponseModel.fromJson(json.decode(str));

String bankTransferInfoResponseModelToJson(
  BankTransferInfoResponseModel data,
) =>
    json.encode(data.toJson());

class BankTransferInfoResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BankTransferInfoResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BankTransferInfoResponseModel.fromJson(Map<String, dynamic> json) => BankTransferInfoResponseModel(
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
  List<String>? otpType;
  String? currentBalance;
  GlobalChargeModel? bankTransferCharge;
  List<BankDataModel>? allBanks;
  List<MyAddedBank>? mySavedBanks;

  Data({
    this.otpType,
    this.currentBalance,
    this.bankTransferCharge,
    this.allBanks,
    this.mySavedBanks,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        bankTransferCharge: json["bank_transfer_charge"] == null ? null : GlobalChargeModel.fromJson(json["bank_transfer_charge"]),
        allBanks: json["all_bank"] == null
            ? []
            : List<BankDataModel>.from(
                json["all_bank"]!.map((x) => BankDataModel.fromJson(x)),
              ),
        mySavedBanks: json["saved_banks"] == null
            ? []
            : List<MyAddedBank>.from(
                json["saved_banks"]!.map((x) => MyAddedBank.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "bank_transfer_charge": bankTransferCharge?.toJson(),
        "all_bank": allBanks == null ? [] : List<dynamic>.from(allBanks!.map((x) => x.toJson())),
        "saved_banks": mySavedBanks == null ? [] : List<dynamic>.from(mySavedBanks!.map((x) => x.toJson())),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class BankDataModel {
  int? id;
  String? name;
  String? fixedCharge;
  String? percentCharge;
  String? formId;
  String? image;
  String? status;
  String? createdAt;
  String? updatedAt;
  DynamicFormsMap? form;

  BankDataModel({
    this.id,
    this.name,
    this.fixedCharge,
    this.percentCharge,
    this.formId,
    this.image,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.form,
  });

  factory BankDataModel.fromJson(Map<String, dynamic> json) => BankDataModel(
        id: json["id"],
        name: json["name"]?.toString(),
        fixedCharge: json["fixed_charge"]?.toString(),
        percentCharge: json["percent_charge"]?.toString(),
        formId: json["form_id"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        form: json["form"] == null ? null : DynamicFormsMap.fromJson(json["form"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "fixed_charge": fixedCharge,
        "percent_charge": percentCharge,
        "form_id": formId,
        "image": image,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "form": form?.toJson(),
      };

  String? getBankImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/bank_transfer/$image';
      return imageUrl;
    }
  }
}

class MyAddedBank {
  int? id;
  String? userId;
  String? bankId;
  String? accountHolder;
  String? accountNumber;
  List<UsersDynamicFormSubmittedDataModel>? userData;
  String? createdAt;
  String? updatedAt;
  BankDataModel? bank;

  MyAddedBank({
    this.id,
    this.userId,
    this.bankId,
    this.accountHolder,
    this.accountNumber,
    this.userData,
    this.createdAt,
    this.updatedAt,
    this.bank,
  });

  factory MyAddedBank.fromJson(Map<String, dynamic> json) => MyAddedBank(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        bankId: json["bank_id"]?.toString(),
        accountHolder: json["account_holder"]?.toString(),
        accountNumber: json["account_number"]?.toString(),
        userData: json["user_data"] == null
            ? []
            : List<UsersDynamicFormSubmittedDataModel>.from(
                json["user_data"]!.map(
                  (x) => UsersDynamicFormSubmittedDataModel.fromJson(x),
                ),
              ),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        bank: json["bank"] == null ? null : BankDataModel.fromJson(json["bank"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "bank_id": bankId,
        "account_holder": accountHolder,
        "account_number": accountNumber,
        "user_data": userData == null ? [] : List<dynamic>.from(userData!.map((x) => x.toJson())),
        "created_at": createdAt,
        "updated_at": updatedAt,
        "bank": bank?.toJson(),
      };
}
