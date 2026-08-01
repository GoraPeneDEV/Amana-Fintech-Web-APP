import 'dart:io';
import 'package:flutter/material.dart';

import '../../../../utils/util_exporter.dart';

class GlobalKYCForm {
  GlobalKYCForm({List<GlobalFormModel>? list}) {
    _list = list;
  }

  List<GlobalFormModel>? _list = [];
  List<GlobalFormModel>? get list => _list;

  GlobalKYCForm.fromJson(dynamic json) {
    var map = Map.from(json).map((key, value) => MapEntry(key, value));
    try {
      List<GlobalFormModel>? list = map.entries
          .map(
            (e) => GlobalFormModel(
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

      if (list.isNotEmpty) {
        list.removeWhere((element) => element.toString().isEmpty);
        _list?.addAll(list);
      }
      _list;
    } catch (e) {
      printX(e.toString());
    }
  }
}

class GlobalFormModel {
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
  // Added an optional parameter to initialize the textEditingController
  GlobalFormModel(
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
}
