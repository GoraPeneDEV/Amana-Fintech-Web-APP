import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/chip/custom_chip.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import '../../../../../core/utils/util_exporter.dart';
import '../../controller/gift_controller.dart';

class GiftPurchasePage extends StatefulWidget {
  const GiftPurchasePage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<GiftPurchasePage> createState() => _GiftPurchasePageState();
}

class _GiftPurchasePageState extends State<GiftPurchasePage> {
  final formKey = GlobalKey<FormState>();

  @override
  Widget build(BuildContext context) {
    return GetBuilder<GiftController>(
      builder: (controller) {
        return GestureDetector(
          onTap: () {
            MyUtils.clearAllTypeFocusNodes();
          },
          child: SingleChildScrollView(
            padding: EdgeInsets.only(
              left: Dimensions.space16,
              right: Dimensions.space16,
              top: Dimensions.space16,
              bottom: MediaQuery.of(context).viewInsets.bottom + Dimensions.space16,
            ),
            child: Form(
              key: formKey,
              child: Column(
                children: [
                  CustomAppCard(
                    padding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    child: Row(
                      children: [
                        MyNetworkImageWidget(
                          width: Dimensions.space40.w,
                          height: Dimensions.space40.w,
                          isProfile: true,
                          boxFit: BoxFit.scaleDown,
                          imageUrl: controller.selectedGiftCard.logoUrls?.first ?? "",
                          imageAlt: controller.getPhoneNumber,
                        ),
                        spaceSide(Dimensions.space16),
                        Expanded(
                          child: Text(
                            controller.selectedGiftCard.productName ?? "",
                            style: MyTextStyle.sectionTitle3,
                          ),
                        )
                      ],
                    ),
                  ),
                  spaceDown(Dimensions.space16),
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        HeaderText(
                          text: MyStrings.to.tr,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        RoundedTextField(
                          contentPadding: EdgeInsets.symmetric(horizontal: Dimensions.space20, vertical: Dimensions.space18),
                          controller: controller.emailController,
                          showLabelText: false,
                          isRequired: true,
                          labelText: MyStrings.to.tr,
                          keyboardType: TextInputType.emailAddress,
                          hintText: MyStrings.enterRecipientEmail,
                          textInputAction: TextInputAction.next,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                          focusBorderColor: MyColor.getPrimaryColor(),
                          onChanged: (value) {},
                          validator: (value) {
                            if (value.toString().trim().isEmpty) {
                              return MyStrings.invalidEmailMsg.tr;
                            } else if (!GetUtils.isEmail(
                              value.toString().trim(),
                            )) {
                              return MyStrings.invalidEmailMsg.tr;
                            } else {
                              return null;
                            }
                          },
                        ),
                        spaceDown(Dimensions.space16),
                        HeaderText(
                          text: MyStrings.from.tr,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        RoundedTextField(
                          contentPadding: EdgeInsets.symmetric(horizontal: Dimensions.space20, vertical: Dimensions.space18),
                          controller: controller.nameController,
                          showLabelText: false,
                          isRequired: true,
                          labelText: MyStrings.from.tr,
                          keyboardType: TextInputType.text,
                          hintText: MyStrings.enterYourName,
                          textInputAction: TextInputAction.next,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                          focusBorderColor: MyColor.getPrimaryColor(),
                          onChanged: (value) {},
                          validator: (value) {
                            if (value != null && value.isEmpty) {
                              return MyStrings.emailIsRequired.tr;
                            }
                            return null;
                          },
                        ),
                        spaceDown(Dimensions.space16),
                        HeaderText(
                          text: MyStrings.enterAmount.tr,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        if (controller.selectedGiftCard.denominationType == AppStatus.range) ...[
                          RoundedTextField(
                            readOnly: controller.selectedGiftCard.denominationType == AppStatus.fixed,
                            contentPadding: EdgeInsets.symmetric(horizontal: Dimensions.space20, vertical: Dimensions.space18),
                            controller: controller.amountController,
                            showLabelText: false,
                            labelText: MyStrings.enterAmount.tr,
                            hintText: controller.selectedGiftCard.denominationType == AppStatus.range ? "${AppConverter.formatNumberDouble(controller.selectedGiftCard.minRecipientDenomination ?? "0")}-${AppConverter.formatNumberDouble(controller.selectedGiftCard.maxRecipientDenomination ?? "0")}" : "0.00",
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
                              controller.getConvertedAmountAndCharge(controller.selectedGiftCard);
                            },
                            validator: (value) {
                              if (value == null || value.isEmpty) {
                                return MyStrings.kAmountNumberError;
                              } else if (controller.selectedGiftCard.denominationType == AppStatus.range) {
                                return MyUtils().validateAmountForm(
                                  value: value,
                                  userCurrentBalance: controller.availableBalance,
                                  minLimit: AppConverter.formatNumberDouble(
                                    controller.selectedGiftCard.minRecipientDenomination ?? "0",
                                  ),
                                  maxLimit: AppConverter.formatNumberDouble(
                                    controller.selectedGiftCard.maxRecipientDenomination ?? "0",
                                  ),
                                );
                              } else {
                                double amount = AppConverter.formatNumberDouble(controller.total);

                                if (controller.availableBalance < amount) {
                                  return MyStrings.kAmountHigherError.tr;
                                } else {
                                  return null;
                                }
                              }
                            },
                          ),
                        ] else ...[
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              spaceDown(Dimensions.space8),
                              Wrap(
                                  runSpacing: Dimensions.space8.h,
                                  spacing: Dimensions.space8.w,
                                  children: List.generate(controller.selectedGiftCard.fixedRecipientDenominations?.length ?? 0, (index) {
                                    var denomination = controller.selectedGiftCard.fixedRecipientDenominations?[index];

                                    return GestureDetector(
                                      onTap: () {
                                        controller.amountController.text = denomination ?? "";
                                        controller.getConvertedAmountAndCharge(controller.selectedGiftCard);
                                      },
                                      child: CustomAppCard(
                                        radius: Dimensions.largeRadius,
                                        padding: EdgeInsets.symmetric(horizontal: Dimensions.space16, vertical: Dimensions.space10),
                                        backgroundColor: controller.amountController.text == denomination ? MyColor.getPrimaryColor().withValues(alpha: .1) : MyColor.getWhiteColor(),
                                        borderColor: controller.amountController.text == denomination ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                                        child: Text("${denomination ?? ""} ${controller.selectedGiftCard.recipientCurrencyCode ?? ""}", style: MyTextStyle.caption1Style.copyWith(color: MyColor.getPrimaryColor(), fontWeight: FontWeight.w600)),
                                      ),
                                    );
                                  })),
                            ],
                          ),
                        ],
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
                                  controller.availableBalance.toString(),
                                ),
                                style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getPrimaryColor()),
                              ),
                            ],
                          ),
                        ),
                        spaceDown(Dimensions.space12),
                        HeaderText(
                          text: MyStrings.quantity.tr,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        RoundedTextField(
                          contentPadding: EdgeInsets.symmetric(horizontal: Dimensions.space20, vertical: Dimensions.space18),
                          controller: controller.quantityController,
                          showLabelText: false,
                          isRequired: true,
                          labelText: MyStrings.quantity.tr,
                          keyboardType: TextInputType.number,
                          hintText: MyStrings.quantity,
                          textInputAction: TextInputAction.done,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                          focusBorderColor: MyColor.getPrimaryColor(),
                          // ✅ Prefix & Suffix Icons
                          prefixIcon: IconButton(
                            icon: const Icon(Icons.remove_circle_outline),
                            color: MyColor.getPrimaryColor(),
                            onPressed: () {
                              int current = int.tryParse(controller.quantityController.text) ?? 0;
                              if (current > 1) {
                                controller.quantityController.text = (current - 1).toString();
                                controller.getConvertedAmountAndCharge(controller.selectedGiftCard);
                              }
                            },
                          ),
                          suffixIcon: IconButton(
                            icon: const Icon(Icons.add_circle_outline),
                            color: MyColor.getPrimaryColor(),
                            onPressed: () {
                              int current = int.tryParse(controller.quantityController.text) ?? 0;
                              controller.quantityController.text = (current + 1).toString();
                              controller.getConvertedAmountAndCharge(controller.selectedGiftCard);
                            },
                          ),

                          onChanged: (value) {
                            controller.getConvertedAmountAndCharge(controller.selectedGiftCard);
                          },

                          validator: (value) {
                            if (value != null && value.isEmpty) {
                              return MyStrings.quantityIsRequired.tr;
                            }
                            return null;
                          },
                        ),
                        spaceDown(Dimensions.space10),
                        if (controller.amountController.text.isNotEmpty) ...[
                          Text.rich(
                            TextSpan(
                              children: [
                                TextSpan(
                                  text: "${MyStrings.unitPrice.tr}: ",
                                  style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                                TextSpan(
                                  text: " ${controller.unitPrice} ${controller.currency} (${controller.amountController.text} ${controller.selectedGiftCard.recipientCurrencyCode ?? ""})",
                                  style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                              ],
                            ),
                          ).animate().fadeIn(),
                          spaceDown(Dimensions.space5),
                          Text.rich(
                            TextSpan(
                              children: [
                                TextSpan(
                                  text: "${MyStrings.subTotal.tr}: ",
                                  style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                                TextSpan(
                                  text: "${controller.unitPrice} x ${controller.quantityController.text} = ",
                                  style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getBodyTextColor()),
                                ),
                                TextSpan(
                                  text: controller.subTotal,
                                  style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getPrimaryColor()),
                                ),
                              ],
                            ),
                          ).animate().fadeIn(),
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
        );
      },
    );
  }
}
