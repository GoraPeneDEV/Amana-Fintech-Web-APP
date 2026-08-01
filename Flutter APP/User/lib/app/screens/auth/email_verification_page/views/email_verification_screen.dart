import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:lottie/lottie.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/otp_field_widget/otp_field_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/auth/email_verification_page/controller/email_verification_controler.dart';
import 'package:ovopay/core/data/repositories/auth/sms_email_verification_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';

class EmailVerificationScreen extends StatefulWidget {
  const EmailVerificationScreen({super.key});

  @override
  State<EmailVerificationScreen> createState() => _EmailVerificationScreenState();
}

class _EmailVerificationScreenState extends State<EmailVerificationScreen> {
  @override
  void initState() {
    Get.put(SmsEmailVerificationRepo());
    final controller = Get.put(EmailVerificationController(repo: Get.find()));

    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.sendAuthorizeCode();
    });
  }

  @override
  void dispose() {
    super.dispose();
  }

  final PageController _pageController = PageController();
  int _currentPage = 0;

  void _nextPage() {
    setState(() {
      _currentPage++;
      _pageController.animateToPage(
        _currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.emailVerification,
      onBackButtonTap: () {
        Get.offAllNamed(RouteHelper.loginScreen);
      },
      // body: _buildCodeVerificationPage(),
      body: PageView(
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: [_buildCodeVerificationPage(), _buildSuccessPage()],
      ),
    );
  }

  Widget _buildCodeVerificationPage() {
    return GetBuilder<EmailVerificationController>(
      builder: (controller) {
        TextSpan buildTimerText(EmailVerificationController controller) {
          // Format time to show minutes and seconds
          int minutes = controller.time ~/ 60;
          int seconds = controller.time % 60;
          String timeText = '${minutes.toString().padLeft(2, '0')}:${seconds.toString().padLeft(2, '0')}';

          return TextSpan(text: timeText, style: MyTextStyle.sectionSubTitle1);
        }

        return SingleChildScrollView(
          child: Column(
            children: [
              //Code Verification
              CustomAppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Align(
                      alignment: AlignmentDirectional.center,
                      child: HeaderText(
                        textAlign: TextAlign.center,
                        text: MyStrings.emailVerification.tr,
                        textStyle: MyTextStyle.headerH3.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                      ),
                    ),
                    spaceDown(Dimensions.space8),
                    Align(
                      alignment: AlignmentDirectional.center,
                      child: HeaderText(
                        textAlign: TextAlign.center,
                        text: MyStrings.emailVerificationMsg.tr,
                        textStyle: MyTextStyle.sectionSubTitle1,
                      ),
                    ),
                    spaceDown(Dimensions.space35),
                    OTPFieldWidget(
                      // controller: controller.otpController,
                      onChanged: controller.onOtpBoxValueChange,
                    ),
                    spaceDown(Dimensions.space10),
                  ],
                ),
              ),
              spaceDown(Dimensions.space15),
              CustomElevatedBtn(
                isLoading: controller.submitLoading,
                radius: Dimensions.largeRadius.r,
                bgColor: MyColor.getPrimaryColor(),
                text: MyStrings.verifyNow,
                onTap: () {
                  controller.verifyYourEmail(
                    onSuccess: () {
                      _nextPage();
                    },
                  );
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
        );
      },
    );
  }

  Widget _buildSuccessPage() {
    return GetBuilder<EmailVerificationController>(
      builder: (controller) {
        return Column(
          children: [
            CustomAppCard(
              padding: EdgeInsetsDirectional.symmetric(
                horizontal: Dimensions.space16.w,
                vertical: Dimensions.space56.h,
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: SizedBox(
                      width: 150.w,
                      height: 150.h,
                      child: Lottie.asset(
                        MyIcons.successLottieIcon,
                        repeat: false,
                        filterQuality: FilterQuality.high,
                        fit: BoxFit.contain,
                        width: 150.w,
                        height: 150.h,
                      ),
                    ),
                  ),
                  spaceDown(Dimensions.space8),
                  Align(
                    alignment: AlignmentDirectional.center,
                    child: HeaderText(
                      textAlign: TextAlign.center,
                      text: MyStrings.emailVerifiedSuccessfully.tr,
                      textStyle: MyTextStyle.headerH3.copyWith(
                        color: MyColor.getHeaderTextColor(),
                      ),
                    ),
                  ),
                  spaceDown(Dimensions.space24),
                  CustomElevatedBtn(
                    radius: Dimensions.largeRadius.r,
                    bgColor: MyColor.getPrimaryColor(),
                    text: MyStrings.continueText,
                    onTap: () {
                      RouteHelper.checkUserStatusAndGoToNextStep(
                        controller.userModel,
                      );
                    },
                  ),
                ],
              ),
            ),
          ],
        );
      },
    );
  }
}
