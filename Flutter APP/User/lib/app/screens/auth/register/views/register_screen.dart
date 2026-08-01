import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/otp_field_widget/otp_field_widget.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/app/screens/auth/register/controller/registration_controller.dart';
import 'package:ovopay/app/screens/auth/register/views/widgets/profile_complete_screen.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/auth/signup_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';

class RegisterScreen extends StatefulWidget {
  final UserModel? userModel;
  const RegisterScreen({super.key, this.userModel});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final PageController _pageController = PageController(initialPage: 0);
  final PageController _pageController2 = PageController(
    initialPage: 0,
  ); // Initialize with the first page
  int _currentPage2 = 0;

  void _goToProfileComplete() {
    setState(() {
      _pageController.animateToPage(
        1,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  void _nextPage({int? goToPage}) {
    if (_pageController2.hasClients) {
      _pageController2.animateToPage(
        goToPage ?? ++_currentPage2,
        duration: const Duration(milliseconds: 600),
        curve: Curves.linear,
      );
    }
  }

  void _previousPage({int? goToPage}) {
    if (_pageController2.hasClients && _currentPage2 > 0) {
      // Check bounds
      _pageController2.animateToPage(
        goToPage ?? --_currentPage2,
        duration: const Duration(milliseconds: 600),
        curve: Curves.linear,
      );
    }
  }

  @override
  void initState() {
    Get.put(RegistrationRepo());
    final controller = Get.put(
      RegistrationController(registrationRepo: Get.find()),
    );
    super.initState();
    // Add listener to observe page changes
    _pageController2.addListener(() {
      final page = _pageController2.page?.round(); // Get the rounded page index
      if (page != null && page != _currentPage2) {
        setState(() {
          _currentPage2 = page; // Update the current page index
        });
      }
    });
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (widget.userModel?.sv == "0") {
        controller.sendAuthorizeCode();
      }
    });
  }

  @override
  void dispose() {
    super.dispose();
    _pageController.dispose();
    _pageController2.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return WillPopWidget(
      nextRoute: "",
      action: () {
        AppDialogs.confirmDialogForAll(
          context,
          onConfirmTap: () {
            Get.offAllNamed(RouteHelper.loginScreen);
          },
          title: MyStrings.exitTitle,
          subTitle: MyStrings.youWantToExitRegistrationProcess,
        );
      },
      child: MyCustomScaffold(
        pageTitle: MyStrings.register,
        onBackButtonTap: () {
          if (_currentPage2 == 0) {
            AppDialogs.confirmDialogForAll(
              context,
              onConfirmTap: () {
                Get.offAllNamed(RouteHelper.loginScreen);
              },
              title: MyStrings.exitTitle,
              subTitle: MyStrings.youWantToExitRegistrationProcess,
            );
          }
          if (_currentPage2 == 1 || _currentPage2 == 2) {
            _previousPage();
          }
        },
        actionButton: [],
        body: GetBuilder<RegistrationController>(
          builder: (controller) {
            return PageView(
              controller: _pageController,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                if (widget.userModel?.sv == "0") ...[
                  _buildOtpCodeVerificationPage(controller),
                ],
                ProfileCompleteScreen(
                  pageController: _pageController2,
                  currentPage: _currentPage2,
                  nextPage: _nextPage,
                  previousPage: _previousPage,
                ),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget _buildOtpCodeVerificationPage(RegistrationController controller) {
    TextSpan buildTimerText(RegistrationController controller) {
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
                    text: MyStrings.smsVerification.tr,
                    textStyle: MyTextStyle.headerH3.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                  ),
                ),
                spaceDown(Dimensions.space8),
                Align(
                  alignment: AlignmentDirectional.center,
                  child: HeaderText(
                    text: "${MyStrings.weHaveSentACodeTo.tr} +${widget.userModel?.dialCode}${widget.userModel?.mobile?.toNumberMask(unmaskedPrefix: 2, unmaskedSuffix: 2, maskChar: "•")}",
                    textStyle: MyTextStyle.sectionSubTitle1.copyWith(color: MyColor.getBodyTextColor()),
                  ),
                ),
                spaceDown(Dimensions.space35),
                OTPFieldWidget(
                  // controller: controller.otpController,
                  onChanged: (v) {
                    controller.onChangeOtpWidgetText(value: v);
                  },
                ),
                spaceDown(Dimensions.space10),
              ],
            ),
          ),
          spaceDown(Dimensions.space15),
          CustomElevatedBtn(
            radius: Dimensions.largeRadius.r,
            isLoading: controller.submitLoading,
            bgColor: MyColor.getPrimaryColor(),
            text: MyStrings.verifyNow,
            onTap: () {
              controller.verifyYourSms(
                onSuccess: () {
                  _goToProfileComplete();
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
  }
}
