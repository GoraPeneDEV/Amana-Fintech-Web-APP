import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/route/route.dart';
import '../../../../core/utils/util_exporter.dart';

class SecurityScreen extends StatelessWidget {
  const SecurityScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.security,
      body: SingleChildScrollView(
        clipBehavior: Clip.none,
        child: Column(
          children: [
            CustomAppCard(
              padding: EdgeInsets.symmetric(horizontal: Dimensions.space16.w),
              child: Column(
                children: [
                  _buildMenuListTile(
                    title: MyStrings.pin,
                    subtitle: MyStrings.changeOrEditPINnumber,
                    iconPath: MyIcons.pinIcon,
                    onPressed: () => Get.toNamed(RouteHelper.pinChangeScreen),
                  ),
                  _buildMenuListTile(
                    title: MyStrings.touchORFaceID,
                    subtitle: MyStrings.touchIDSubText,
                    iconPath: MyIcons.fingerIcon,
                    onPressed: () => Get.toNamed(RouteHelper.setupBioMetricScreen),
                  ),
                  _buildMenuListTile(
                    title: MyStrings.twoFactorAuthentication,
                    subtitle: MyStrings.setup2FAAuthentication,
                    iconPath: MyIcons.twoFaIcon,
                    onPressed: () => Get.toNamed(RouteHelper.twoFactorSetupScreen),
                  ),
                  if (SharedPreferenceService.isSupportQrCodeLogin()) ...[
                    _buildMenuListTile(
                      title: MyStrings.qrCodeLogin,
                      subtitle: MyStrings.qrCodeLoginSubTitle,
                      iconPath: MyIcons.walletQrCodeIcon,
                      onPressed: () => Get.toNamed(RouteHelper.qrCodeLoginScreen),
                      showBorder: false,
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuListTile({
    required String title,
    required String subtitle,
    required String iconPath,
    required VoidCallback onPressed,
    bool showBorder = true,
  }) {
    return CustomListTileCard(
      showBorder: showBorder,
      padding: EdgeInsets.symmetric(vertical: Dimensions.space17),
      title: title.tr,
      titleStyle: MyTextStyle.sectionTitle2.copyWith(
        color: MyColor.getHeaderTextColor(),
      ),
      subtitle: subtitle.tr,
      subtitleStyle: MyTextStyle.caption1Style.copyWith(
        color: MyColor.getBodyTextColor(),
      ),
      leading: MyAssetImageWidget(
        isSvg: true,
        assetPath: iconPath,
        width: Dimensions.space24.w,
        height: Dimensions.space24.w,
        boxFit: BoxFit.scaleDown,
        color: MyColor.getPrimaryColor(),
      ),
      trailing: Icon(
        Icons.arrow_forward_ios_rounded,
        color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
        size: Dimensions.space16.w,
      ),
      onPressed: onPressed,
    );
  }
}
