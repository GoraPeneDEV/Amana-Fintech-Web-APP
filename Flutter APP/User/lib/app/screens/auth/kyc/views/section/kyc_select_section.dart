import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/drop_down/my_drop_down_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;

import '../../../../../../core/utils/util_exporter.dart';

class KycSelectSection extends StatelessWidget {
  final kyc.KycFormModel model;
  final Function(String) onChanged;
  const KycSelectSection({
    super.key,
    required this.model,
    required this.onChanged,
  });

  @override
  Widget build(BuildContext context) {
    bool isRequired = model.isRequired == 'optional' ? false : true;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        AppDropdownWidget(
          items: model.options ?? [],
          onItemSelected: (value) => onChanged(value),
          selectedItem: model.selectedValue,
          child: RoundedTextField(
            isRequired: model.isRequired == 'optional' ? false : true,
            readOnly: true,
            labelText: (model.name ?? '').tr,
            hintText: MyStrings.selectOne.tr,
            controller: TextEditingController(text: "${model.selectedValue}"),
            instructions: model.instruction,
            textInputAction: TextInputAction.next,
            keyboardType: TextInputType.text,
            onTap: () {},
            suffixIcon: Icon(
              Icons.keyboard_arrow_down_rounded,
              color: MyColor.getDarkColor(),
            ),
            validator: (value) {
              if (isRequired && value.toString().trim().isEmpty) {
                return '${model.name.toString().capitalizeFirst} ${MyStrings.isRequired.tr}';
              } else {
                return null;
              }
            },
          ),
        ),
        const SizedBox(height: Dimensions.space10),
      ],
    );
  }
}
