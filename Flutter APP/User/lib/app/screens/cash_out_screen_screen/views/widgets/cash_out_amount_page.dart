import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_contact_list_tile_card.dart';
import 'package:ovopay/app/components/chip/custom_chip.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/cash_out_screen_screen/controller/cash_out_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../../../core/utils/util_exporter.dart';

class CashOutAmountPage extends StatefulWidget {
  const CashOutAmountPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<CashOutAmountPage> createState() => _CashOutAmountPageState();
}

class _CashOutAmountPageState extends State<CashOutAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<CashOutController>(
      builder: (cashOutController) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      CustomContactListTileCard(
                        leading: MyNetworkImageWidget(
                          width: Dimensions.space40.w,
                          height: Dimensions.space40.w,
                          isProfile: true,
                          imageUrl: '${cashOutController.existAgentModel?.getAgentImageUrl()}',
                          imageAlt: '${cashOutController.existAgentModel?.getFullName()}',
                        ),
                        padding: EdgeInsets.zero,
                        imagePath: cashOutController.existAgentModel?.getAgentImageUrl(),
                        title: cashOutController.existAgentModel?.getFullName(),
                        subtitle: "+${cashOutController.existAgentModel?.getUserMobileNo(withCountryCode: true)}",
                        showBorder: false,
                      ),
                    ],
                  ),
                ),
                spaceDown(Dimensions.space16),
                CustomAppCard(
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
                        controller: cashOutController.amountController,
                        showLabelText: false,
                        labelText: MyStrings.enterAmount.tr,
                        hintText: "${AppConverter.formatNumberDouble(cashOutController.globalChargeModel?.minLimit ?? "0", precision: 0)}-${AppConverter.formatNumberDouble(cashOutController.globalChargeModel?.maxLimit ?? "0", precision: 0)}",
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
                          cashOutController.onChangeAmountControllerText(value);
                        },
                        validator: (value) {
                          return MyUtils().validateAmountForm(
                            value: value ?? '0',
                            userCurrentBalance: cashOutController.userCurrentBalance,
                            minLimit: AppConverter.formatNumberDouble(
                              cashOutController.globalChargeModel?.minLimit ?? "0",
                            ),
                            maxLimit: AppConverter.formatNumberDouble(
                              cashOutController.globalChargeModel?.maxLimit ?? "0",
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
                                cashOutController.userCurrentBalance.toString(),
                              ),
                              style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getPrimaryColor()),
                            ),
                          ],
                        ),
                      ),
                      spaceDown(Dimensions.space24),
                      SingleChildScrollView(
                        clipBehavior: Clip.hardEdge,
                        scrollDirection: Axis.horizontal, // Allows horizontal scrolling
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.center,
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: SharedPreferenceService.getQuickMoneyList().map((
                            value,
                          ) {
                            return CustomAppChip(
                              isSelected: value == cashOutController.getAmount ? true : false,
                              text: value,
                              onTap: () {
                                cashOutController.onChangeAmountControllerText(value);
                              },
                            );
                          }).toList(),
                        ),
                      ),
                    ],
                  ),
                ),
                if (cashOutController.otpType.isNotEmpty) ...[
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
                          children: cashOutController.otpType.map((value) {
                            return CustomAppChip(
                              backgroundColor: MyColor.getWhiteColor(),
                              isSelected: value == cashOutController.selectedOtpType,
                              text: cashOutController.getOtpType(value),
                              onTap: () => cashOutController.selectAnOtpType(
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
                  isLoading: cashOutController.isSubmitLoading,
                  isActive: cashOutController.amountController.text.trim().isNotEmpty,
                  text: MyStrings.next,
                  onTap: () {
                    if (cashOutController.selectedOtpType == "") {
                      if (cashOutController.otpType.isNotEmpty) {
                        CustomSnackBar.error(
                          errorList: [MyStrings.pleaseSelectAnOtpType.tr],
                        );
                        return;
                      }
                    }
                    if (formKey.currentState?.validate() ?? false) {
                      // widget.onSuccessCallback();
                      cashOutController.submitThisProcess(
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
                            actionRemark: cashOutController.actionRemark,
                            otpType: cashOutController.selectedOtpType,
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
