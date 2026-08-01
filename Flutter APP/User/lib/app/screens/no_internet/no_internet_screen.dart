import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/dialog/exit_dialog.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class NoInterNetScreen extends StatelessWidget {
  const NoInterNetScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return WillPopWidget(
      child: MyCustomScaffold(
        pageTitle: MyStrings.noInternetTitle.tr,
        onBackButtonTap: () {
          showExitDialog(context);
        },
        body: CustomAppCard(
          height: MediaQuery.of(context).size.height,
          width: MediaQuery.of(context).size.width,
          child: FittedBox(
            fit: BoxFit.scaleDown,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                MyAssetImageWidget(
                  assetPath: MyImages.noInternatImage,
                  width: Dimensions.space100,
                  height: Dimensions.space100,
                ),
                spaceDown(Dimensions.space20),
                HeaderText(
                  text: MyStrings.noInternetTitle,
                  textAlign: TextAlign.center,
                ),
                spaceDown(Dimensions.space10),
                HeaderTextSmaller(
                  text: MyStrings.noInternetMessage,
                  textAlign: TextAlign.center,
                  textStyle: MyTextStyle.sectionSubTitle1.copyWith(),
                ),
                spaceDown(Dimensions.space40),
                CustomElevatedBtn(
                  width: Dimensions.space80.w,
                  height: Dimensions.space40.h,
                  text: MyStrings.tryAgain,
                  onTap: () {
                    Get.offAllNamed(RouteHelper.splashScreen);
                  },
                ),
                spaceDown(Dimensions.space50),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
