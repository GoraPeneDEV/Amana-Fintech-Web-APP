import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/otp_field_widget/otp_field_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/controller/two_factor_controller.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/widget/enable_qr_code_widget.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class TwoFactorEnableSection extends StatefulWidget {
  const TwoFactorEnableSection({super.key});

  @override
  State<TwoFactorEnableSection> createState() => _TwoFactorEnableSectionState();
}

class _TwoFactorEnableSectionState extends State<TwoFactorEnableSection> {
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

  void previousPage() {
    setState(() {
      _currentPage--;
      _pageController.animateToPage(
        _currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TwoFactorController>(
      builder: (twoFactorController) {
        return PageView(
          clipBehavior: Clip.none,
          controller: _pageController,
          physics: const NeverScrollableScrollPhysics(),
          children: [
            _buildQRCodeSection(twoFactorController),
            _buildEnableSection(context, twoFactorController),
          ],
        );
      },
    );
  }

  Widget _buildQRCodeSection(TwoFactorController controller) {
    return GetBuilder<TwoFactorController>(
      builder: (controller) {
        return RefreshIndicator(
          color: MyColor.getPrimaryColor(),
          onRefresh: () async {
            controller.get2FaCode();
          },
          child: SingleChildScrollView(
            clipBehavior: Clip.none,
            physics: AlwaysScrollableScrollPhysics(
              parent: const ClampingScrollPhysics(),
            ),
            child: Column(
              children: [
                CustomAppCard(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Center(
                        child: Text(
                          MyStrings.useQRCODETips.tr,
                          textAlign: TextAlign.center,
                          style: MyTextStyle.sectionSubTitle1.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                        ),
                      ),
                      spaceDown(Dimensions.space30),
                      EnableQRCodeWidget(
                        qrImage: controller.twoFactorCodeModel.data?.qrCodeUrl ?? '',
                        secret: "${controller.twoFactorCodeModel.data?.secret}",
                      ),
                    ],
                  ),
                ),
                spaceDown(Dimensions.space20),
                CustomElevatedBtn(
                  onTap: () {
                    _nextPage();
                  },
                  text: MyStrings.enterConfirmationCode.tr,
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildEnableSection(
    BuildContext context,
    TwoFactorController controller,
  ) {
    return SingleChildScrollView(
      physics: AlwaysScrollableScrollPhysics(
        parent: const ClampingScrollPhysics(),
      ),
      child: Column(
        children: [
          CustomAppCard(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.start,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Align(
                  alignment: AlignmentDirectional.center,
                  child: HeaderText(
                    textAlign: TextAlign.center,
                    text: MyStrings.enable2Fa.tr,
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
                    text: MyStrings.twoFactorMsg2.tr,
                    textStyle: MyTextStyle.sectionSubTitle1,
                  ),
                ),
                spaceDown(Dimensions.space24),
                OTPFieldWidget(
                  onChanged: (v) {
                    controller.currentText = v;
                  },
                ),
              ],
            ),
          ),
          spaceDown(Dimensions.space20),
          Row(
            children: [
              Expanded(
                child: CustomElevatedBtn(
                  isLoading: controller.submitLoading,
                  radius: Dimensions.largeRadius.r,
                  bgColor: MyColor.getPrimaryColor(),
                  text: MyStrings.confirm.tr,
                  onTap: () {
                    MyUtils.clearAllTypeFocusNodes();
                    controller.enable2fa(
                      controller.twoFactorCodeModel.data?.secret ?? '',
                      controller.currentText,
                      onSuccess: () {
                        AppDialogs.successDialogForAll(
                          context,
                          title: MyStrings.twoFactorEnable.tr,
                          subTitle: MyStrings.twoFactorEnableSubMsg.tr,
                          onTap: () {
                            Get.back();
                            // Get.back();
                          },
                        );
                      },
                    );
                  },
                ),
              ),
              spaceSide(Dimensions.space15),
              Expanded(
                child: CustomElevatedBtn(
                  radius: Dimensions.largeRadius.r,
                  textColor: MyColor.getDarkColor(),
                  bgColor: MyColor.getWhiteColor(),
                  borderColor: MyColor.getBorderColor(),
                  text: MyStrings.cancel,
                  onTap: () {
                    previousPage();
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: Dimensions.space30),
        ],
      ),
    );
  }
}
