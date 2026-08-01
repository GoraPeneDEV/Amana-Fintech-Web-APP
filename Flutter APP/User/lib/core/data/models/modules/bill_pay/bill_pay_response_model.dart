// To parse this JSON data, do
//
//     final billPayCategoryAndCompanyModel = billPayCategoryAndCompanyModelFromJson(jsonString);

import 'dart:convert';

import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_response_model.dart';
import 'package:ovopay/core/utils/url_container.dart';

BillPayCategoryAndCompanyModel billPayCategoryAndCompanyModelFromJson(
  String str,
) =>
    BillPayCategoryAndCompanyModel.fromJson(json.decode(str));

String billPayCategoryAndCompanyModelToJson(
  BillPayCategoryAndCompanyModel data,
) =>
    json.encode(data.toJson());

class BillPayCategoryAndCompanyModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  BillPayCategoryAndCompanyModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory BillPayCategoryAndCompanyModel.fromJson(Map<String, dynamic> json) => BillPayCategoryAndCompanyModel(
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
  List<BillPayCategory>? billCategory;
  GlobalChargeModel? utilityCharge;
  List<UserSavedCompany>? userCompanies;
  List<CountryListModel>? counties;

  Data({
    this.otpType,
    this.currentBalance,
    this.billCategory,
    this.utilityCharge,
    this.userCompanies,
    this.counties,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        otpType: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString() ?? "0",
        billCategory: json["bill_category"] == null ? [] : List<BillPayCategory>.from(json["bill_category"]!.map((x) => BillPayCategory.fromJson(x))),
        utilityCharge: json["utility_charge"] == null ? null : GlobalChargeModel.fromJson(json["utility_charge"]),
        userCompanies: json["user_companies"] == null ? [] : List<UserSavedCompany>.from(json["user_companies"]!.map((x) => UserSavedCompany.fromJson(x))),
        counties: json["counties"] == null ? [] : List<CountryListModel>.from(json["counties"]!.map((x) => CountryListModel.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
        "current_balance": currentBalance,
        "bill_category": billCategory == null ? [] : List<dynamic>.from(billCategory!.map((x) => x.toJson())),
        "utility_charge": utilityCharge?.toJson(),
        "user_companies": userCompanies == null ? [] : List<dynamic>.from(userCompanies!.map((x) => x.toJson())),
        "counties": counties == null ? [] : List<dynamic>.from(counties!.map((x) => x.toJson())),
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance.toString()) ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class BillPayCategory {
  String? id;
  String? name;
  String? formattedName;
  String? image;
  String? status;
  String? createdAt;
  String? updatedAt;
  List<BillPayCompany>? company;

  BillPayCategory({
    this.id,
    this.name,
    this.formattedName,
    this.image,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.company,
  });

  factory BillPayCategory.fromJson(Map<String, dynamic> json) => BillPayCategory(
        id: json["id"]?.toString(),
        name: json["name"]?.toString(),
        formattedName: json["formatted_name"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        company: json["company"] == null
            ? []
            : List<BillPayCompany>.from(
                json["company"]!.map((x) => BillPayCompany.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "formatted_name": formattedName,
        "image": image,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "company": company == null ? [] : List<dynamic>.from(company!.map((x) => x.toJson())),
      };
  String? getCategoryImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/setup_utility/$image';
      return imageUrl;
    }
  }
}

class BillPayCompany {
  int? id;
  String? categoryId;
  String? countryId;
  String? name;
  String? currencyCode;
  String? serviceType;
  String? fixedCharge;
  String? percentCharge;
  String? minimumAmount;
  String? maximumAmount;
  String? image;
  String? status;
  String? rate;
  String? companyId;
  String? denominationType;
  List<FixedAmount>? fixedAmounts;
  String? createdAt;
  String? updatedAt;

  BillPayCompany({
    this.id,
    this.categoryId,
    this.countryId,
    this.name,
    this.currencyCode,
    this.serviceType,
    this.fixedCharge,
    this.percentCharge,
    this.minimumAmount,
    this.maximumAmount,
    this.image,
    this.status,
    this.rate,
    this.companyId,
    this.denominationType,
    this.fixedAmounts,
    this.createdAt,
    this.updatedAt,
  });

  factory BillPayCompany.fromJson(Map<String, dynamic> json) => BillPayCompany(
        id: json["id"],
        categoryId: json["category_id"]?.toString(),
        countryId: json["country_id"]?.toString(),
        name: json["name"]?.toString(),
        currencyCode: json["currency_code"]?.toString(),
        serviceType: json["service_type"]?.toString(),
        fixedCharge: json["fixed_charge"]?.toString(),
        percentCharge: json["percent_charge"]?.toString(),
        minimumAmount: json["minimum_amount"]?.toString(),
        maximumAmount: json["maximum_amount"]?.toString(),
        image: json["image"]?.toString(),
        status: json["status"]?.toString(),
        rate: json["rate"]?.toString(),
        companyId: json["company_id"]?.toString(),
        denominationType: json["denomination_type"]?.toString(),
        fixedAmounts: json["fixed_amounts"] == null ? [] : List<FixedAmount>.from(json["fixed_amounts"]!.map((x) => FixedAmount.fromJson(x))),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "category_id": categoryId,
        "country_id": countryId,
        "name": name,
        "currency_code": currencyCode,
        "service_type": serviceType,
        "fixed_charge": fixedCharge,
        "percent_charge": percentCharge,
        "minimum_amount": minimumAmount,
        "maximum_amount": maximumAmount,
        "image": image,
        "status": status,
        "rate": rate,
        "company_id": companyId,
        "denomination_type": denominationType,
        "fixed_amounts": fixedAmounts == null ? [] : List<dynamic>.from(fixedAmounts!.map((x) => x.toJson())),
        "created_at": createdAt,
        "updated_at": updatedAt,
      };

  String? getCompanyImageUrl() {
    if (image == null) {
      return null;
    } else {
      var imageUrl = '${UrlContainer.domainUrl}/assets/images/setup_utility/$image';
      return imageUrl;
    }
  }
}

class FixedAmount {
  String? id;
  String? amount;
  String? description;

  FixedAmount({
    this.id,
    this.amount,
    this.description,
  });

  factory FixedAmount.fromJson(Map<String, dynamic> json) => FixedAmount(
        id: json["id"]?.toString(),
        amount: json["amount"]?.toString(),
        description: json["description"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "amount": amount,
        "description": description,
      };
}

class UserSavedCompany {
  int? id;
  String? userId;
  String? companyId;
  String? uniqueId;
  List<UsersDynamicFormSubmittedDataModel>? userData;
  String? createdAt;
  String? updatedAt;
  BillPayCompany? company;

  UserSavedCompany({
    this.id,
    this.userId,
    this.companyId,
    this.uniqueId,
    this.userData,
    this.createdAt,
    this.updatedAt,
    this.company,
  });

  factory UserSavedCompany.fromJson(Map<String, dynamic> json) => UserSavedCompany(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        companyId: json["company_id"]?.toString(),
        uniqueId: json["unique_id"]?.toString(),
        userData: json["user_data"] == null
            ? []
            : List<UsersDynamicFormSubmittedDataModel>.from(
                json["user_data"]!.map(
                  (x) => UsersDynamicFormSubmittedDataModel.fromJson(x),
                ),
              ),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        company: json["company"] == null ? null : BillPayCompany.fromJson(json["company"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "company_id": companyId,
        "unique_id": uniqueId,
        "user_data": userData == null ? [] : List<dynamic>.from(userData!.map((x) => x.toJson())),
        "created_at": createdAt,
        "updated_at": updatedAt,
        "company": company?.toJson(),
      };
}
