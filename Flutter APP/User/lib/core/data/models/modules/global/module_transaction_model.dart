class ModuleGlobalSubmitTransactionModel {
  String? userId;
  String? total;
  String? amount;
  String? postBalance;
  String? charge;
  String? chargeType;
  String? trxType;
  String? remark;
  String? details;
  String? receiverId;
  String? receiverType;
  String? trx;
  String? updatedAt;
  String? createdAt;
  int? id;

  ModuleGlobalSubmitTransactionModel({
    this.userId,
    this.total,
    this.amount,
    this.postBalance,
    this.charge,
    this.chargeType,
    this.trxType,
    this.remark,
    this.details,
    this.receiverId,
    this.receiverType,
    this.trx,
    this.updatedAt,
    this.createdAt,
    this.id,
  });

  factory ModuleGlobalSubmitTransactionModel.fromJson(
    Map<String, dynamic> json,
  ) =>
      ModuleGlobalSubmitTransactionModel(
        userId: json["user_id"]?.toString(),
        total: json["total"]?.toString(),
        amount: json["amount"]?.toString(),
        postBalance: json["post_balance"]?.toString(),
        charge: json["charge"]?.toString(),
        chargeType: json["charge_type"]?.toString(),
        trxType: json["trx_type"]?.toString(),
        remark: json["remark"]?.toString(),
        details: json["details"]?.toString(),
        receiverId: json["receiver_id"]?.toString(),
        receiverType: json["receiver_type"]?.toString(),
        trx: json["trx"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        id: json["id"],
      );

  Map<String, dynamic> toJson() => {
        "user_id": userId,
        "before_charge": total,
        "amount": amount,
        "post_balance": postBalance,
        "charge": charge,
        "charge_type": chargeType,
        "trx_type": trxType,
        "remark": remark,
        "details": details,
        "receiver_id": receiverId,
        "receiver_type": receiverType,
        "trx": trx,
        "updated_at": updatedAt,
        "created_at": createdAt,
        "id": id,
      };
}
