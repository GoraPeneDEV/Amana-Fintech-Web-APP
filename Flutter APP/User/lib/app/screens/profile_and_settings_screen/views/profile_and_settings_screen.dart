import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/controller/profile_controller.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../core/utils/util_exporter.dart';

class ProfileAndSettingsScreen extends StatefulWidget {
  const ProfileAndSettingsScreen({super.key, this.onItemTapped});
  final Function(int index)? onItemTapped;
  @override
  State<ProfileAndSettingsScreen> createState() => _ProfileAndSettingsScreenState();
}

class _ProfileAndSettingsScreenState extends State<ProfileAndSettingsScreen> {
  @override
  void initState() {
    Get.put(ProfileRepo());
    final controller = Get.put(ProfileController(profileRepo: Get.find()));

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadProfileInfo();
    });
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.profile,
      onBackButtonTap: (widget.onItemTapped != null)
          ? () {
              widget.onItemTapped!(0);
            }
          : null,
      body: SingleChildScrollView(
        clipBehavior: Clip.none,
        child: Column(
          children: [
            CustomAppCard(
              onPressed: () {
                Get.toNamed(RouteHelper.profileInformationScreen);
              },
              child: GetBuilder<ProfileController>(
                builder: (controller) {
                  return Skeletonizer(
                    enabled: controller.isLoading,
                    child: CustomListTileCard(
                      leading: Skeleton.replace(
                        replace: true,
                        replacement: Bone.square(size: Dimensions.space48.h),
                        child: MyNetworkImageWidget(
                          radius: Dimensions.radiusMax.r,
                          imageUrl: SharedPreferenceService.getUserImage(),
                          isProfile: true,
                          width: Dimensions.space48.w,
                          height: Dimensions.space48.w,
                          imageAlt: SharedPreferenceService.getUserFullName(),
                        ),
                      ),
                      padding: EdgeInsetsDirectional.zero,
                      imagePath: SharedPreferenceService.getUserImage(),
                      title: SharedPreferenceService.getUserFullName(),
                      subtitle: "+${SharedPreferenceService.getDialCode()}${SharedPreferenceService.getUserPhoneNumber()}",
                      showBorder: false,
                      titleStyle: MyTextStyle.sectionTitle.copyWith(
                        color: MyColor.getDarkColor(),
                      ),
                      subtitleStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
                        color: MyColor.getBodyTextColor(),
                      ),
                      imageWidth: Dimensions.space48.w,
                      imageHeight: Dimensions.space48.w,
                    ),
                  );
                },
              ),
            ),
            spaceDown(Dimensions.space20),
            CustomAppCard(
              padding: EdgeInsetsDirectional.symmetric(
                horizontal: Dimensions.space16.w,
              ),
              child: Column(
                children: [
                  CustomListTileCard(
                    padding: EdgeInsetsDirectional.symmetric(
                      vertical: Dimensions.space17,
                    ),
                    title: MyStrings.personalInformation.tr,
                    showBorder: true,
                    titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    leading: MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.profileInactive,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                      boxFit: BoxFit.scaleDown,
                      color: MyColor.getPrimaryColor(),
                    ),
                    trailing: Icon(
                      Icons.arrow_forward_ios_rounded,
                      color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                    ),
                    onPressed: () {
                      Get.toNamed(RouteHelper.profileInformationScreen);
                    },
                  ),
                  CustomListTileCard(
                    padding: EdgeInsetsDirectional.symmetric(
                      vertical: Dimensions.space17,
                    ),
                    title: MyStrings.notification.tr,
                    showBorder: true,
                    titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    leading: MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.notificationIcon,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                      boxFit: BoxFit.scaleDown,
                      color: MyColor.getPrimaryColor(),
                    ),
                    trailing: Icon(
                      Icons.arrow_forward_ios_rounded,
                      color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                    ),
                    onPressed: () {
                      Get.toNamed(RouteHelper.notificationSettingsScreen);
                    },
                  ),
                  CustomListTileCard(
                    padding: EdgeInsetsDirectional.symmetric(
                      vertical: Dimensions.space17,
                    ),
                    title: MyStrings.security.tr,
                    showBorder: true,
                    titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    leading: MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.lock,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                      color: MyColor.getPrimaryColor(),
                    ),
                    trailing: Icon(
                      Icons.arrow_forward_ios_rounded,
                      color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                    ),
                    onPressed: () {
                      Get.toNamed(RouteHelper.securityScreen);
                    },
                  ),
                  CustomListTileCard(
                    padding: EdgeInsetsDirectional.symmetric(
                      vertical: Dimensions.space17,
                    ),
                    title: MyStrings.supportTicket.tr,
                    showBorder: true,
                    titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    leading: MyAssetImageWidget(
                      assetPath: MyIcons.helpDeskIcon,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                      boxFit: BoxFit.scaleDown,
                      color: MyColor.getPrimaryColor(),
                    ),
                    trailing: Icon(
                      Icons.arrow_forward_ios_rounded,
                      color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                    ),
                    onPressed: () {
                      Get.toNamed(RouteHelper.supportTicketScreen);
                    },
                  ),
                  CustomListTileCard(
                    padding: EdgeInsetsDirectional.symmetric(
                      vertical: Dimensions.space17,
                    ),
                    title: MyStrings.privacySettings.tr,
                    showBorder: true,
                    titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    leading: MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.privacyIcon,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                      boxFit: BoxFit.scaleDown,
                      color: MyColor.getPrimaryColor(),
                    ),
                    trailing: Icon(
                      Icons.arrow_forward_ios_rounded,
                      color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                    ),
                    onPressed: () {
                      Get.toNamed(RouteHelper.privacyScreen);
                    },
                  ),
                  if (SharedPreferenceService.isSupportMultiLanguage()) ...[
                    CustomListTileCard(
                      padding: EdgeInsetsDirectional.symmetric(
                        vertical: Dimensions.space17,
                      ),
                      title: MyStrings.appPreference.tr,
                      showBorder: false,
                      titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                        color: MyColor.getBodyTextColor(),
                      ),
                      leading: MyAssetImageWidget(
                        isSvg: true,
                        assetPath: MyIcons.settingsIcon,
                        width: Dimensions.space24.w,
                        height: Dimensions.space24.w,
                        boxFit: BoxFit.scaleDown,
                        color: MyColor.getPrimaryColor(),
                      ),
                      trailing: Icon(
                        Icons.arrow_forward_ios_rounded,
                        color: MyColor.getBodyTextColor().withValues(
                          alpha: 0.5,
                        ),
                      ),
                      onPressed: () {
                        Get.toNamed(RouteHelper.appPreferencesScreen);
                      },
                    ),
                  ],
                ],
              ),
            ),
            spaceDown(Dimensions.space20),
            //Log out
            GetBuilder<ProfileController>(
              builder: (controller) {
                return CustomAppCard(
                  child: CustomListTileCard(
                    padding: EdgeInsetsDirectional.zero,
                    title: MyStrings.logout.tr,
                    showBorder: false,
                    titleStyle: MyTextStyle.bodyTextStyle2.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    leading: MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.logoutIcon,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                      boxFit: BoxFit.scaleDown,
                      color: MyColor.getPrimaryColor(),
                    ),
                    trailing: controller.isLogOutLoading
                        ? SizedBox(
                            width: Dimensions.space24.w,
                            height: Dimensions.space24.w,
                            child: CircularProgressIndicator(
                              color: MyColor.getPrimaryColor(),
                              strokeWidth: 2,
                            ),
                          )
                        : null,
                  ),
                  onPressed: () {
                    if (SharedPreferenceService.getBioMetricStatus() == true) {
                      Get.offAllNamed(RouteHelper.loginScreen);
                    } else {
                      controller.logMeOut(
                        successCallback: () {
                          SharedPreferenceService.setAccessToken("");
                          SharedPreferenceService.setRememberMe(true);
                          SharedPreferenceService.setIsLoggedIn(false);
                          SharedPreferenceService.setBioMetricStatus(false);
                          Get.offAllNamed(RouteHelper.loginScreen);
                        },
                      );
                    }
                  },
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}
