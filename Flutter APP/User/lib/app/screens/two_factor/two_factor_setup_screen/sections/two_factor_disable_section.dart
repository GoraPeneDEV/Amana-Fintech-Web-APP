import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/otp_field_widget/otp_field_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/controller/two_factor_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class TwoFactorDisableSection extends StatelessWidget {
  const TwoFactorDisableSection({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TwoFactorController>(
      builder: (twoFactorController) {
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
                        text: MyStrings.disable2Fa.tr,
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
                    spaceDown(Dimensions.space24),
                    OTPFieldWidget(
                      onChanged: (v) {
                        twoFactorController.currentText = v;
                      },
                    ),
                  ],
                ),
              ),
              spaceDown(Dimensions.space20),
              CustomElevatedBtn(
                isLoading: twoFactorController.submitLoading,
                radius: Dimensions.largeRadius.r,
                bgColor: MyColor.getPrimaryColor(),
                text: MyStrings.confirm.tr,
                onTap: () {
                  MyUtils.clearAllTypeFocusNodes();

                  twoFactorController.disable2fa(
                    twoFactorController.currentText,
                  );
                },
              ),
              const SizedBox(height: Dimensions.space30),
            ],
          ),
        );
      },
    );
  }
}
