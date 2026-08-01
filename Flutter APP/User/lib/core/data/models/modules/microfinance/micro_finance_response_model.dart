// To parse this JSON data, do
//
//     final microFinanceResponseModel = microFinanceResponseModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_forms_map.dart';
import 'package:ovopay/core/utils/url_container.dart';

MicroFinanceResponseModel microFinanceResponseModelFromJson(String str) => MicroFinanceResponseModel.fromJson(json.decode(str));

String microFinanceResponseModelToJson(MicroFinanceResponseModel data) => json.encode(data.toJson());

class MicroFinanceResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  MicroFinanceResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory MicroFinanceResponseModel.fromJson(Map<String, dynamic> json) => MicroFinanceResponseModel(
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
  List<MicrofinanceNgo>? allNgoList;
  List<LatestMicrofinancePayHistoryModel>? latestMicrofinancePayHistory;
  GlobalChargeModel? microfinanceGlobalCharge;

  Data({
    this.otpType,
    this.currentBalance,
    this.allNgoList,
    this.latestMicrofinancePayHistory,
    this.microfinanceGlobalCharge,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        allNgoList: json["all_ngo"] == null
            ? []
            : List<MicrofinanceNgo>.from(
                json["all_ngo"]!.map((x) => MicrofinanceNgo.fromJson(x)),
              ),
        latestMicrofinancePayHistory: json["latest_microfinance"] == null
            ? []
            : List<LatestMicrofinancePayHistoryModel>.from(
                json["latest_microfinance"]!.map(
                  (x) => LatestMicrofinancePayHistoryModel.fromJson(x),
                ),
              ),
        microfinanceGlobalCharge: json["microfinance_charge"] == null ? null : GlobalChargeModel.fromJson(json["microfinance_charge"]),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "all_ngo": allNgoList == null ? [] : List<dynamic>.from(allNgoList!.map((x) => x.toJson())),
        "latest_microfinance": latestMicrofinancePayHistory == null
            ? []
            : List<dynamic>.from(
                latestMicrofinancePayHistory!.map((x) => x.toJson()),
              ),
        "microfinance_charge": microfinanceGlobalCharge?.toJson(),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class LatestMicrofinancePayHistoryModel {
  int? id;
  String? userId;
  String? ngoId;
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
  MicrofinanceNgo? ngo;

  LatestMicrofinancePayHistoryModel({
    this.id,
    this.userId,
    this.ngoId,
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
    this.ngo,
  });

  factory LatestMicrofinancePayHistoryModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      LatestMicrofinancePayHistoryModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        ngoId: json["ngo_id"]?.toString(),
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
        ngo: json["ngo"] == null ? null : MicrofinanceNgo.fromJson(json["ngo"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "ngo_id": ngoId,
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
        "ngo": ngo?.toJson(),
      };
}

class GetTrx {
  int? id;
  String? userId;
  String? agentId;
  String? merchantId;
  String? charityId;
  String? amount;
  String? receiverId;
  String? receiverType;
  String? beforeCharge;
  String? charge;
  String? chargeType;
  String? postBalance;
  String? trxType;
  String? trx;
  String? details;
  String? remark;
  dynamic reference;
  String? hideIdentity;
  String? createdAt;
  String? updatedAt;

  GetTrx({
    this.id,
    this.userId,
    this.agentId,
    this.merchantId,
    this.charityId,
    this.amount,
    this.receiverId,
    this.receiverType,
    this.beforeCharge,
    this.charge,
    this.chargeType,
    this.postBalance,
    this.trxType,
    this.trx,
    this.details,
    this.remark,
    this.reference,
    this.hideIdentity,
    this.createdAt,
    this.updatedAt,
  });

  factory GetTrx.fromJson(Map<String, dynamic> json) => GetTrx(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        agentId: json["agent_id"]?.toString(),
        merchantId: json["merchant_id"]?.toString(),
        charityId: json["charity_id"]?.toString(),
        amount: json["amount"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        receiverType: json["receiver_type"]?.toString(),
        beforeCharge: json["before_charge"]?.toString(),
        charge: json["charge"]?.toString(),
        chargeType: json["charge_type"]?.toString(),
        postBalance: json["post_balance"]?.toString(),
        trxType: json["trx_type"]?.toString(),
        trx: json["trx"]?.toString(),
        details: json["details"]?.toString(),
        remark: json["remark"]?.toString(),
        reference: json["reference"]?.toString(),
        hideIdentity: json["hide_identity"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "agent_id": agentId,
        "merchant_id": merchantId,
        "charity_id": charityId,
        "amount": amount,
        "receiver_id": receiverId,
        "receiver_type": receiverType,
        "before_charge": beforeCharge,
        "charge": charge,
        "charge_type": chargeType,
        "post_balance": postBalance,
        "trx_type": trxType,
        "trx": trx,
        "details": details,
        "remark": remark,
        "reference": reference,
        "hide_identity": hideIdentity,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class MicrofinanceNgo {
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

  MicrofinanceNgo({
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

  factory MicrofinanceNgo.fromJson(
    Map<String, dynamic> json,
  ) =>
      MicrofinanceNgo(
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
  String? getNgoImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/microfinance/$image';
      return imageUrl;
    }
  }
}
