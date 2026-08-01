import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;

import '../../../../../../core/utils/util_exporter.dart';

class KycTextAnEmailSection extends StatelessWidget {
  final kyc.KycFormModel model;
  final Function(String)? onChanged;

  const KycTextAnEmailSection({
    super.key,
    required this.onChanged,
    required this.model,
  });

  @override
  Widget build(BuildContext context) {
    bool isRequired = model.isRequired == 'optional' ? false : true;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        RoundedTextField(
          controller: model.textEditingController,
          isRequired: model.isRequired == 'optional' ? false : true,
          labelText: (model.name ?? '').tr,
          hintText: '',
          textInputAction: model.type == "textarea" ? TextInputAction.newline : TextInputAction.next,
          keyboardType: model.type == "textarea" ? TextInputType.multiline : MyUtils.getInputTextFieldType(model.type ?? 'text'),
          instructions: model.instruction,
          maxLine: model.type == "textarea" ? 5 : 1,
          // textInputAction: TextInputAction.newline,
          // keyboardType: TextInputType.multiline,
          validator: (value) {
            if (isRequired && value.toString().trim().isEmpty) {
              return '${model.name.toString().capitalizeFirst} ${MyStrings.isRequired.tr}';
            } else {
              return null;
            }
          },
          onChanged: onChanged,
        ),
        const SizedBox(height: Dimensions.space10),
      ],
    );
  }
}
