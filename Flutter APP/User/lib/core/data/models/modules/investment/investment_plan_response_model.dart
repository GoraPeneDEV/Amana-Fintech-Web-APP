import 'dart:convert';

InvestmentPlanResponseModel investmentPlanResponseModelFromJson(String str) => InvestmentPlanResponseModel.fromJson(json.decode(str));

String investmentPlanResponseModelToJson(InvestmentPlanResponseModel data) => json.encode(data.toJson());

class InvestmentPlanResponseModel {
  final String? remark;
  final String? status;
  final List<String>? message;
  final Data? data;

  InvestmentPlanResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory InvestmentPlanResponseModel.fromJson(Map<String, dynamic> json) => InvestmentPlanResponseModel(
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
  final Plans? plans;
  final List<String>? supportedOtpTypes;
  final String? currentBalance;

  Data({
    this.plans,
    this.supportedOtpTypes,
    this.currentBalance,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        plans: json["plans"] == null ? null : Plans.fromJson(json["plans"]),
        supportedOtpTypes: json["supported_otp_types"] == null ? [] : List<String>.from(json["supported_otp_types"]!.map((x) => x)),
        currentBalance: json["current_balance"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "plans": plans?.toJson(),
        "supported_otp_types": supportedOtpTypes == null ? [] : List<dynamic>.from(supportedOtpTypes!.map((x) => x)),
        "current_balance": currentBalance,
      };
  double getCurrentBalance() {
    try {
      return double.tryParse(currentBalance?.toString() ?? "") ?? 0.0;
    } catch (e) {
      return 0.0;
    }
  }
}

class Plans {
  final String? currentPage;
  final List<PlanData>? data;
  final String? firstPageUrl;
  final String? from;
  final String? lastPage;
  final String? lastPageUrl;

  final String? nextPageUrl;
  final String? path;
  final String? perPage;
  final String? prevPageUrl;
  final String? to;
  final String? total;

  Plans({
    this.currentPage,
    this.data,
    this.firstPageUrl,
    this.from,
    this.lastPage,
    this.lastPageUrl,
    this.nextPageUrl,
    this.path,
    this.perPage,
    this.prevPageUrl,
    this.to,
    this.total,
  });

  factory Plans.fromJson(Map<String, dynamic> json) => Plans(
        currentPage: json["current_page"]?.toString(),
        data: json["data"] == null ? [] : List<PlanData>.from(json["data"]!.map((x) => PlanData.fromJson(x))),
        firstPageUrl: json["first_page_url"]?.toString(),
        from: json["from"]?.toString(),
        lastPage: json["last_page"]?.toString(),
        lastPageUrl: json["last_page_url"]?.toString(),
        nextPageUrl: json["next_page_url"]?.toString(),
        path: json["path"]?.toString(),
        perPage: json["per_page"]?.toString(),
        prevPageUrl: json["prev_page_url"]?.toString(),
        to: json["to"]?.toString(),
        total: json["total"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "current_page": currentPage,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "first_page_url": firstPageUrl,
        "from": from,
        "last_page": lastPage,
        "last_page_url": lastPageUrl,
        "next_page_url": nextPageUrl,
        "path": path,
        "per_page": perPage,
        "prev_page_url": prevPageUrl,
        "to": to,
        "total": total,
      };
}

class PlanData {
  final String? id;
  final String? name;
  final String? description;
  final String? investType;
  final String? fixedAmount;
  final String? minInvest;
  final String? maxInvest;
  final String? interestType;
  final String? interestAmount;
  final String? timeId;
  final String? status;
  final String? returnType;
  final String? repeatTimes;
  final String? capitalBack;
  final String? createdAt;
  final String? updatedAt;
  final InvestTime? investTime;

  PlanData({
    this.id,
    this.name,
    this.description,
    this.investType,
    this.fixedAmount,
    this.minInvest,
    this.maxInvest,
    this.interestType,
    this.interestAmount,
    this.timeId,
    this.status,
    this.returnType,
    this.repeatTimes,
    this.capitalBack,
    this.createdAt,
    this.updatedAt,
    this.investTime,
  });

  factory PlanData.fromJson(Map<String, dynamic> json) => PlanData(
        id: json["id"]?.toString(),
        name: json["name"]?.toString(),
        description: json["description"]?.toString(),
        investType: json["invest_type"]?.toString(),
        fixedAmount: json["fixed_amount"]?.toString(),
        minInvest: json["min_invest"]?.toString(),
        maxInvest: json["max_invest"]?.toString(),
        interestType: json["interest_type"]?.toString(),
        interestAmount: json["interest_amount"]?.toString(),
        timeId: json["time_id"]?.toString(),
        status: json["status"]?.toString(),
        returnType: json["return_type"]?.toString(),
        repeatTimes: json["repeat_times"]?.toString(),
        capitalBack: json["capital_back"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        investTime: json["invest_time"] == null ? null : InvestTime.fromJson(json["invest_time"]),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "description": description,
        "invest_type": investType,
        "fixed_amount": fixedAmount,
        "min_invest": minInvest,
        "max_invest": maxInvest,
        "Stringerest_type": interestType,
        "Stringerest_amount": interestAmount,
        "time_id": timeId,
        "status": status,
        "return_type": returnType,
        "repeat_times": repeatTimes,
        "capital_back": capitalBack,
        "created_at": createdAt,
        "updated_at": updatedAt,
        "invest_time": investTime?.toJson(),
      };
}

class InvestTime {
  final String? id;
  final String? name;
  final String? time;
  final String? status;
  final String? createdAt;
  final String? updatedAt;

  InvestTime({
    this.id,
    this.name,
    this.time,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory InvestTime.fromJson(Map<String, dynamic> json) => InvestTime(
        id: json["id"]?.toString(),
        name: json["name"]?.toString(),
        time: json["time"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "time": time,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
