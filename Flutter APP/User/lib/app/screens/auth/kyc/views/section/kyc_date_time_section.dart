import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;

import '../../../../../../core/utils/util_exporter.dart';

class KycDateTimeSection extends StatelessWidget {
  final kyc.KycFormModel model;
  final Function onChanged;
  final Function onTap;
  final TextEditingController? controller;
  const KycDateTimeSection({
    super.key,
    required this.model,
    required this.onTap,
    required this.onChanged,
    this.controller,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        RoundedTextField(
          instructions: model.instruction,
          isRequired: model.isRequired == 'optional' ? false : true,
          hintText: (model.name ?? '').tr.toString().capitalizeFirst ?? "",
          labelText: (model.name ?? '').tr.toString().capitalize ?? "",
          controller: controller,
          keyboardType: TextInputType.datetime,
          readOnly: true,
          validator: (value) {
            if (model.isRequired != 'optional' && value.toString().isEmpty) {
              return '${model.name.toString().capitalizeFirst} ${MyStrings.isRequired.tr}';
            } else {
              return null;
            }
          },
          onTap: () {
            onTap();
          },
          onChanged: (value) => onChanged(value),
        ),
        const SizedBox(height: Dimensions.space10),
      ],
    );
  }
}
