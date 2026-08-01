import 'package:ovopay/core/helper/string_format_helper.dart';

class DepositMethodResponseModel {
  DepositMethodResponseModel({
    String? remark,
    List<String>? message,
    Data? data,
  }) {
    _remark = remark;
    _message = message;
    _data = data;
  }

  DepositMethodResponseModel.fromJson(dynamic json) {
    _remark = json['remark'];
    _message = json['message'] != null ? (json['message'] as List<dynamic>).toStringList() : [];
    _data = json['data'] != null ? Data.fromJson(json['data']) : null;
  }

  String? _remark;
  List<String>? _message;
  Data? _data;

  String? get remark => _remark;
  List<String>? get message => _message;
  Data? get data => _data;

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map['remark'] = _remark;
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
  Data({List<DepositMethods>? methods, String? imagePath}) {
    _methods = methods;
    _imagePath = imagePath;
  }

  Data.fromJson(dynamic json) {
    if (json['methods'] != null) {
      _methods = [];
      json['methods'].forEach((v) {
        _methods?.add(DepositMethods.fromJson(v));
      });
    }
    _imagePath = json['image_path'].toString();
  }

  List<DepositMethods>? _methods;
  List<DepositMethods>? get methods => _methods;

  String? _imagePath;
  String? get imagePath => _imagePath;

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    if (_methods != null) {
      map['methods'] = _methods?.map((v) => v.toJson()).toList();
    }
    map['image_path'] = _imagePath.toString();
    return map;
  }
}

class DepositMethods {
  DepositMethods({
    int? id,
    String? name,
    String? currency,
    String? symbol,
    String? methodCode,
    String? gatewayAlias,
    String? minAmount,
    String? maxAmount,
    String? percentCharge,
    String? fixedCharge,
    String? rate,
    dynamic image,
    String? gatewayParameter,
    String? createdAt,
    String? updatedAt,
    Method? method,
  }) {
    _id = id;
    _name = name;
    _currency = currency;
    _symbol = symbol;
    _methodCode = methodCode;
    _gatewayAlias = gatewayAlias;
    _minAmount = minAmount;
    _maxAmount = maxAmount;
    _percentCharge = percentCharge;
    _fixedCharge = fixedCharge;
    _rate = rate;
    _image = image;
    _gatewayParameter = gatewayParameter;
    _createdAt = createdAt;
    _updatedAt = updatedAt;
    _method = method;
  }

  DepositMethods.fromJson(dynamic json) {
    _id = json['id'];
    _name = json['name'] != null ? json['name'].toString() : '';
    _currency = json['currency'] != null ? json['currency'].toString() : '';
    _symbol = json['symbol'] != null ? json['symbol'].toString() : '';
    _methodCode = json['method_code'].toString();
    _gatewayAlias = json['gateway_alias'].toString();
    _minAmount = json['min_amount'].toString();
    _maxAmount = json['max_amount'].toString();
    _percentCharge = json['percent_charge'].toString();
    _fixedCharge = json['fixed_charge'].toString();
    _rate = json['rate'].toString();
    _image = json['image'].toString();
    _gatewayParameter = json['gateway_parameter'].toString();
    _createdAt = json['created_at'];
    _updatedAt = json['updated_at'];
    _method = json['method'] != null ? Method.fromJson(json['method']) : null;
  }

  int? _id;
  String? _name;
  String? _currency;
  String? _symbol;
  String? _methodCode;
  String? _gatewayAlias;
  String? _minAmount;
  String? _maxAmount;
  String? _percentCharge;
  String? _fixedCharge;
  String? _rate;
  dynamic _image;
  String? _gatewayParameter;
  String? _createdAt;
  String? _updatedAt;
  Method? _method;

  int? get id => _id;
  String? get name => _name;
  String? get currency => _currency;
  String? get symbol => _symbol;
  String? get methodCode => _methodCode;
  String? get gatewayAlias => _gatewayAlias;
  String? get minAmount => _minAmount;
  String? get maxAmount => _maxAmount;
  String? get percentCharge => _percentCharge;
  String? get fixedCharge => _fixedCharge;
  String? get rate => _rate;
  dynamic get image => _image;
  String? get gatewayParameter => _gatewayParameter;
  String? get createdAt => _createdAt;
  String? get updatedAt => _updatedAt;
  Method? get method => _method;

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map['id'] = _id;
    map['name'] = _name;
    map['currency'] = _currency;
    map['symbol'] = _symbol;
    map['method_code'] = _methodCode;
    map['gateway_alias'] = _gatewayAlias;
    map['min_amount'] = _minAmount;
    map['max_amount'] = _maxAmount;
    map['percent_charge'] = _percentCharge;
    map['fixed_charge'] = _fixedCharge;
    map['rate'] = _rate;
    map['image'] = _image;
    map['gateway_parameter'] = _gatewayParameter;
    map['created_at'] = _createdAt;
    map['updated_at'] = _updatedAt;
    if (_method != null) {
      map['method'] = _method?.toJson();
    }
    return map;
  }
}

class Method {
  Method({
    int? id,
    String? formId,
    String? code,
    String? name,
    String? alias,
    String? gatewayParameters,
    String? image,
  }) {
    _id = id;
    _formId = formId;
    _code = code;
    _name = name;
    _alias = alias;
    _image = image;
    _gatewayParameters = gatewayParameters;
  }

  Method.fromJson(dynamic json) {
    _id = json['id'];
    _formId = json['form_id'].toString();
    _code = json['code'].toString();
    _name = json['name'] != null ? json['name'].toString() : '';
    _alias = json['alias'] != null ? json['alias'].toString() : '';
    _image = json['image'] != null ? json['image'].toString() : '';
    _gatewayParameters = json['gateway_parameters'].toString();
  }
  int? _id;
  String? _formId;
  String? _code;
  String? _name;
  String? _alias;
  String? _image;
  String? _gatewayParameters;

  int? get id => _id;
  String? get formId => _formId;
  String? get code => _code;
  String? get name => _name;
  String? get alias => _alias;
  String? get image => _image;
  String? get gatewayParameters => _gatewayParameters;

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map['id'] = _id;
    map['form_id'] = _formId;
    map['code'] = _code;
    map['name'] = _name;
    map['alias'] = _alias;
    map['image'] = _image;
    map['gateway_parameters'] = _gatewayParameters;
    return map;
  }
}
