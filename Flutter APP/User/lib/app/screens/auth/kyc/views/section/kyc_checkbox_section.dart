import 'package:flutter/material.dart';
import 'package:ovopay/app/components/checkbox_and_radio/custom_check_box.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;

class KycCheckBoxSection extends StatelessWidget {
  final kyc.KycFormModel model;
  final Function onChanged;
  final List<String>? selectedValue;
  const KycCheckBoxSection({
    super.key,
    required this.model,
    required this.onChanged,
    required this.selectedValue,
  });

  @override
  Widget build(BuildContext context) {
    return CustomCheckBox(
      title: model.name,
      instruction: model.instruction,
      isRequired: model.isRequired == 'optional' ? false : true,
      selectedValue: selectedValue,
      list: model.options ?? [],
      onChanged: (value) => onChanged(value),
    );
  }
}
