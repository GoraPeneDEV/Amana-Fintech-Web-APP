// To parse this JSON data, do
//
//     final airTimeRechargeOperatorsResponseModel = airTimeRechargeOperatorsResponseModelFromJson(jsonString);

import 'dart:convert';

AirTimeRechargeOperatorsResponseModel airTimeRechargeOperatorsResponseModelFromJson(String str) => AirTimeRechargeOperatorsResponseModel.fromJson(json.decode(str));

String airTimeRechargeOperatorsResponseModelToJson(
  AirTimeRechargeOperatorsResponseModel data,
) =>
    json.encode(data.toJson());

class AirTimeRechargeOperatorsResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  AirTimeRechargeOperatorsResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory AirTimeRechargeOperatorsResponseModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      AirTimeRechargeOperatorsResponseModel(
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
  OperatorsInfo? operators;
  List<dynamic>? otpType;

  Data({this.operators, this.otpType});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        operators: json["operators"] == null ? null : OperatorsInfo.fromJson(json["operators"]),
        otpType: json["supported_otp_types"] == null ? [] : List<dynamic>.from(json["supported_otp_types"]!.map((x) => x)),
      );

  Map<String, dynamic> toJson() => {
        "operators": operators?.toJson(),
        "supported_otp_types": otpType == null ? [] : List<dynamic>.from(otpType!.map((x) => x)),
      };
}

class OperatorsInfo {
  List<OperatorData>? data;

  OperatorsInfo({this.data});

  factory OperatorsInfo.fromJson(Map<String, dynamic> json) => OperatorsInfo(
        data: json["data"] == null
            ? []
            : List<OperatorData>.from(
                json["data"]!.map((x) => OperatorData.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class OperatorData {
  int? id;
  String? countryId;
  String? uniqueId;
  String? name;
  String? bundle;
  String? data;
  String? pin;
  String? supportsLocalAmount;
  String? supportsGeographicalRechargePlans;
  String? denominationType;
  String? senderCurrencyCode;
  String? senderCurrencySymbol;
  String? destinationCurrencyCode;
  String? destinationCurrencySymbol;
  String? commission;
  String? internationalDiscount;
  String? localDiscount;
  String? mostPopularAmount;
  String? mostPopularLocalAmount;
  String? minAmount;
  String? maxAmount;
  String? localMinAmount;
  String? localMaxAmount;
  String? rate;
  // Fx? fx;
  List<String>? logoUrls;
  List<String>? fixedAmounts;
  // Map<String, String>? fixedAmountsDescriptions;
  List<Description>? fixedAmountsDescriptions;
  List<dynamic>? localFixedAmounts;
  LocalFixedAmountsDescriptions? localFixedAmountsDescriptions;
  List<String>? suggestedAmounts;
  // LocalFixedAmountsDescriptions? suggestedAmountsMap;
  Fees? fees;
  List<dynamic>? geographicalRechargePlans;
  String? status;
  String? reloadlyStatus;
  String? createdAt;
  String? updatedAt;

  OperatorData({
    this.id,
    this.countryId,
    this.uniqueId,
    this.name,
    this.bundle,
    this.data,
    this.pin,
    this.supportsLocalAmount,
    this.supportsGeographicalRechargePlans,
    this.denominationType,
    this.senderCurrencyCode,
    this.senderCurrencySymbol,
    this.destinationCurrencyCode,
    this.destinationCurrencySymbol,
    this.commission,
    this.internationalDiscount,
    this.localDiscount,
    this.mostPopularAmount,
    this.mostPopularLocalAmount,
    this.minAmount,
    this.maxAmount,
    this.localMinAmount,
    this.localMaxAmount,
    this.rate,
    this.logoUrls,
    this.fixedAmounts,
    this.fixedAmountsDescriptions,
    this.localFixedAmounts,
    this.localFixedAmountsDescriptions,
    this.suggestedAmounts,
    // this.suggestedAmountsMap,
    this.fees,
    this.geographicalRechargePlans,
    this.status,
    this.reloadlyStatus,
    this.createdAt,
    this.updatedAt,
  });

  factory OperatorData.fromJson(Map<String, dynamic> json) => OperatorData(
        id: json["id"],
        countryId: json["country_id"] != null ? json["country_id"].toString() : "",
        uniqueId: json["unique_id"] != null ? json["unique_id"].toString() : "",
        name: json["name"] != null ? json["name"].toString() : "",
        bundle: json["bundle"] != null ? json["bundle"].toString() : "",
        data: json["data"] != null ? json["data"].toString() : "",
        pin: json["pin"] != null ? json["pin"].toString() : "",
        supportsLocalAmount: json["supports_local_amount"] != null ? json["supports_local_amount"].toString() : "",
        supportsGeographicalRechargePlans: json["supports_geographical_recharge_plans"] != null ? json["supports_geographical_recharge_plans"].toString() : "",
        denominationType: json["denomination_type"] != null ? json["denomination_type"].toString() : "",
        senderCurrencyCode: json["sender_currency_code"] != null ? json["sender_currency_code"].toString() : "",
        senderCurrencySymbol: json["sender_currency_symbol"] != null ? json["sender_currency_symbol"].toString() : "",
        destinationCurrencyCode: json["destination_currency_code"] != null ? json["destination_currency_code"].toString() : "",
        destinationCurrencySymbol: json["destination_currency_symbol"] != null ? json["destination_currency_symbol"].toString() : "",
        commission: json["commission"] != null ? json["commission"].toString() : "",
        internationalDiscount: json["international_discount"] != null ? json["international_discount"].toString() : "",
        localDiscount: json["local_discount"] != null ? json["local_discount"].toString() : "",
        mostPopularAmount: json["most_popular_amount"] != null ? json["most_popular_amount"].toString() : "",
        mostPopularLocalAmount: json["most_popular_local_amount"] != null ? json["most_popular_local_amount"].toString() : "",
        minAmount: json["min_amount"] != null ? json["min_amount"].toString() : "",
        maxAmount: json["max_amount"] != null ? json["max_amount"].toString() : "",
        localMinAmount: json["local_min_amount"] != null ? json["local_min_amount"].toString() : "",
        localMaxAmount: json["local_max_amount"] != null ? json["local_max_amount"].toString() : "",
        rate: json["rate"]?.toString(),

        logoUrls: json["logo_urls"] == null ? [] : List<String>.from(json["logo_urls"]!.map((x) => x)),
        fixedAmounts: json["fixed_amounts"] == null
            ? []
            : List<String>.from(
                json["fixed_amounts"]!.map((x) => x?.toString()),
              ),
        fixedAmountsDescriptions: (json["fixed_amounts_descriptions"] != null && json["fixed_amounts_descriptions"] is Map)
            ? Map<String, dynamic>.from(json["fixed_amounts_descriptions"])
                .entries
                .map(
                  (e) => Description(
                    amount: e.key,
                    description: e.value ?? "",
                  ),
                )
                .toList()
            : [],

        localFixedAmounts: json["local_fixed_amounts"] == null ? [] : List<dynamic>.from(json["local_fixed_amounts"]!.map((x) => x)),
        localFixedAmountsDescriptions: (json["local_fixed_amounts_descriptions"] != null && json["local_fixed_amounts_descriptions"] is Map)
            ? LocalFixedAmountsDescriptions.fromJson(
                Map<String, dynamic>.from(json["local_fixed_amounts_descriptions"]),
              )
            : null,
        suggestedAmounts: json["suggested_amounts"] == null
            ? []
            : List<String>.from(
                json["suggested_amounts"]!.map((x) => x?.toString()),
              ),
        // suggestedAmountsMap: json["suggested_amounts_map"] == null
        //     ? null
        //     : LocalFixedAmountsDescriptions.fromJson(
        //         json["suggested_amounts_map"],
        //       ),
        fees: json["fees"] == null ? null : Fees.fromJson(json["fees"]),
        geographicalRechargePlans: json["geographical_recharge_plans"] == null
            ? []
            : List<dynamic>.from(
                json["geographical_recharge_plans"]!.map((x) => x),
              ),
        status: json["status"] != null ? json["status"].toString() : "",
        reloadlyStatus: json["reloadly_status"] != null ? json["reloadly_status"].toString() : "",
        createdAt: json["created_at"],
        updatedAt: json["updated_at"],
      );
  Map<String, dynamic> toJson() => {
        "id": id,
        "country_id": countryId,
        "unique_id": uniqueId,
        "name": name,
        "bundle": bundle,
        "data": data,
        "pin": pin,
        "supports_local_amount": supportsLocalAmount,
        "supports_geographical_recharge_plans": supportsGeographicalRechargePlans,
        "denomination_type": denominationType,
        "sender_currency_code": senderCurrencyCode,
        "sender_currency_symbol": senderCurrencySymbol,
        "destination_currency_code": destinationCurrencyCode,
        "destination_currency_symbol": destinationCurrencySymbol,
        "commission": commission,
        "international_discount": internationalDiscount,
        "local_discount": localDiscount,
        "most_popular_amount": mostPopularAmount,
        "most_popular_local_amount": mostPopularLocalAmount,
        "min_amount": minAmount,
        "max_amount": maxAmount,
        "local_min_amount": localMinAmount,
        "local_max_amount": localMaxAmount,
        "rate": rate,
        "logo_urls": logoUrls ?? [],
        "fixed_amounts": fixedAmounts ?? [],
        "fixed_amounts_descriptions": fixedAmountsDescriptions == null
            ? {}
            : {
                for (var desc in fixedAmountsDescriptions!) desc.amount!: desc.description,
              },
        "local_fixed_amounts": localFixedAmounts ?? [],
        "local_fixed_amounts_descriptions": localFixedAmountsDescriptions?.toJson(),
        "suggested_amounts": suggestedAmounts ?? [],
        // "suggested_amounts_map": suggestedAmountsMap?.toJson(),
        "fees": fees?.toJson(),
        "geographical_recharge_plans": geographicalRechargePlans ?? [],
        "status": status,
        "reloadly_status": reloadlyStatus,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}

class Description {
  String? amount;
  String? description;

  Description({this.amount, this.description});

  Map<String, dynamic> toJson() => {
        "amount": amount,
        "description": description,
      };
}

class Fees {
  String? international;
  String? local;
  String? localPercentage;
  String? internationalPercentage;

  Fees({
    this.international,
    this.local,
    this.localPercentage,
    this.internationalPercentage,
  });

  factory Fees.fromJson(Map<String, dynamic> json) => Fees(
        international: json["international"] != null ? json["international"].toString() : "",
        local: json["local"] != null ? json["local"].toString() : "",
        localPercentage: json["localPercentage"] != null ? json["localPercentage"].toString() : "",
        internationalPercentage: json["internationalPercentage"] != null ? json["internationalPercentage"].toString() : "",
      );

  Map<String, dynamic> toJson() => {
        "international": international,
        "local": local,
        "localPercentage": localPercentage,
        "internationalPercentage": internationalPercentage,
      };
}

class LocalFixedAmountsDescriptions {
  LocalFixedAmountsDescriptions();

  factory LocalFixedAmountsDescriptions.fromJson(Map<String, dynamic> json) => LocalFixedAmountsDescriptions();

  Map<String, dynamic> toJson() => {};
}
