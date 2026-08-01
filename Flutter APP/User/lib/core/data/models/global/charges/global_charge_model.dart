class GlobalChargeModel {
  int? id;
  String? name;
  String? slug;
  String? fixedCharge;
  String? percentCharge;
  String? minLimit;
  String? maxLimit;
  String? agentCommissionFixed;
  String? agentCommissionPercent;
  String? merchantFixedCharge;
  String? merchantPercentCharge;
  String? monthlyLimit;
  String? dailyLimit;
  String? dailyRequestAcceptLimit;
  String? monthlyRequestAcceptLimit;
  String? cap;
  String? createdAt;
  String? updatedAt;

  GlobalChargeModel({
    this.id,
    this.name,
    this.slug,
    this.fixedCharge,
    this.percentCharge,
    this.minLimit,
    this.maxLimit,
    this.agentCommissionFixed,
    this.agentCommissionPercent,
    this.merchantFixedCharge,
    this.merchantPercentCharge,
    this.monthlyLimit,
    this.dailyLimit,
    this.dailyRequestAcceptLimit,
    this.monthlyRequestAcceptLimit,
    this.cap,
    this.createdAt,
    this.updatedAt,
  });

  factory GlobalChargeModel.fromJson(Map<String, dynamic> json) => GlobalChargeModel(
        id: json["id"],
        name: json["name"]?.toString(),
        slug: json["slug"]?.toString(),
        fixedCharge: json["fixed_charge"]?.toString(),
        percentCharge: json["percent_charge"]?.toString(),
        minLimit: json["min_limit"]?.toString(),
        maxLimit: json["max_limit"]?.toString(),
        agentCommissionFixed: json["agent_commission_fixed"]?.toString(),
        agentCommissionPercent: json["agent_commission_percent"]?.toString(),
        merchantFixedCharge: json["merchant_fixed_charge"]?.toString(),
        merchantPercentCharge: json["merchant_percent_charge"]?.toString(),
        monthlyLimit: json["monthly_limit"]?.toString(),
        dailyLimit: json["daily_limit"]?.toString(),
        dailyRequestAcceptLimit: json["daily_request_accept_limit"]?.toString(),
        monthlyRequestAcceptLimit: json["monthly_request_accept_limit"]?.toString(),
        cap: json["cap"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "slug": slug,
        "fixed_charge": fixedCharge,
        "percent_charge": percentCharge,
        "min_limit": minLimit,
        "max_limit": maxLimit,
        "agent_commission_fixed": agentCommissionFixed,
        "agent_commission_percent": agentCommissionPercent,
        "merchant_fixed_charge": merchantFixedCharge,
        "merchant_percent_charge": merchantPercentCharge,
        "monthly_limit": monthlyLimit,
        "daily_limit": dailyLimit,
        "daily_request_accept_limit": dailyRequestAcceptLimit,
        "monthly_request_accept_limit": monthlyRequestAcceptLimit,
        "cap": cap,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
