import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/auth/biometric/controller/biometric_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../../core/utils/util_exporter.dart';

class SetupFingerPrintScreen extends StatefulWidget {
  const SetupFingerPrintScreen({super.key});

  @override
  State<SetupFingerPrintScreen> createState() => _SetupFingerPrintScreenState();
}

class _SetupFingerPrintScreenState extends State<SetupFingerPrintScreen> {
  @override
  void initState() {
    final controller = Get.put(BioMetricController());
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.checkAvailableBiometrics();
      controller.loadBiometricPreference();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<BioMetricController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.touchORFaceID,
          body: SingleChildScrollView(
            child: CustomAppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  MyAssetImageWidget(
                    isSvg: true,
                    assetPath: MyIcons.loginFingerPrintIcon,
                    color: MyColor.getPrimaryColor(),
                    width: Dimensions.space100.w,
                    height: Dimensions.space100.w,
                  ),
                  spaceDown(Dimensions.space20),
                  Text(
                    MyStrings.touchIDMsgText.tr,
                    textAlign: TextAlign.center,
                    style: MyTextStyle.sectionSubTitle1.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                  ),
                  spaceDown(Dimensions.space25),
                  if (controller.isShowBioMetricAccountPinBox == false) ...[
                    CustomElevatedBtn(
                      elevation: 0,
                      radius: Dimensions.largeRadius.r,
                      bgColor: MyColor.getWhiteColor(),
                      textColor: SharedPreferenceService.getBioMetricStatus() ? MyColor.error : MyColor.getPrimaryColor(),
                      text: SharedPreferenceService.getBioMetricStatus() ? MyStrings.disableTouchORFaceID : MyStrings.getStarted,
                      // text: MyStrings.getStarted,
                      borderColor: SharedPreferenceService.getBioMetricStatus() ? MyColor.error : MyColor.getPrimaryColor(),
                      shadowColor: MyColor.getWhiteColor(),
                      icon: IconButton(
                        onPressed: null,
                        icon: MyAssetImageWidget(
                          width: Dimensions.space40.w,
                          height: Dimensions.space40.w,
                          boxFit: BoxFit.contain,
                          assetPath: MyIcons.loginFingerPrintIcon,
                          color: SharedPreferenceService.getBioMetricStatus() ? MyColor.error : MyColor.getPrimaryColor(),
                          isSvg: true,
                        ),
                      ),
                      onTap: () async {
                        controller.toggleIsShowAccountPinBox();
                      },
                    ),
                  ] else ...[
                    RoundedTextField(
                      controller: controller.pinCodeController,
                      labelText: MyStrings.pin,
                      hintText: MyStrings.enterYourPinCode,
                      textInputAction: TextInputAction.done,
                      keyboardType: TextInputType.number,
                      isPassword: true,
                      textInputFormatter: [
                        FilteringTextInputFormatter.digitsOnly, // Allow only digits
                        LengthLimitingTextInputFormatter(
                          SharedPreferenceService.getMaxPinNumberDigit(),
                        ), // Limit to 5 characters
                      ],
                      validator: (value) {
                        if (value.toString().isEmpty) {
                          return MyStrings.kPinNumberError.tr;
                        } else if (value.toString().length < SharedPreferenceService.getMaxPinNumberDigit()) {
                          return MyStrings.kPinMaxNumberError.tr;
                        } else {
                          return null;
                        }
                      },
                    ),
                    spaceDown(Dimensions.space15),
                    if (SharedPreferenceService.getBioMetricStatus()) ...[
                      CustomElevatedBtn(
                        isLoading: controller.isPinValidateLoading,
                        elevation: 0,
                        radius: Dimensions.largeRadius.r,
                        bgColor: MyColor.error,
                        text: MyStrings.confirm,
                        onTap: () async {
                          MyUtils.clearAllTypeFocusNodes();
                          if (controller.pinCodeController.text.toString().length < SharedPreferenceService.getMaxPinNumberDigit()) {
                            CustomSnackBar.error(
                              errorList: [MyStrings.kPinMaxNumberError.tr],
                            );
                            return;
                          }
                          controller.disableBiometric(
                            onSuccess: () {
                              AppDialogs.globalAppDialogForAll(
                                context,
                                type: OvoDialogTypeType.warning,
                                title: MyStrings.touchORFaceID.tr,
                                subTitle: MyStrings.touchIDDisableSuccessMsgText.tr,
                                onTap: () {
                                  Get.back();
                                  Get.back();
                                },
                              );
                            },
                          );
                        },
                      ),
                    ] else ...[
                      CustomElevatedBtn(
                        isLoading: controller.isPinValidateLoading,
                        elevation: 0,
                        radius: Dimensions.largeRadius.r,
                        bgColor: MyColor.getPrimaryColor(),
                        text: MyStrings.confirm,
                        onTap: () async {
                          MyUtils.clearAllTypeFocusNodes();
                          if (controller.pinCodeController.text.toString().length < SharedPreferenceService.getMaxPinNumberDigit()) {
                            CustomSnackBar.error(
                              errorList: [MyStrings.kPinMaxNumberError.tr],
                            );
                            return;
                          }
                          controller.enableBiometric(
                            onSuccess: () {
                              AppDialogs.successDialogForAll(
                                context,
                                title: MyStrings.congratulations.tr,
                                subTitle: MyStrings.touchIDSuccessMsgText.tr,
                                onTap: () {
                                  Get.back();
                                  Get.back();
                                },
                              );
                            },
                          );
                        },
                      ),
                    ],
                  ],
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}
