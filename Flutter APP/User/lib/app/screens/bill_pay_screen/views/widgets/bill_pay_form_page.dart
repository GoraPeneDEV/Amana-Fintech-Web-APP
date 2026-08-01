import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/bill_pay_screen/controller/bill_pay_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class BillPayFormPage extends StatefulWidget {
  const BillPayFormPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });
  final VoidCallback onSuccessCallback;

  final BuildContext context;

  @override
  State<BillPayFormPage> createState() => _BillPayFormPageState();
}

class _BillPayFormPageState extends State<BillPayFormPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<BillPayController>(
      builder: (billPayController) {
        return SingleChildScrollView(
          child: Column(
            children: [
              if (billPayController.selectedUtilityCompany != null) ...[
                CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      CustomCompanyListTileCard(
                        padding: EdgeInsets.zero,
                        imagePath: "${billPayController.selectedUtilityCompany?.getCompanyImageUrl()}",
                        title: "${billPayController.selectedUtilityCompany?.name}",
                        subtitle: billPayController.utilityCategoryDataList
                                .firstWhereOrNull(
                                  (e) => e.id?.toString() == billPayController.selectedUtilityCompany?.categoryId?.toString(),
                                )
                                ?.formattedName ??
                            "",
                        trailingTitle: MyStrings.customerID.tr,
                        trailingSubtitle: "${billPayController.selectedUtilityCompany?.id}",
                        showBorder: false,
                      ),
                    ],
                  ),
                ),
              ],
              spaceDown(Dimensions.space16),
              CustomAppCard(
                width: double.infinity,
                child: Form(
                  key: formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      HeaderTextSmaller(
                        textAlign: TextAlign.center,
                        text: "${MyStrings.billingDetails.tr} ",
                      ),
                      spaceDown(Dimensions.space24),
                      RoundedTextField(
                        forceFillColor: false,
                        readOnly: billPayController.selectedSavedUtilityCompany != null,
                        isRequired: true,
                        controller: billPayController.uniqueIDController,
                        showLabelText: true,
                        labelText: MyStrings.uniqueIDTitle,
                        hintText: "",
                        textInputAction: TextInputAction.next,
                        keyboardType: TextInputType.text,
                        validator: (value) {
                          if (value.toString().isEmpty) {
                            return MyStrings.fieldErrorMsg.tr;
                          } else {
                            return null;
                          }
                        },
                      ),
                      spaceDown(Dimensions.space16),
                      RoundedTextField(
                        forceFillColor: false,
                        isRequired: true,
                        controller: billPayController.referenceController,
                        showLabelText: true,
                        labelText: MyStrings.reference,
                        hintText: "",
                        textInputAction: TextInputAction.done,
                        keyboardType: TextInputType.text,
                        validator: (value) {
                          if (value.toString().isEmpty) {
                            return MyStrings.referenceMsg.tr;
                          } else {
                            return null;
                          }
                        },
                      ),
                      spaceDown(Dimensions.space10),
                      Row(
                        children: [
                          Checkbox.adaptive(
                            value: billPayController.saveInformation,
                            onChanged: (v) => billPayController.toggleSaveInformation(),
                            activeColor: MyColor.getPrimaryColor(), // ✅ fill color when checked
                            checkColor: Colors.white, // optional
                            side: BorderSide(
                                color: billPayController.saveInformation
                                    ? MyColor.getPrimaryColor() // border when checked
                                    : MyColor.getBorderColor(), // border when unchecked
                                width: 1.8),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(Dimensions.defaultRadius)), // 👇 Removes extra space
                            visualDensity: VisualDensity.compact,
                            materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                          ),
                          spaceSide(Dimensions.space3),
                          Expanded(
                            child: GestureDetector(
                              onTap: () {
                                billPayController.toggleSaveInformation();
                              },
                              child: Text(
                                MyStrings.saveInformation.tr,
                                style: MyTextStyle.sectionSubTitle1.copyWith(
                                  color: MyColor.getBodyTextColor(),
                                ),
                              ),
                            ),
                          ),
                        ],
                      )
                    ],
                  ),
                ),
              ),
              spaceDown(Dimensions.space15),
              AppMainSubmitButton(
                text: MyStrings.continueText,
                onTap: () {
                  if (formKey.currentState?.validate() ?? false) {
                    widget.onSuccessCallback();
                  }
                },
              ),
            ],
          ),
        );
      },
    );
  }
}
