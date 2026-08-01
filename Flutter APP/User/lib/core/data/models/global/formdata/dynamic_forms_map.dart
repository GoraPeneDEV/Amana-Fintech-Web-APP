import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';

class DynamicFormsMap {
  int? id;
  String? act;
  KycFormData? formData;
  String? createdAt;
  String? updatedAt;

  DynamicFormsMap({
    this.id,
    this.act,
    this.formData,
    this.createdAt,
    this.updatedAt,
  });

  factory DynamicFormsMap.fromJson(Map<String, dynamic> json) => DynamicFormsMap(
        id: json["id"],
        act: json["act"],
        formData: json["form_data"] == null
            ? null
            : json["form_data"].toString() == "[]"
                ? null
                : KycFormData.fromJson(json["form_data"]),
        createdAt: json["created_at"],
        updatedAt: json["updated_at"],
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "act": act,
        "form_data": formData?.toJson(),
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
