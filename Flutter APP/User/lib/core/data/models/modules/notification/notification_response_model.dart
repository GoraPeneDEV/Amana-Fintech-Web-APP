// To parse this JSON data, do
//
//     final notificationResponseModel = notificationResponseModelFromJson(jsonString);

import 'dart:convert';

NotificationResponseModel notificationResponseModelFromJson(String str) => NotificationResponseModel.fromJson(json.decode(str));

String notificationResponseModelToJson(NotificationResponseModel data) => json.encode(data.toJson());

class NotificationResponseModel {
  String? remark;
  String? status;
  List<String>? message;
  Data? data;

  NotificationResponseModel({
    this.remark,
    this.status,
    this.message,
    this.data,
  });

  factory NotificationResponseModel.fromJson(Map<String, dynamic> json) => NotificationResponseModel(
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
  Notifications? notifications;

  Data({this.notifications});

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        notifications: json["notifications"] == null ? null : Notifications.fromJson(json["notifications"]),
      );

  Map<String, dynamic> toJson() => {"notifications": notifications?.toJson()};
}

class Notifications {
  List<NotificationsDataModel>? data;

  String? nextPageUrl;
  String? path;

  Notifications({this.data, this.nextPageUrl, this.path});

  factory Notifications.fromJson(Map<String, dynamic> json) => Notifications(
        data: json["data"] == null
            ? []
            : List<NotificationsDataModel>.from(
                json["data"]!.map((x) => NotificationsDataModel.fromJson(x)),
              ),
        nextPageUrl: json["next_page_url"]?.toString(),
        path: json["path"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
        "next_page_url": nextPageUrl,
        "path": path,
      };
}

class NotificationsDataModel {
  int? id;
  String? userId;
  String? agentId;
  String? merchantId;
  String? adminId;
  String? sender;
  String? sentFrom;
  String? sentTo;
  String? subject;
  String? message;
  String? notificationType;
  String? image;
  String? userRead;
  String? createdAt;
  String? updatedAt;

  NotificationsDataModel({
    this.id,
    this.userId,
    this.agentId,
    this.merchantId,
    this.adminId,
    this.sender,
    this.sentFrom,
    this.sentTo,
    this.subject,
    this.message,
    this.notificationType,
    this.image,
    this.userRead,
    this.createdAt,
    this.updatedAt,
  });

  factory NotificationsDataModel.fromJson(Map<String, dynamic> json) => NotificationsDataModel(
        id: json["id"],
        userId: json["user_id"]?.toString(),
        agentId: json["agent_id"]?.toString(),
        merchantId: json["merchant_id"]?.toString(),
        adminId: json["admin_id"]?.toString(),
        sender: json["sender"]?.toString(),
        sentFrom: json["sent_from"]?.toString(),
        sentTo: json["sent_to"]?.toString(),
        subject: json["subject"]?.toString(),
        message: json["message"]?.toString(),
        notificationType: json["notification_type"]?.toString(),
        image: json["image"]?.toString(),
        userRead: json["user_read"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        updatedAt: json["updated_at"]?.toString(),
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "user_id": userId,
        "agent_id": agentId,
        "merchant_id": merchantId,
        "admin_id": adminId,
        "sender": sender,
        "sent_from": sentFrom,
        "sent_to": sentTo,
        "subject": subject,
        "message": message,
        "notification_type": notificationType,
        "image": image,
        "user_read": userRead,
        "created_at": createdAt,
        "updated_at": updatedAt,
      };
}
