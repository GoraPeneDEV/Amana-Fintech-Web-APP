import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/chip/custom_chip.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/expand_able_text.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/donation_screen/controller/donation_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class DonationDynamicFormPage extends StatefulWidget {
  const DonationDynamicFormPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });
  final BuildContext context;
  final VoidCallback onSuccessCallback;

  @override
  State<DonationDynamicFormPage> createState() => _DonationDynamicFormPageState();
}

class _DonationDynamicFormPageState extends State<DonationDynamicFormPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<DonationController>(
      builder: (controller) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                if (controller.selectedOrganization != null) ...[
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CustomListTileCard(
                          leading: CustomAppCard(
                            width: Dimensions.space45.w,
                            height: Dimensions.space45.w,
                            radius: Dimensions.badgeRadius,
                            padding: EdgeInsetsDirectional.all(
                              Dimensions.space4.w,
                            ),
                            child: MyNetworkImageWidget(
                              boxFit: BoxFit.scaleDown,
                              imageUrl: controller.selectedOrganization?.getOrgImageUrl() ?? "",
                              isProfile: false,
                              width: Dimensions.space40.w,
                              height: Dimensions.space40.w,
                            ),
                          ),
                          imagePath: "",
                          title: controller.selectedOrganization?.name ?? "",
                          subtitleWidget: ExpandableText(
                            text: controller.selectedOrganization?.details ?? '',
                            charLimit: 80,
                            style: MyTextStyle.caption1Style.copyWith(
                              color: MyColor.getBodyTextColor(),
                            ),
                          ),
                          subtitleStyle: MyTextStyle.caption1Style.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                          showBorder: false,
                        ),
                      ],
                    ),
                  ),
                ],
                spaceDown(Dimensions.space16),
                CustomAppCard(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      HeaderTextSmaller(
                        textAlign: TextAlign.start,
                        text: MyStrings.senderDetails,
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          spaceDown(Dimensions.space16),
                          //Text Field
                          RoundedTextField(
                            controller: controller.nameController,
                            showLabelText: true,
                            labelText: MyStrings.name,
                            hintText: MyStrings.enterYourNameExample,
                            textInputAction: TextInputAction.next,
                            keyboardType: TextInputType.name,
                            validator: (value) {
                              if (value.toString().trim().isEmpty) {
                                return MyStrings.kNameNullError.tr;
                              } else {
                                return null;
                              }
                            },
                          ),
                          spaceDown(Dimensions.space16),
                          RoundedTextField(
                            controller: controller.emailController,
                            showLabelText: true,
                            labelText: MyStrings.email,
                            hintText: MyStrings.enterYourEmailExample,
                            textInputAction: TextInputAction.next,
                            keyboardType: TextInputType.emailAddress,
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
                          RoundedTextField(
                            controller: controller.noteController,
                            maxLine: 5,
                            labelText: MyStrings.note,
                            hintText: MyStrings.enterNote,
                            textInputAction: TextInputAction.newline,
                            keyboardType: TextInputType.multiline,
                          ),
                          spaceDown(Dimensions.space16),
                          RoundedTextField(
                            controller: controller.amountController,
                            showLabelText: true,
                            labelText: MyStrings.amount,
                            hintText: MyStrings.enterAmount,
                            textInputAction: TextInputAction.done,
                            keyboardType: TextInputType.number,
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
                              if (value.toString().isEmpty) {
                                return MyStrings.kAmountNumberError.tr;
                              } else if (AppConverter.formatNumberDouble(
                                    value ?? "0",
                                  ) >
                                  controller.userCurrentBalance) {
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
                                  style: MyTextStyle.caption2Style.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                                TextSpan(
                                  text: MyUtils.getUserAmount(
                                    controller.userCurrentBalance.toString(),
                                  ),
                                  style: MyTextStyle.caption2Style.copyWith(
                                    color: MyColor.getPrimaryColor(),
                                  ),
                                ),
                              ],
                            ),
                            textAlign: TextAlign.start,
                          ),
                          spaceDown(Dimensions.space16),
                          InkWell(
                            onTap: () {
                              printE("object");
                              controller.toggleIdentity();
                            },
                            child: Row(
                              children: [
                                CustomAppCard(
                                  width: Dimensions.space20.w,
                                  height: Dimensions.space20.w,
                                  padding: EdgeInsets.zero,
                                  boxBorder: Border.all(
                                    width: 2,
                                    color: controller.isHideMyIdentities ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor(),
                                  ),
                                  backgroundColor: controller.isHideMyIdentities ? MyColor.getPrimaryColor() : MyColor.transparentColor,
                                  child: FittedBox(
                                    fit: BoxFit.scaleDown,
                                    child: Icon(
                                      Icons.check,
                                      color: controller.isHideMyIdentities ? MyColor.getWhiteColor() : MyColor.getWhiteColor(),
                                      size: Dimensions.space20.sp,
                                    ),
                                  ),
                                ),
                                spaceSide(Dimensions.space8),
                                Text(
                                  MyStrings.hideMyIdentity,
                                  style: MyTextStyle.sectionSubTitle1.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
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
                  text: MyStrings.continueText,
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
        );
      },
    );
  }
}
