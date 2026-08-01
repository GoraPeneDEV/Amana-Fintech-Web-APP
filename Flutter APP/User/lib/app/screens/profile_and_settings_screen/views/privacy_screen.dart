import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/core/route/route.dart';
import '../../../../core/utils/util_exporter.dart';

class PrivacyScreen extends StatelessWidget {
  const PrivacyScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.privacySettings,
      body: SingleChildScrollView(
        clipBehavior: Clip.none,
        child: Column(
          children: [
            CustomAppCard(
              padding: EdgeInsets.symmetric(horizontal: Dimensions.space16.w),
              child: Column(
                children: [
                  _buildMenuListTile(
                    title: MyStrings.accountDeletion.tr,
                    subtitle: MyStrings.accountDeletionSubTitle.tr,
                    iconPath: MyIcons.deleteUser,
                    onPressed: () => Get.toNamed(RouteHelper.deleteAccountScreen),
                  ),
                  _buildMenuListTile(
                    title: MyStrings.privacyPolicy,
                    iconPath: MyIcons.privacyIcon,
                    onPressed: () => Get.toNamed(RouteHelper.pageContentScreen),
                  ),
                  // _buildMenuListTile(
                  //   title: MyStrings.tosTitle,
                  //   iconPath: MyIcons.paperIcon,
                  //   onPressed: () => Get.toNamed(RouteHelper.pageContentScreen),
                  //   showBorder: true,
                  // ),
                  _buildMenuListTile(
                    title: MyStrings.faq,
                    iconPath: MyIcons.paperIcon,
                    onPressed: () => Get.toNamed(RouteHelper.faqScreen),
                    showBorder: false,
                  ),
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
    String? subtitle,
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
      subtitle: subtitle == null ? null : (subtitle).tr,
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
