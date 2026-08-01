import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/helper/string_format_helper.dart';

class AuthorizationResponseModel {
  AuthorizationResponseModel({
    String? remark,
    String? status,
    List<String>? message,
    Data? data,
  }) {
    _remark = remark;
    _status = status;
    _message = message;
    _data = data;
  }

  AuthorizationResponseModel.fromJson(dynamic json) {
    _remark = json['remark'];
    _status = json['status'];
    _message = json['message'] != null ? (json['message'] as List<dynamic>).toStringList() : [];
    _data = json['data'] != null ? Data.fromJson(json['data']) : null;
  }

  String? _remark;
  String? _status;
  List<String>? _message;
  Data? _data;

  String? get remark => _remark;
  String? get status => _status;
  List<String>? get message => _message;
  Data? get data => _data;

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map['remark'] = _remark;
    map['status'] = _status;
    if (_message != null) {
      map['message'] = _message;
    }
    if (_data != null) {
      map['data'] = _data?.toJson();
    }
    return map;
  }
}

class Data {
  Data({this.actionId, this.user, this.nextStep, this.merchant, this.agent});

  Data.fromJson(dynamic json) {
    actionId = json['action_id'] != null ? json['action_id'].toString() : '';
    user = json['user'] != null ? UserModel.fromJson(json['user']) : null;
    agent = json['agent'] != null ? UserModel.fromJson(json['agent']) : null;
    merchant = json['merchant'] != null ? UserModel.fromJson(json['merchant']) : null;
    nextStep = json["next_step"]?.toString();
  }

  String? actionId; // Public field for actionId
  UserModel? user; // Public field for user
  UserModel? merchant; // Public field for merchant
  UserModel? agent; // Public field for agent
  String? nextStep; // Public field for nextStep

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map['action_id'] = actionId;
    if (user != null) {
      map['user'] = user?.toJson();
    }
    if (agent != null) {
      map['agent'] = agent?.toJson();
    }
    if (merchant != null) {
      map['merchant'] = merchant?.toJson();
    }
    map['next_step'] = nextStep; // Public field 'nextStep' converted to map
    return map;
  }
}
