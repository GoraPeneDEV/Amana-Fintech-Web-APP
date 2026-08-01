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
import 'package:ovopay/app/components/text/small_text.dart';
import 'package:ovopay/app/packages/auto_height_grid_view/src/auto_height_grid_view.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/controller/airtime_recharge_controller.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/views/widgets/airtime_bottom_sheet.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../../../core/utils/util_exporter.dart';

class AirtimeRechargeAmountPage extends StatefulWidget {
  const AirtimeRechargeAmountPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<AirtimeRechargeAmountPage> createState() => _AirtimeRechargeAmountPageState();
}

class _AirtimeRechargeAmountPageState extends State<AirtimeRechargeAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<AirTimeRechargeController>(
      builder: (controller) {
        return Form(
          key: formKey,
          child: Column(
            children: [
              if (controller.selectedOperator?.bundle == "0" && (controller.selectedOperator?.fixedAmounts?.length ?? 0) == 0) ...[
                Expanded(
                  child: SingleChildScrollView(
                    child: Column(
                      children: [
                        if (controller.selectedOperator != null) ...[
                          CustomAppCard(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                CustomContactListTileCard(
                                  leading: MyNetworkImageWidget(
                                    imageUrl: controller.selectedOperator?.logoUrls?.first ?? "",
                                    width: Dimensions.space63.w,
                                    boxFit: BoxFit.contain,
                                    height: null,
                                  ),
                                  padding: EdgeInsets.zero,
                                  imagePath: controller.selectedOperator?.logoUrls?.first ?? "",
                                  title: "${controller.selectedOperator?.name}",
                                  subtitle: "${controller.selectedCountry?.callingCodes?.firstOrNull ?? ""}${controller.phoneNumberOrUserNameController.text}",
                                  showBorder: false,
                                ),
                              ],
                            ),
                          ),
                          spaceDown(Dimensions.space16),
                        ],
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
                                hintText: controller.selectedOperator?.minAmount?.isEmptyString == true || controller.selectedOperator?.maxAmount?.isEmptyString == true ? "0.00" : "${AppConverter.formatNumberDouble(controller.selectedOperator?.minAmount ?? "0")}-${AppConverter.formatNumberDouble(controller.selectedOperator?.maxAmount ?? "0")}",
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
                                  controller.onChangeAmountControllerText(
                                    value,
                                  );
                                },
                                validator: (value) {
                                  if (controller.selectedOperator?.minAmount?.isEmptyString == true || controller.selectedOperator?.maxAmount?.isEmptyString == true) {
                                    return null;
                                  }
                                  return MyUtils().validateAmountForm(
                                    value: value ?? '0',
                                    userCurrentBalance: controller.userCurrentBalance,
                                    minLimit: AppConverter.formatNumberDouble(
                                      controller.selectedOperator?.minAmount ?? "0",
                                    ),
                                    maxLimit: AppConverter.formatNumberDouble(
                                      controller.selectedOperator?.maxAmount ?? "0",
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
                                      style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(
                                        color: MyColor.getPrimaryColor(),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              if (controller.suggestedAmounts.isNotEmpty) ...[
                                spaceDown(Dimensions.space24),
                                SingleChildScrollView(
                                  clipBehavior: Clip.hardEdge,
                                  scrollDirection: Axis.horizontal, // Allows horizontal scrolling
                                  child: Row(
                                    crossAxisAlignment: CrossAxisAlignment.center,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: controller.suggestedAmounts.map((
                                      value,
                                    ) {
                                      return CustomAppChip(
                                        isSelected: value == controller.getAmount ? true : false,
                                        text: "${AppConverter.formatNumber(value.toString())} ${(controller.selectedOperator?.senderCurrencySymbol ?? SharedPreferenceService.getCurrencySymbol()).toLowerCase()}",
                                        onTap: () {
                                          controller.onChangeAmountControllerText(
                                            value,
                                          );
                                        },
                                      );
                                    }).toList(),
                                  ),
                                ),
                              ] else ...[
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
                                        controller.selectedOperator?.minAmount ?? "0",
                                        precision: 0,
                                      ),
                                      AppConverter.formatNumberDouble(
                                        controller.selectedOperator?.maxAmount ?? "0",
                                        precision: 0,
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
                                      onTap: () => controller.selectAnOtpType(
                                        value,
                                      ),
                                    );
                                  }).toList(),
                                ),
                              ],
                            ),
                          ),
                        ],
                        spaceDown(Dimensions.space24),
                        AppMainSubmitButton(
                          isLoading: controller.isSubmitLoading,
                          isActive: controller.amountController.text.trim().isNotEmpty,
                          text: MyStrings.next,
                          onTap: () {
                            if (controller.selectedOtpType == "") {
                              if (controller.otpType.isNotEmpty) {
                                CustomSnackBar.error(
                                  errorList: [
                                    MyStrings.pleaseSelectAnOtpType.tr,
                                  ],
                                );
                                return;
                              }
                            }
                            if (formKey.currentState?.validate() ?? false) {
                              controller.submitThisProcess(
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
                ),
              ],
              //Bundle
              if (controller.selectedOperator?.bundle == "1" || (controller.selectedOperator?.fixedAmounts?.length ?? 0) != 0) ...[
                if (controller.selectedOperator != null) ...[
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CustomContactListTileCard(
                          leading: MyNetworkImageWidget(
                            imageUrl: controller.selectedOperator?.logoUrls?.first ?? "",
                            width: Dimensions.space63.w,
                            boxFit: BoxFit.contain,
                            height: null,
                          ),
                          padding: EdgeInsets.zero,
                          imagePath: controller.selectedOperator?.logoUrls?.first ?? "",
                          title: "${controller.selectedOperator?.name}",
                          subtitle: "${controller.selectedCountry?.callingCodes?.firstOrNull ?? ""}${controller.phoneNumberOrUserNameController.text}",
                          showBorder: false,
                        ),
                      ],
                    ),
                  ),
                  spaceDown(Dimensions.space16),
                ],
                Expanded(
                  child: AutoHeightGridView(
                    physics: const BouncingScrollPhysics(),
                    shrinkWrap: true,
                    padding: const EdgeInsets.symmetric(horizontal: 0),
                    itemCount: controller.selectedOperator?.fixedAmounts?.length ?? 0,
                    mainAxisSpacing: Dimensions.space10,
                    crossAxisSpacing: Dimensions.space10,
                    builder: (context, index) {
                      var item = (controller.selectedOperator?.fixedAmounts != null && index >= 0 && index < controller.selectedOperator!.fixedAmounts!.length) ? controller.selectedOperator!.fixedAmounts![index] : null;
                      var itemDes = (controller.selectedOperator?.fixedAmountsDescriptions != null && index >= 0 && index < controller.selectedOperator!.fixedAmountsDescriptions!.length) ? controller.selectedOperator!.fixedAmountsDescriptions![index] : null;

                      return CustomAppCard(
                        onPressed: () {
                          controller.onSelectedAmountAndIndex(
                            index,
                            item ?? '0',
                          );
                          // widget.onSuccessCallback();
                        },
                        borderColor: controller.selectedAmountIndex == index ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.center,
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            HeaderText(
                              text: "${AppConverter.formatNumber(item.toString())} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                            ),
                            if (itemDes?.description != null) ...[
                              spaceDown(Dimensions.space10),
                              SmallText(
                                text: itemDes?.description ?? '',
                                textAlign: TextAlign.center,
                                maxLine: 100,
                                textStyle: MyTextStyle.sectionSubTitle1,
                              ),
                            ],
                          ],
                        ),
                      );
                    },
                  ),
                ),
                spaceDown(Dimensions.space15),
                AppMainSubmitButton(
                  isLoading: controller.isSubmitLoading,
                  isActive: controller.amountController.text.trim().isNotEmpty,
                  text: MyStrings.next,
                  onTap: () {
                    if (controller.otpType.isNotEmpty) {
                      AirTimeBottomSheet.otpBottomSheet(
                        context,
                        onSuccessCallback: () {
                          widget.onSuccessCallback();
                        },
                      );
                      // CustomSnackBar.error(errorList: [MyStrings.pleaseSelectAnOtpType.tr]);
                      return;
                    }
                    if (formKey.currentState?.validate() ?? false) {
                      controller.submitThisProcess(
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
            ],
          ),
        );
      },
    );
  }
}
