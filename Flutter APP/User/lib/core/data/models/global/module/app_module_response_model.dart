// To parse this JSON data, do
//
//     final appModuleResponseModel = appModuleResponseModelFromJson(jsonString);

import 'dart:convert';

AppModuleResponseModel appModuleResponseModelFromJson(String str) => AppModuleResponseModel.fromJson(json.decode(str));

String appModuleResponseModelToJson(AppModuleResponseModel data) => json.encode(data.toJson());

class AppModuleResponseModel {
  String? remark;
  String? status;
  Data? data;

  AppModuleResponseModel({this.remark, this.status, this.data});

  factory AppModuleResponseModel.fromJson(Map<String, dynamic> json) => AppModuleResponseModel(
        remark: json["remark"],
        status: json["status"],
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "remark": remark,
        "status": status,
        "data": data?.toJson(),
      };
}

class Data {
  ModuleSetting? moduleSetting;

  Data({this.moduleSetting});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        moduleSetting: json["module_setting"] == null ? null : ModuleSetting.fromJson(json["module_setting"]),
      );

  Map<String, dynamic> toJson() => {"module_setting": moduleSetting?.toJson()};
}

class ModuleSetting {
  List<ModuleModel>? user;
  List<ModuleModel>? agent;
  List<ModuleModel>? merchant;

  ModuleSetting({this.user, this.agent, this.merchant});

  factory ModuleSetting.fromJson(Map<String, dynamic> json) => ModuleSetting(
        user: json["user"] == null
            ? []
            : List<ModuleModel>.from(
                json["user"]!.map((x) => ModuleModel.fromJson(x)),
              ),
        agent: json["agent"] == null
            ? []
            : List<ModuleModel>.from(
                json["agent"]!.map((x) => ModuleModel.fromJson(x)),
              ),
        merchant: json["merchant"] == null
            ? []
            : List<ModuleModel>.from(
                json["merchant"]!.map((x) => ModuleModel.fromJson(x)),
              ),
      );

  Map<String, dynamic> toJson() => {
        "user": user == null ? [] : List<dynamic>.from(user!.map((x) => x.toJson())),
        "agent": agent == null ? [] : List<dynamic>.from(agent!.map((x) => x.toJson())),
        "merchant": merchant == null ? [] : List<dynamic>.from(merchant!.map((x) => x.toJson())),
      };
}

class ModuleModel {
  int? id;
  String? userType;
  String? title;
  String? slug;
  String? descriptionDisabled;
  String? descriptionEnabled;
  String? icon;
  String? status;
  String? createdAt;
  String? updatedAt;

  ModuleModel({
    this.id,
    this.userType,
    this.title,
    this.slug,
    this.descriptionDisabled,
    this.descriptionEnabled,
    this.icon,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory ModuleModel.fromJson(Map<String, dynamic> json) => ModuleModel(
        id: json["id"],
        userType: json["user_type"]?.toString(),
        title: json["title"]?.toString(),
        slug: json["slug"]?.toString(),
        descriptionDisabled: json["description_disabled"]?.toString(),
        descriptionEnabled: json["description_enabled"]?.toString(),
        icon: json["icon"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_type": userType,
        "title": title,
        "slug": slug,
        "description_disabled": descriptionDisabled,
        "description_enabled": descriptionEnabled,
        "icon": icon,
        "status": status,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
