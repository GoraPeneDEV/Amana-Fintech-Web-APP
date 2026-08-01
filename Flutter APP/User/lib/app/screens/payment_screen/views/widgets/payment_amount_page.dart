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
import 'package:ovopay/app/screens/payment_screen/controller/payment_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../../../core/utils/util_exporter.dart';

class PaymentAmountPage extends StatefulWidget {
  const PaymentAmountPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<PaymentAmountPage> createState() => _PaymentAmountPageState();
}

class _PaymentAmountPageState extends State<PaymentAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<PaymentController>(
      builder: (paymentController) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                CustomAppCard(
                  child: CustomContactListTileCard(
                    leading: MyNetworkImageWidget(
                      width: Dimensions.space40.w,
                      height: Dimensions.space40.w,
                      isProfile: true,
                      imageUrl: paymentController.existMerchantModel?.getMerchantImageUrl() ?? '',
                      imageAlt: '${paymentController.existMerchantModel?.getFullName()}',
                    ),
                    padding: EdgeInsets.zero,
                    imagePath: paymentController.existMerchantModel?.getMerchantImageUrl(),
                    title: paymentController.existMerchantModel?.getFullName(),
                    subtitle: "+${paymentController.existMerchantModel?.getUserMobileNo(withCountryCode: true)}",
                    showBorder: false,
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
                        controller: paymentController.amountController,
                        showLabelText: false,
                        labelText: MyStrings.enterAmount.tr,
                        hintText: '',
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
                          paymentController.onChangeAmountControllerText(value);
                        },
                        validator: (value) {
                          if (value.toString().isEmpty) {
                            return MyStrings.kAmountNumberError.tr;
                          } else if (AppConverter.formatNumberDouble(
                                value ?? "0",
                              ) >
                              paymentController.userCurrentBalance) {
                            return MyStrings.kAmountHigherError.tr;
                          } else {
                            return null;
                          }
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
                                paymentController.userCurrentBalance.toString(),
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
                              isSelected: value == paymentController.getAmount ? true : false,
                              text: value,
                              onTap: () {
                                paymentController.onChangeAmountControllerText(value);
                              },
                            );
                          }).toList(),
                        ),
                      ),
                    ],
                  ),
                ),
                if (paymentController.otpType.isNotEmpty) ...[
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
                          children: paymentController.otpType.map((value) {
                            return CustomAppChip(
                              backgroundColor: MyColor.getWhiteColor(),
                              isSelected: value == paymentController.selectedOtpType,
                              text: paymentController.getOtpType(value),
                              onTap: () => paymentController.selectAnOtpType(
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
                  isLoading: paymentController.isSubmitLoading,
                  isActive: paymentController.amountController.text.trim().isNotEmpty,
                  text: MyStrings.next,
                  onTap: () {
                    if (paymentController.selectedOtpType == "") {
                      if (paymentController.otpType.isNotEmpty) {
                        CustomSnackBar.error(
                          errorList: [MyStrings.pleaseSelectAnOtpType.tr],
                        );
                        return;
                      }
                    }
                    if (formKey.currentState?.validate() ?? false) {
                      // widget.onSuccessCallback();
                      paymentController.submitThisProcess(
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
                            actionRemark: paymentController.actionRemark,
                            otpType: paymentController.selectedOtpType,
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
