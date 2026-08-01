import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/chip/custom_chip.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/education_fee_screen/controller/education_fee_controller.dart';
import 'package:ovopay/app/screens/global/controller/global_dynamic_form_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class EducationFeeAmountPage extends StatefulWidget {
  const EducationFeeAmountPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<EducationFeeAmountPage> createState() => _EducationFeeAmountPageState();
}

class _EducationFeeAmountPageState extends State<EducationFeeAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<EducationFeeController>(
      builder: (educationController) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                if (educationController.selectedEducationInstitute != null) ...[
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CustomCompanyListTileCard(
                          padding: EdgeInsets.zero,
                          imagePath: "${educationController.selectedEducationInstitute?.getInstituteImageUrl()}",
                          title: "${educationController.selectedEducationInstitute?.name}",
                          subtitle: "${educationController.educationCategoryDataList.firstWhereOrNull((e) => e.id?.toString() == educationController.selectedEducationInstitute?.categoryId?.toString())?.name}",
                          trailingTitle: MyStrings.customerID.tr,
                          trailingSubtitle: "${educationController.selectedEducationInstitute?.id}",
                          showBorder: false,
                        ),
                      ],
                    ),
                  ),
                ],
                spaceDown(Dimensions.space16),
                CustomAppCard(
                  width: double.infinity,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      HeaderText(
                        text: MyStrings.enterAmount.tr,
                        textStyle: MyTextStyle.headerH3.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                      ),
                      spaceDown(Dimensions.space24),
                      RoundedTextField(
                        controller: educationController.amountController,
                        showLabelText: false,
                        labelText: MyStrings.enterAmount.tr,
                        hintText: "${AppConverter.formatNumberDouble(educationController.globalChargeModel?.minLimit ?? "0", precision: 0)}-${AppConverter.formatNumberDouble(educationController.globalChargeModel?.maxLimit ?? "0", precision: 0)}",
                        textInputAction: TextInputAction.done,
                        keyboardType: TextInputType.numberWithOptions(
                          decimal: true,
                        ),
                        textStyle: MyTextStyle.headerH3.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                        focusBorderColor: MyColor.getPrimaryColor(),
                        textInputFormatter: [
                          FilteringTextInputFormatter.allow(
                            RegExp(r'[0-9.]'),
                          ), // Allows digits and a decimal point
                          FilteringTextInputFormatter.deny(
                            RegExp(r'(\.\d{30,})'),
                          ), // Limits decimal places (optional, adjust as needed)
                        ],
                        onChanged: (value) {
                          educationController.onChangeAmountControllerText(
                            value,
                          );
                        },
                        validator: (value) {
                          return MyUtils().validateAmountForm(
                            value: value ?? '0',
                            userCurrentBalance: educationController.userCurrentBalance,
                            minLimit: AppConverter.formatNumberDouble(
                              educationController.globalChargeModel?.minLimit ?? "0",
                            ),
                            maxLimit: AppConverter.formatNumberDouble(
                              educationController.globalChargeModel?.maxLimit ?? "0",
                            ),
                          );
                        },
                      ),
                      spaceDown(Dimensions.space8),
                      Text.rich(
                        TextSpan(
                          children: [
                            TextSpan(
                              text: "${MyStrings.availableBalance.tr}: ",
                              style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                color: MyColor.getBodyTextColor(),
                              ),
                            ),
                            TextSpan(
                              text: MyUtils.getUserAmount(
                                educationController.userCurrentBalance.toString(),
                              ),
                              style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getPrimaryColor()),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                if (educationController.otpType.isNotEmpty) ...[
                  spaceDown(Dimensions.space16),
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        HeaderText(
                          text: MyStrings.verificationType,
                          textStyle: MyTextStyle.sectionTitle2.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        Row(
                          children: educationController.otpType.map((value) {
                            return CustomAppChip(
                              backgroundColor: MyColor.getWhiteColor(),
                              isSelected: value == educationController.selectedOtpType,
                              text: educationController.getOtpType(value),
                              onTap: () => educationController.selectAnOtpType(
                                value,
                              ),
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),
                ],
                spaceDown(Dimensions.space15),
                AppMainSubmitButton(
                  isLoading: educationController.isSubmitLoading,
                  isActive: educationController.amountController.text.trim().isNotEmpty,
                  text: MyStrings.next,
                  onTap: () {
                    if (educationController.selectedOtpType == "") {
                      if (educationController.otpType.isNotEmpty) {
                        CustomSnackBar.error(
                          errorList: [MyStrings.pleaseSelectAnOtpType.tr],
                        );
                        return;
                      }
                    }
                    if (formKey.currentState?.validate() ?? false) {
                      GlobalDynamicFormController dynamicFormController = Get.find();
                      educationController.submitThisProcess(
                        dynamicFormList: dynamicFormController.formList,
                        onSuccessCallback: (value) {
                          widget.onSuccessCallback();
                        },
                        onVerifyOtpCallback: (value) async {
                          await AppDialogs.verifyOtpPopUpWidget(
                            context,
                            onSuccess: (value) async {
                              Navigator.pop(context);
                              widget.onSuccessCallback();
                            },
                            title: '',
                            actionRemark: educationController.actionRemark,
                            otpType: educationController.selectedOtpType,
                          );
                          return;
                        },
                      );
                    }
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
