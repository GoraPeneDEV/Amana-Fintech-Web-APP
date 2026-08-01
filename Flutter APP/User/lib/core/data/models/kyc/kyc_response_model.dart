import 'dart:io';
import 'package:flutter/material.dart';

import '../../../utils/util_exporter.dart';

class KycResponseModel {
  KycResponseModel({
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

  KycResponseModel.fromJson(dynamic json) {
    _remark = json['remark'];
    _status = json['status'] != null ? json['status'].toString() : '';
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
}

class Data {
  Data({
    KycFormData? form,
    List<KycPendingData>? kycPendingData,
    String? path,
  }) {
    _form = form;
    _kycPendingData = kycPendingData;
    _path = path;
  }

  Data.fromJson(dynamic json) {
    _form = json['form'] != null ? KycFormData.fromJson(json['form']) : null;
    _kycPendingData = json['kyc_data'] == null
        ? []
        : List<KycPendingData>.from(
            json["kyc_data"]!.map((x) => KycPendingData.fromJson(x)),
          );
    _path = json["path"]?.toString();
  }

  KycFormData? _form;
  String? _path;
  List<KycPendingData>? _kycPendingData;
  KycFormData? get form => _form;
  List<KycPendingData>? get kycPendingData => _kycPendingData;
  String? get path => _path;
}

class KycFormData {
  /// Public list of KycFormModel
  List<KycFormModel>? list;

  KycFormData({this.list});

  KycFormData.fromJson(dynamic json) {
    var map = Map.from(json).map((key, value) => MapEntry(key, value));
    try {
      List<KycFormModel>? parsedList = map.entries
          .map(
            (e) => KycFormModel(
              e.value['name'],
              e.value['label'],
              e.value['is_required'],
              e.value['instruction'],
              e.value['extensions'],
              (e.value['options'] as List).map((e) => e as String).toList(),
              e.value['type'],
              '',
            ),
          )
          .toList();

      if (parsedList.isNotEmpty) {
        parsedList.removeWhere((element) => element.toString().isEmpty);
        list = parsedList;
      }
    } catch (e) {
      printX(e.toString());
    }
  }

  /// Converts the KycFormData object to JSON
  Map<String, dynamic> toJson() {
    return list != null
        ? Map.fromEntries(
            list!.map((item) => MapEntry(item.label ?? "", item.toJson())),
          )
        : {};
  }
}

class KycFormModel {
  String? name;
  String? label;
  String? isRequired;
  String? instruction;
  String? extensions;
  List<String>? options;
  String? type;
  dynamic selectedValue;
  TextEditingController? textEditingController;
  File? imageFile;
  List<String>? cbSelected;

  /// Constructor
  KycFormModel(
    this.name,
    this.label,
    this.isRequired,
    this.instruction,
    this.extensions,
    this.options,
    this.type,
    this.selectedValue, {
    this.cbSelected,
    this.imageFile,
    this.textEditingController,
  }) {
    // Initialize textEditingController if not provided
    textEditingController ??= TextEditingController();
  }

  /// Converts the model to JSON
  Map<String, dynamic> toJson() {
    return {
      'name': name,
      'label': label,
      'is_required': isRequired,
      'instruction': instruction,
      'extensions': extensions,
      'options': options,
      'type': type,
      'selected_value': selectedValue,
      'cb_selected': cbSelected,
      'image_file': imageFile?.path, // Save the file path
    };
  }

  /// Factory constructor to create an instance from JSON
  factory KycFormModel.fromJson(Map<String, dynamic> json) {
    return KycFormModel(
      json['name'],
      json['label'],
      json['is_required'],
      json['instruction'],
      json['extensions'],
      (json['options'] as List<dynamic>?)?.map((e) => e as String).toList(),
      json['type'],
      json['selected_value'],
      cbSelected: (json['cb_selected'] as List<dynamic>?)?.map((e) => e as String).toList(),
      imageFile: json['image_file'] != null ? File(json['image_file']) : null,
      textEditingController: TextEditingController(), // Initialize a controller
    );
  }

  /// copyWith method
  KycFormModel copyWith({
    String? name,
    String? label,
    String? isRequired,
    String? instruction,
    String? extensions,
    List<String>? options,
    String? type,
    dynamic selectedValue,
    TextEditingController? textEditingController,
    File? imageFile,
    List<String>? cbSelected,
  }) {
    return KycFormModel(
      name ?? this.name,
      label ?? this.label,
      isRequired ?? this.isRequired,
      instruction ?? this.instruction,
      extensions ?? this.extensions,
      options ?? this.options,
      type ?? this.type,
      selectedValue ?? this.selectedValue,
      cbSelected: cbSelected ?? this.cbSelected,
      imageFile: imageFile ?? this.imageFile,
      textEditingController: textEditingController ?? this.textEditingController,
    );
  }
}

class KycPendingData {
  String? name;
  String? type;
  String? value;

  KycPendingData({this.name, this.type, this.value});

  factory KycPendingData.fromJson(Map<String, dynamic> json) => KycPendingData(
        name: json["name"],
        type: json["type"],
        value: json["value"] != null ? json["value"].toString() : '---',
      );

  Map<String, dynamic> toJson() => {"name": name, "type": type, "value": value};
}
