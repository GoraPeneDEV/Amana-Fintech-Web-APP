import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/buttons/hold_to_confirm_button.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/otp_field_widget/otp_field_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/app/screens/virtual_cards/controller/virtual_cards_controller.dart';
import 'package:ovopay/core/data/controller/otp_verification_controller/otp_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:lottie/lottie.dart';
import '../../../core/utils/util_exporter.dart';

class AppDialogs {
  static Future pinVerificationPopUpWidget(
    BuildContext context, {
    required Function(dynamic)? onSuccess,
    required Function(String pin)? onSubmitClick,
  }) {
    String pinCode = "";
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: false,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        return GetBuilder<VirtualCardsController>(
          builder: (controller) {
            return WillPopWidget(
              nextRoute: "",
              action: () {},
              child: Dialog(
                surfaceTintColor: MyColor.transparentColor,
                insetPadding: EdgeInsets.all(Dimensions.space16.w),
                backgroundColor: MyColor.transparentColor,
                insetAnimationCurve: Curves.easeIn,
                insetAnimationDuration: const Duration(milliseconds: 100),
                child: LayoutBuilder(
                  builder: (context, constraint) {
                    return Container(
                      padding: EdgeInsetsDirectional.all(Dimensions.space16.w),
                      decoration: BoxDecoration(
                        color: MyColor.white,
                        borderRadius: BorderRadius.all(Radius.circular(20.w)),
                        border: Border.all(
                          color: MyColor.transparentColor,
                          width: 0.6,
                        ),
                      ),
                      child: SingleChildScrollView(
                        physics: const BouncingScrollPhysics(),
                        child: ConstrainedBox(
                          constraints: BoxConstraints(
                            minHeight: constraint.maxHeight / 2,
                          ),
                          child: IntrinsicHeight(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              mainAxisSize: MainAxisSize.max,
                              children: [
                                Align(
                                  alignment: AlignmentDirectional.topEnd,
                                  child: IconButton(
                                    padding: EdgeInsets.all(
                                      Dimensions.space3.w,
                                    ),
                                    style: IconButton.styleFrom(),
                                    onPressed: () {
                                      Navigator.pop(context);
                                    },
                                    icon: MyAssetImageWidget(
                                      color: MyColor.getPrimaryColor(),
                                      isSvg: true,
                                      assetPath: MyIcons.closeButton,
                                      width: Dimensions.space40.w,
                                      height: Dimensions.space40.w,
                                    ),
                                  ),
                                ),

                                spaceDown(Dimensions.space30),
                                //body
                                Align(
                                  alignment: AlignmentDirectional.center,
                                  child: HeaderText(
                                    textAlign: TextAlign.center,
                                    text: MyStrings.pinVerification.tr,
                                    textStyle: MyTextStyle.headerH3.copyWith(
                                      color: MyColor.getHeaderTextColor(),
                                    ),
                                  ),
                                ),
                                spaceDown(Dimensions.space8),
                                Align(
                                  alignment: AlignmentDirectional.center,
                                  child: HeaderText(
                                    text: MyStrings.pinVerificationMsg.tr,
                                    textAlign: TextAlign.center,
                                    textStyle: MyTextStyle.sectionSubTitle1.copyWith(
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                  ),
                                ),
                                spaceDown(Dimensions.space35),
                                SizedBox(
                                  height: Dimensions.space60,
                                  child: RoundedTextField(
                                    labelText: MyStrings.pin.tr,
                                    hintText: MyStrings.enterYourPinCode.tr,
                                    textInputAction: TextInputAction.done,
                                    keyboardType: TextInputType.number,
                                    isPassword: true,
                                    forceShowSuffixDesign: true,
                                    onChanged: (value) {
                                      pinCode = value;
                                      controller.update();
                                    },
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
                                        return MyStrings.kPinMaxNumberError.tr.rKv({
                                          "digit": "${SharedPreferenceService.getMaxPinNumberDigit()}",
                                        }).tr;
                                      } else {
                                        return null;
                                      }
                                    },
                                  ),
                                ),
                                spaceDown(Dimensions.space10),

                                spaceDown(Dimensions.space15),

                                AppMainSubmitButton(
                                  isActive: pinCode.trim().isNotEmpty,
                                  isLoading: controller.isViewConfidentialDataLoading,
                                  text: MyStrings.continueText,
                                  onTap: () {
                                    if (onSubmitClick != null) {
                                      onSubmitClick(pinCode);
                                    }
                                  },
                                ),
                                spaceDown(Dimensions.space24),
                              ],
                            ),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
            );
          },
        );
      },
    );
  }

  static Future verifyOtpPopUpWidget(
    BuildContext context, {
    required Function(dynamic)? onSuccess,
    required String title,
    required String actionRemark,
    required String otpType,
  }) {
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: false,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        TextSpan buildTimerText(OtpVerificationController controller) {
          // Format time to show minutes and seconds
          int minutes = controller.time ~/ 60;
          int seconds = controller.time % 60;
          String timeText = '${minutes.toString().padLeft(2, '0')}:${seconds.toString().padLeft(2, '0')}';

          return TextSpan(text: timeText, style: MyTextStyle.sectionSubTitle1);
        }

        return WillPopWidget(
          nextRoute: "",
          action: () {},
          child: Dialog(
            surfaceTintColor: MyColor.transparentColor,
            insetPadding: EdgeInsets.all(Dimensions.space16.w),
            backgroundColor: MyColor.transparentColor,
            insetAnimationCurve: Curves.easeIn,
            insetAnimationDuration: const Duration(milliseconds: 100),
            child: LayoutBuilder(
              builder: (context, constraint) {
                return GetBuilder<OtpVerificationController>(
                  init: OtpVerificationController(),
                  initState: (state) {
                    WidgetsBinding.instance.addPostFrameCallback((t) {
                      if (state.controller != null) {
                        state.controller?.initializeOtpSteps(
                          actionRemark,
                          otpType,
                        );
                      }
                    });
                  },
                  builder: (controller) {
                    return Container(
                      padding: EdgeInsetsDirectional.all(Dimensions.space16.w),
                      decoration: BoxDecoration(
                        color: MyColor.white,
                        borderRadius: BorderRadius.all(Radius.circular(20.w)),
                        border: Border.all(
                          color: MyColor.transparentColor,
                          width: 0.6,
                        ),
                      ),
                      child: SingleChildScrollView(
                        physics: const BouncingScrollPhysics(),
                        child: ConstrainedBox(
                          constraints: BoxConstraints(
                            minHeight: constraint.maxHeight / 2,
                          ),
                          child: IntrinsicHeight(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              mainAxisSize: MainAxisSize.max,
                              children: [
                                Align(
                                  alignment: AlignmentDirectional.topEnd,
                                  child: IconButton(
                                    padding: EdgeInsets.all(
                                      Dimensions.space3.w,
                                    ),
                                    style: IconButton.styleFrom(),
                                    onPressed: () {
                                      Navigator.pop(context);
                                    },
                                    icon: MyAssetImageWidget(
                                      color: MyColor.getPrimaryColor(),
                                      isSvg: true,
                                      assetPath: MyIcons.closeButton,
                                      width: Dimensions.space40.w,
                                      height: Dimensions.space40.w,
                                    ),
                                  ),
                                ),

                                spaceDown(Dimensions.space30),
                                //body
                                Align(
                                  alignment: AlignmentDirectional.center,
                                  child: HeaderText(
                                    textAlign: TextAlign.center,
                                    text: MyStrings.otpVerification.tr,
                                    textStyle: MyTextStyle.headerH3.copyWith(
                                      color: MyColor.getHeaderTextColor(),
                                    ),
                                  ),
                                ),
                                spaceDown(Dimensions.space8),
                                Align(
                                  alignment: AlignmentDirectional.center,
                                  child: HeaderText(
                                    text: "${MyStrings.weHaveSentACodeTo.tr} ${otpType == "email" ? SharedPreferenceService.getUserEmail().toNumberMask(unmaskedPrefix: 2, unmaskedSuffix: 4, maskChar: "•") : otpType == "sms" ? "+${SharedPreferenceService.getDialCode()}${SharedPreferenceService.getUserPhoneNumber().toNumberMask(unmaskedPrefix: 2, unmaskedSuffix: 2, maskChar: "•")}" : ""}",
                                    textAlign: TextAlign.center,
                                    textStyle: MyTextStyle.sectionSubTitle1.copyWith(color: MyColor.getBodyTextColor()),
                                  ),
                                ),
                                spaceDown(Dimensions.space35),
                                SizedBox(
                                  height: Dimensions.space60.w,
                                  child: OTPFieldWidget(
                                    controller: controller.otpController,
                                    onChanged: (v) {
                                      controller.onChangeOtpWidgetText(
                                        value: v,
                                      );
                                    },
                                  ),
                                ),
                                spaceDown(Dimensions.space25),

                                AppMainSubmitButton(
                                  isActive: controller.otpController.text.trim().isNotEmpty,
                                  isLoading: controller.submitLoading,
                                  text: MyStrings.continueText,
                                  onTap: () {
                                    controller.verifyOtp(onSuccess: onSuccess);
                                  },
                                ),
                                spaceDown(Dimensions.space24),
                                RichText(
                                  textAlign: TextAlign.center,
                                  text: TextSpan(
                                    text: "${controller.isOtpExpired == false ? MyStrings.waitUtilTheTimerFinishes.tr : MyStrings.didNotReceiveCode.tr} ",
                                    style: MyTextStyle.sectionSubTitle1,
                                    children: <TextSpan>[
                                      if (controller.isOtpExpired == false) ...[
                                        buildTimerText(controller),
                                      ] else ...[
                                        TextSpan(
                                          text: controller.resendLoading ? "${MyStrings.resending.tr}..." : MyStrings.resendCode.tr,
                                          style: MyTextStyle.sectionSubTitle1.copyWith(
                                            decoration: TextDecoration.underline,
                                            decorationColor: MyColor.getPrimaryColor(),
                                            color: MyColor.getPrimaryColor(),
                                          ),
                                          recognizer: TapGestureRecognizer()
                                            ..onTap = () {
                                              if (!controller.resendLoading) {
                                                controller.resendOtp();
                                              }
                                            },
                                        ),
                                      ],
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        );
      },
    );
  }

  static Future confirmDialog(
    BuildContext context, {
    required Function onFinish,
    Function? onWaiting,
    required String title,
    required Widget? userDetailsWidget,
    required Widget cashDetailsWidget,
  }) {
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: false,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        return WillPopWidget(
          nextRoute: "",
          action: () {},
          child: Dialog(
            surfaceTintColor: MyColor.transparentColor,
            insetPadding: EdgeInsets.all(Dimensions.space16.w),
            backgroundColor: MyColor.transparentColor,
            insetAnimationCurve: Curves.easeIn,
            insetAnimationDuration: const Duration(milliseconds: 100),
            child: LayoutBuilder(
              builder: (context, constraint) {
                return Container(
                  padding: EdgeInsetsDirectional.all(Dimensions.space16.w),
                  decoration: BoxDecoration(
                    color: MyColor.white,
                    borderRadius: BorderRadius.all(Radius.circular(20.w)),
                    border: Border.all(
                      color: MyColor.transparentColor,
                      width: 0.6,
                    ),
                  ),
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    child: ConstrainedBox(
                      constraints: BoxConstraints(
                        minHeight: constraint.maxHeight / 2,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        mainAxisSize: MainAxisSize.max,
                        children: [
                          //TITLE
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              //Title
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  HeaderText(
                                    text: MyStrings.confirmTo.tr,
                                    textStyle: MyTextStyle.headerH3.copyWith(
                                      color: MyColor.getBodyTextColor(),
                                      fontWeight: FontWeight.w400,
                                    ),
                                  ),
                                  HeaderText(
                                    text: title.tr,
                                    textStyle: MyTextStyle.headerH3.copyWith(
                                      color: MyColor.getPrimaryColor(),
                                    ),
                                  ),
                                ],
                              ),

                              //Close Button
                              IconButton(
                                padding: EdgeInsets.all(Dimensions.space3.w),
                                style: IconButton.styleFrom(),
                                onPressed: () {
                                  Navigator.pop(context);
                                },
                                icon: MyAssetImageWidget(
                                  color: MyColor.getPrimaryColor(),
                                  isSvg: true,
                                  assetPath: MyIcons.closeButton,
                                  width: Dimensions.space40.w,
                                  height: Dimensions.space40.w,
                                ),
                              ),
                            ],
                          ),

                          spaceDown(Dimensions.space30),
                          //body
                          userDetailsWidget ?? SizedBox(),
                          spaceDown(Dimensions.space15),
                          cashDetailsWidget,
                          spaceDown(Dimensions.space30),

                          HoldToConfirmButton(
                            zoomScale: false,
                            onProgressChange: (value) {
                              // printX(value);
                            },
                            onProgressCompleted: () async {
                              printX("Completed");
                            },
                            onApiCall: () async {
                              await onFinish();
                            },
                            hapticFeedback: true,
                            border: Border.all(color: MyColor.getBorderColor()),
                            backgroundColor: MyColor.getScreenBgColor(),
                            fillColor: MyColor.getPrimaryColor().withValues(
                              alpha: 0.8,
                            ),
                            borderRadius: const BorderRadius.all(
                              Radius.circular(12),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
        );
      },
    );
  }

  static Future successDialog(
    BuildContext context, {
    required String title,
    required Widget userDetailsWidget,
    required Widget cashDetailsWidget,
  }) {
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: false,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        return WillPopWidget(
          nextRoute: "",
          action: () {},
          child: Dialog(
            surfaceTintColor: MyColor.transparentColor,
            insetPadding: EdgeInsets.all(Dimensions.space16.w),
            backgroundColor: MyColor.transparentColor,
            insetAnimationCurve: Curves.fastOutSlowIn,
            insetAnimationDuration: const Duration(milliseconds: 100),
            child: LayoutBuilder(
              builder: (context, constraint) {
                return Container(
                  padding: EdgeInsetsDirectional.symmetric(
                    vertical: Dimensions.space32.w,
                    horizontal: Dimensions.space16.w,
                  ),
                  decoration: BoxDecoration(
                    color: MyColor.white,
                    borderRadius: BorderRadius.all(Radius.circular(20.w)),
                    border: Border.all(
                      color: MyColor.transparentColor,
                      width: 0.6,
                    ),
                  ),
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    child: ConstrainedBox(
                      constraints: BoxConstraints(
                        minHeight: constraint.maxHeight / 2,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        mainAxisSize: MainAxisSize.max,
                        children: [
                          //TITLE
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            crossAxisAlignment: CrossAxisAlignment.center,
                            children: [
                              //Close Button
                              MyAssetImageWidget(
                                color: MyColor.getPrimaryColor(),
                                isSvg: true,
                                assetPath: MyIcons.verifyIcon,
                                width: Dimensions.space32.w,
                                height: Dimensions.space32.w,
                              ),
                              spaceSide(Dimensions.space10),
                              //Title
                              Expanded(
                                child: HeaderText(
                                  text: title.tr,
                                  textStyle: MyTextStyle.sectionTitle.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                    fontWeight: FontWeight.w400,
                                  ),
                                ),
                              ),
                            ],
                          ),

                          spaceDown(Dimensions.space30),
                          //body
                          userDetailsWidget,
                          spaceDown(Dimensions.space15),
                          cashDetailsWidget,
                          spaceDown(Dimensions.space30),

                          CustomElevatedBtn(
                            radius: Dimensions.largeRadius.r,
                            bgColor: MyColor.getPrimaryColor(),
                            text: MyStrings.backToHome.tr,
                            onTap: () {
                              Get.offAllNamed(RouteHelper.dashboardScreen);
                            },
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
        );
      },
    );
  }

  static Future successDialogForAll(
    BuildContext context, {
    required String title,
    required String subTitle,
    String buttonTitle = MyStrings.continueText,
    required VoidCallback onTap,
    bool barrierDismissible = true,
  }) {
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: barrierDismissible,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        return Dialog(
          surfaceTintColor: MyColor.transparentColor,
          insetPadding: EdgeInsets.all(Dimensions.space16.w),
          backgroundColor: MyColor.transparentColor,
          insetAnimationCurve: Curves.fastOutSlowIn,
          insetAnimationDuration: const Duration(milliseconds: 100),
          child: LayoutBuilder(
            builder: (context, constraint) {
              return Container(
                padding: EdgeInsetsDirectional.symmetric(
                  vertical: Dimensions.space32.w,
                  horizontal: Dimensions.space16.w,
                ),
                decoration: BoxDecoration(
                  color: MyColor.white,
                  borderRadius: BorderRadius.all(Radius.circular(20.w)),
                  border: Border.all(
                    color: MyColor.transparentColor,
                    width: 0.6,
                  ),
                ),
                child: SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  child: IntrinsicHeight(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.center,
                      mainAxisSize: MainAxisSize.max,
                      children: [
                        SizedBox(
                          width: 150.w,
                          height: 150.w,
                          child: Lottie.asset(
                            MyIcons.successLottieIcon,
                            repeat: false,
                            filterQuality: FilterQuality.high,
                            fit: BoxFit.contain,
                            width: 150.w,
                            height: 150.h,
                          ),
                        ),
                        spaceDown(Dimensions.space30),
                        HeaderText(
                          text: title,
                          textStyle: MyTextStyle.headerH3.copyWith(
                            color: MyColor.getDarkColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space4),
                        HeaderTextSmaller(
                          text: subTitle.tr,
                          textAlign: TextAlign.center,
                          textStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space30),
                        CustomElevatedBtn(
                          radius: Dimensions.largeRadius.r,
                          bgColor: MyColor.getPrimaryColor(),
                          text: buttonTitle.tr,
                          onTap: onTap,
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }

  static Future globalAppDialogForAll(
    BuildContext context, {
    required String title,
    required String subTitle,
    String buttonTitle = MyStrings.continueText,
    OvoDialogTypeType type = OvoDialogTypeType.error,
    required VoidCallback onTap,
  }) {
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: true,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        return Dialog(
          surfaceTintColor: MyColor.transparentColor,
          insetPadding: EdgeInsets.all(Dimensions.space16.w),
          backgroundColor: MyColor.transparentColor,
          insetAnimationCurve: Curves.fastOutSlowIn,
          insetAnimationDuration: const Duration(milliseconds: 100),
          child: LayoutBuilder(
            builder: (context, constraint) {
              return Container(
                padding: EdgeInsetsDirectional.symmetric(
                  vertical: Dimensions.space32.w,
                  horizontal: Dimensions.space16.w,
                ),
                decoration: BoxDecoration(
                  color: MyColor.white,
                  borderRadius: BorderRadius.all(Radius.circular(20.w)),
                  border: Border.all(
                    color: MyColor.transparentColor,
                    width: 0.6,
                  ),
                ),
                child: SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  child: IntrinsicHeight(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.center,
                      mainAxisSize: MainAxisSize.max,
                      children: [
                        SizedBox(
                          width: 150.w,
                          height: 150.h,
                          child: Lottie.asset(
                            type == OvoDialogTypeType.error
                                ? MyIcons.errorLottieIcon
                                : type == OvoDialogTypeType.warning
                                    ? MyIcons.warningLottieIcon
                                    : type == OvoDialogTypeType.success
                                        ? MyIcons.successLottieIcon
                                        : MyIcons.warningLottieIcon,
                            repeat: false,
                            filterQuality: FilterQuality.high,
                            fit: BoxFit.contain,
                            width: 150.w,
                            height: 150.h,
                          ),
                        ),
                        spaceDown(Dimensions.space30),
                        HeaderText(
                          text: title.tr,
                          textStyle: MyTextStyle.headerH3.copyWith(
                            color: MyColor.getDarkColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space4),
                        HeaderTextSmaller(
                          text: subTitle.tr,
                          textAlign: TextAlign.center,
                          textStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space30),
                        CustomElevatedBtn(
                          radius: Dimensions.largeRadius.r,
                          bgColor: MyColor.getPrimaryColor(),
                          text: buttonTitle.tr,
                          onTap: onTap,
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }

  static Future confirmDialogForAll(
    BuildContext context, {
    String? title,
    String? subTitle,
    String buttonTitle = MyStrings.yes,
    required VoidCallback onConfirmTap,
    bool isConfirmLoading = false,
    VoidCallback? onCancelTap,
  }) {
    return showDialog(
      context: context,
      useSafeArea: true,
      barrierDismissible: true,
      traversalEdgeBehavior: TraversalEdgeBehavior.leaveFlutterView,
      builder: (_) {
        return Dialog(
          surfaceTintColor: MyColor.transparentColor,
          insetPadding: EdgeInsets.all(Dimensions.space16.w),
          backgroundColor: MyColor.transparentColor,
          insetAnimationCurve: Curves.fastOutSlowIn,
          insetAnimationDuration: const Duration(milliseconds: 100),
          child: LayoutBuilder(
            builder: (context, constraint) {
              return Container(
                padding: EdgeInsetsDirectional.symmetric(
                  vertical: Dimensions.space32.w,
                  horizontal: Dimensions.space16.w,
                ),
                decoration: BoxDecoration(
                  color: MyColor.getWhiteColor(),
                  borderRadius: BorderRadius.all(Radius.circular(20.w)),
                  border: Border.all(
                    color: MyColor.transparentColor,
                    width: 0.6,
                  ),
                ),
                child: SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  child: IntrinsicHeight(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.center,
                      mainAxisSize: MainAxisSize.max,
                      children: [
                        SizedBox(
                          width: Dimensions.space60.w,
                          height: Dimensions.space60.w,
                          child: Lottie.asset(
                            MyIcons.warningLottieIcon,
                            repeat: false,
                            filterQuality: FilterQuality.high,
                            fit: BoxFit.contain,
                            width: Dimensions.space60.w,
                            height: Dimensions.space60.w,
                          ),
                        ),
                        spaceDown(Dimensions.space10),
                        HeaderText(
                          text: title?.tr ?? MyStrings.pleaseConfirm.tr,
                          textStyle: MyTextStyle.headerH3.copyWith(
                            color: MyColor.getDarkColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space4),
                        HeaderTextSmaller(
                          text: subTitle?.tr ?? MyStrings.sureToDoThis.tr,
                          textAlign: TextAlign.center,
                          textStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space30),
                        Row(
                          children: [
                            Expanded(
                              child: CustomElevatedBtn(
                                radius: Dimensions.largeRadius.r,
                                bgColor: MyColor.getWhiteColor(),
                                borderColor: MyColor.getBodyTextColor(),
                                textColor: MyColor.getBodyTextColor(),
                                text: MyStrings.cancel.tr,
                                onTap: () {
                                  if (onCancelTap == null) {
                                    Navigator.of(context).maybePop();
                                  } else {
                                    onCancelTap();
                                  }
                                },
                              ),
                            ),
                            spaceSide(Dimensions.space10),
                            Expanded(
                              child: StatefulBuilder(
                                builder: (context, setState) {
                                  return CustomElevatedBtn(
                                    isLoading: isConfirmLoading,
                                    radius: Dimensions.largeRadius.r,
                                    bgColor: MyColor.getPrimaryColor(),
                                    text: buttonTitle.tr,
                                    onTap: () {
                                      setState(() {
                                        isConfirmLoading = true;
                                        onConfirmTap();
                                      });
                                    },
                                  );
                                },
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }
}
