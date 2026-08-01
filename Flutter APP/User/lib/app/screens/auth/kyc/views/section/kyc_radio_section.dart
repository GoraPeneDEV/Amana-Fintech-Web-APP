import 'package:flutter/material.dart';

import 'package:ovopay/app/components/checkbox_and_radio/custom_radio_button.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;

class KycRadioSection extends StatelessWidget {
  final kyc.KycFormModel model;
  final Function onChanged;
  final int selectedIndex;
  const KycRadioSection({
    super.key,
    required this.model,
    required this.onChanged,
    required this.selectedIndex,
  });

  @override
  Widget build(BuildContext context) {
    return CustomRadioButton(
      title: model.name,
      instruction: model.instruction,
      isRequired: model.isRequired == 'optional' ? false : true,
      selectedIndex: selectedIndex,
      list: model.options ?? [],
      onChanged: (index) => onChanged(index),
    );
  }
}
