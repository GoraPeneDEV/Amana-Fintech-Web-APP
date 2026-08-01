import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:lottie/lottie.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/otp_field_widget/otp_field_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/controller/two_factor_controller.dart';
import 'package:ovopay/core/data/repositories/auth/two_factor_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../core/utils/util_exporter.dart';

class TwoFactorVerificationScreen extends StatefulWidget {
  const TwoFactorVerificationScreen({super.key});

  @override
  State<TwoFactorVerificationScreen> createState() => _TwoFactorVerificationScreenState();
}

class _TwoFactorVerificationScreenState extends State<TwoFactorVerificationScreen> {
  @override
  void initState() {
    Get.put(TwoFactorRepo());
    Get.put(TwoFactorController(repo: Get.find()));
    super.initState();
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
      pageTitle: MyStrings.twoFactorAuthentication,
      body: PageView(
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: [_buildCodeVerificationPage(), _buildSuccessPage()],
      ),
    );
  }

  Widget _buildCodeVerificationPage() {
    return GetBuilder<TwoFactorController>(
      builder: (controller) {
        return Column(
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
                      text: MyStrings.verify2Fa.tr,
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
                      text: MyStrings.twoFactorMsg.tr,
                      textStyle: MyTextStyle.sectionSubTitle1,
                    ),
                  ),
                  spaceDown(Dimensions.space35),
                  OTPFieldWidget(onChanged: controller.onOtpBoxValueChange),
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
                controller.verify2FACode(
                  onSuccess: () {
                    _nextPage();
                  },
                );
              },
            ),
            spaceDown(Dimensions.space24),
          ],
        );
      },
    );
  }

  Widget _buildSuccessPage() {
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
                  text: MyStrings.twoFASuccessMessage.tr,
                  textStyle: MyTextStyle.headerH3.copyWith(
                    color: MyColor.getHeaderTextColor(),
                  ),
                ),
              ),
              spaceDown(Dimensions.space24),
              CustomElevatedBtn(
                radius: Dimensions.largeRadius.r,
                bgColor: MyColor.getPrimaryColor(),
                text: MyStrings.home,
                onTap: () {
                  Get.offAllNamed(RouteHelper.dashboardScreen);
                },
              ),
            ],
          ),
        ),
      ],
    );
  }
}
