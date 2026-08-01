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
import 'package:ovopay/app/screens/bank_transfer_screen/controller/bank_transfer_controller.dart';
import 'package:ovopay/app/screens/global/controller/global_dynamic_form_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class BankTransferAmountPage extends StatefulWidget {
  const BankTransferAmountPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<BankTransferAmountPage> createState() => _BankTransferAmountPageState();
}

class _BankTransferAmountPageState extends State<BankTransferAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<BankTransferController>(
      builder: (controller) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                if (controller.selectedMyAccount != null) ...[
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CustomContactListTileCard(
                          padding: EdgeInsets.zero,
                          leading: CustomAppCard(
                            width: Dimensions.space45.w,
                            height: Dimensions.space45.w,
                            radius: Dimensions.largeRadius.r,
                            padding: EdgeInsetsDirectional.all(
                              Dimensions.space4.w,
                            ),
                            child: MyNetworkImageWidget(
                              boxFit: BoxFit.scaleDown,
                              imageUrl: controller.selectedMyAccount?.bank?.getBankImageUrl() ?? "",
                              isProfile: false,
                              radius: Dimensions.largeRadius.r,
                              width: Dimensions.space40.w,
                              height: Dimensions.space40.h,
                            ),
                          ),
                          subtitleStyle: MyTextStyle.sectionSubTitle1.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                          imagePath: "",
                          title: controller.selectedMyAccount?.accountHolder ?? "",
                          subtitle: (controller.selectedMyAccount?.accountNumber ?? "").toNumberMask(
                            unmaskedPrefix: 2,
                            unmaskedSuffix: 3,
                            maskChar: "•",
                          ),
                          showBorder: false,
                        ),
                      ],
                    ),
                  ),
                ] else ...[
                  if (controller.selectedBank != null) ...[
                    CustomAppCard(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          CustomContactListTileCard(
                            padding: EdgeInsets.zero,
                            leading: CustomAppCard(
                              width: Dimensions.space45.w,
                              height: Dimensions.space45.w,
                              radius: Dimensions.largeRadius.r,
                              padding: EdgeInsetsDirectional.all(
                                Dimensions.space4.w,
                              ),
                              child: MyNetworkImageWidget(
                                boxFit: BoxFit.scaleDown,
                                imageUrl: controller.selectedBank?.getBankImageUrl() ?? "",
                                isProfile: false,
                                radius: Dimensions.largeRadius.r,
                                width: Dimensions.space40.w,
                                height: Dimensions.space40.h,
                              ),
                            ),
                            subtitleStyle: MyTextStyle.sectionSubTitle1.copyWith(
                              color: MyColor.getBodyTextColor(),
                            ),
                            imagePath: "",
                            title: controller.bankAccountNameController.text,
                            subtitle: controller.bankAccountNumberController.text,
                            showBorder: false,
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
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
                        controller: controller.amountController,
                        showLabelText: false,
                        labelText: MyStrings.enterAmount.tr,
                        hintText: "${AppConverter.formatNumberDouble(controller.globalChargeModel?.minLimit ?? "0", precision: 0)}-${AppConverter.formatNumberDouble(controller.globalChargeModel?.maxLimit ?? "0", precision: 0)}",
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
                          controller.onChangeAmountControllerText(value);
                        },
                        validator: (value) {
                          return MyUtils().validateAmountForm(
                            value: value ?? '0',
                            userCurrentBalance: controller.userCurrentBalance,
                            minLimit: AppConverter.formatNumberDouble(
                              controller.globalChargeModel?.minLimit ?? "0",
                            ),
                            maxLimit: AppConverter.formatNumberDouble(
                              controller.globalChargeModel?.maxLimit ?? "0",
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
                                controller.userCurrentBalance.toString(),
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
                          children: MyUtils()
                              .quickMoneyStringList(
                            AppConverter.formatNumberDouble(
                              controller.globalChargeModel?.minLimit ?? "0",
                            ),
                            AppConverter.formatNumberDouble(
                              controller.globalChargeModel?.maxLimit ?? "0",
                            ),
                          )
                              .map((value) {
                            return CustomAppChip(
                              isSelected: value == controller.getAmount ? true : false,
                              text: value,
                              onTap: () {
                                controller.onChangeAmountControllerText(
                                  value,
                                );
                              },
                            );
                          }).toList(),
                        ),
                      ),
                    ],
                  ),
                ),
                if (controller.otpType.isNotEmpty) ...[
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
                          children: controller.otpType.map((value) {
                            return CustomAppChip(
                              backgroundColor: MyColor.getWhiteColor(),
                              isSelected: value == controller.selectedOtpType,
                              text: controller.getOtpType(value),
                              onTap: () => controller.selectAnOtpType(value),
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),
                ],
                spaceDown(Dimensions.space15),
                AppMainSubmitButton(
                  isLoading: controller.isSubmitLoading,
                  isActive: controller.amountController.text.trim().isNotEmpty,
                  text: MyStrings.next,
                  onTap: () {
                    if (controller.selectedOtpType == "") {
                      if (controller.otpType.isNotEmpty) {
                        CustomSnackBar.error(
                          errorList: [MyStrings.pleaseSelectAnOtpType.tr],
                        );
                        return;
                      }
                    }
                    if (formKey.currentState?.validate() ?? false) {
                      GlobalDynamicFormController dynamicFormController = Get.find();
                      controller.submitThisProcess(
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
                            actionRemark: controller.actionRemark,
                            otpType: controller.selectedOtpType,
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
