import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/auth/kyc/controller/kyc_controller.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';

import '../../../../../../../core/utils/util_exporter.dart';

class KycFileSelectItem extends StatefulWidget {
  final int index;

  const KycFileSelectItem({super.key, required this.index});

  @override
  State<KycFileSelectItem> createState() => _KycFileSelectItemState();
}

class _KycFileSelectItemState extends State<KycFileSelectItem> {
  @override
  Widget build(BuildContext context) {
    return GetBuilder<KycController>(
      builder: (controller) {
        KycFormModel? model = controller.formList[widget.index];

        return Column(
          children: [
            RoundedTextField(
              isRequired: model.isRequired == 'optional' ? false : true,
              instructions: model.instruction,
              contentPadding: EdgeInsets.symmetric(
                vertical: Dimensions.space30,
              ),
              prefixIcon: CustomAppCard(
                showBorder: false,
                margin: EdgeInsetsDirectional.only(
                  start: Dimensions.space10,
                  end: Dimensions.space10,
                ),
                padding: EdgeInsetsDirectional.zero,
                width: 50,
                height: 40,
                radius: Dimensions.largeRadius.r,
                backgroundColor: MyColor.getPrimaryColor(),
                child: Icon(
                  controller.isImageFile(
                    extensions: model.extensions?.split(',') ?? [],
                  )
                      ? Icons.camera_alt_outlined
                      : Icons.file_present_outlined,
                  color: MyColor.getWhiteColor(),
                ),
              ),
              onTap: () {
                controller.pickFile(
                  widget.index,
                  extention: model.extensions?.split(','),
                );
              },
              controller: TextEditingController(
                text: model.selectedValue.toString().trim(),
              ),
              readOnly: true,
              showLabelText: true,
              labelText: (model.name ?? '').tr,
              hintText: model.selectedValue.toString().trim() == ""
                  ? controller.isImageFile(
                      extensions: model.extensions?.split(',') ?? [],
                    )
                      ? "${MyStrings.chooseAImage.tr} (${model.extensions ?? ""})"
                      : "${MyStrings.chooseAFile.tr} (${model.extensions ?? ""})"
                  : model.selectedValue.toString(),
              textInputAction: TextInputAction.done,
              keyboardType: TextInputType.text,
              validator: (value) {
                if (model.isRequired != 'optional' && value.toString().isEmpty) {
                  return '${model.name.toString().capitalizeFirst} ${MyStrings.isRequired.tr}';
                } else {
                  return null;
                }
              },
            ),
          ],
        );
      },
    );
  }
}
