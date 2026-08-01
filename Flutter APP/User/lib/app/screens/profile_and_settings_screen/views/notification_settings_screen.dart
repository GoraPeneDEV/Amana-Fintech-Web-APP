import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/core/data/controller/general_settings/general_settings_controller.dart';
import 'package:ovopay/core/data/repositories/auth/general_setting_repo.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../core/utils/util_exporter.dart';

class NotificationSettingsScreen extends StatefulWidget {
  const NotificationSettingsScreen({super.key});

  @override
  State<NotificationSettingsScreen> createState() => _NotificationSettingsScreenState();
}

class _NotificationSettingsScreenState extends State<NotificationSettingsScreen> {
  @override
  void initState() {
    Get.put(GeneralSettingRepo());
    final controller = Get.put(GeneralSettingsController(repo: Get.find()));

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadNotificationSettingsStatusInfo();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<GeneralSettingsController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.notificationSettings,
          body: SingleChildScrollView(
            clipBehavior: Clip.none,
            child: Skeletonizer(
              enabled: controller.isLoading,
              child: Column(
                children: [
                  CustomAppCard(
                    padding: EdgeInsets.symmetric(
                      horizontal: Dimensions.space16.w,
                    ),
                    child: Column(
                      children: [
                        _buildMenuListTile(
                          title: MyStrings.notificationSettingsTitle,
                          subtitle: MyStrings.pushNotificationDescription,
                          iconPath: MyIcons.notificationIcon,
                          trailing: IntrinsicWidth(
                            child: StatefulBuilder(
                              builder: (
                                BuildContext context,
                                StateSetter setState,
                              ) {
                                // Fetch the current notification status from SharedPreferences
                                bool isNotificationEnabled = controller.en;

                                return Skeleton.replace(
                                  replace: true,
                                  replacement: Bone.button(
                                    uniRadius: Dimensions.radiusProMax,
                                    height: Dimensions.space35,
                                    width: Dimensions.space60,
                                  ),
                                  child: CupertinoSwitch(
                                    value: isNotificationEnabled,
                                    onChanged: (value) async {
                                      controller.toggleEn();
                                      controller.saveNotificationSettingsStatusInfo();
                                    },
                                  ),
                                );
                              },
                            ),
                          ),
                          onPressed: () {},
                        ),
                        _buildMenuListTile(
                          title: MyStrings.emailNotifications,
                          subtitle: MyStrings.emailNotificationDescription,
                          iconPath: MyIcons.emailIcon,
                          trailing: IntrinsicWidth(
                            child: StatefulBuilder(
                              builder: (
                                BuildContext context,
                                StateSetter setState,
                              ) {
                                // Fetch the current notification status from SharedPreferences
                                bool isNotificationEnabled = controller.pn;

                                return Skeleton.replace(
                                  replace: true,
                                  replacement: Bone.button(
                                    uniRadius: Dimensions.radiusProMax,
                                    height: Dimensions.space35,
                                    width: Dimensions.space60,
                                  ),
                                  child: CupertinoSwitch(
                                    value: isNotificationEnabled,
                                    onChanged: (value) async {
                                      controller.togglePn();
                                      controller.saveNotificationSettingsStatusInfo();
                                    },
                                  ),
                                );
                              },
                            ),
                          ),
                          onPressed: () {},
                        ),
                        _buildMenuListTile(
                          title: MyStrings.smsNotifications,
                          subtitle: MyStrings.smsNotificationDescription,
                          iconPath: MyIcons.smsIcon,
                          trailing: IntrinsicWidth(
                            child: StatefulBuilder(
                              builder: (
                                BuildContext context,
                                StateSetter setState,
                              ) {
                                // Fetch the current notification status from SharedPreferences
                                bool isNotificationEnabled = controller.sn;

                                return Skeleton.replace(
                                  replace: true,
                                  replacement: Bone.button(
                                    uniRadius: Dimensions.radiusProMax,
                                    height: Dimensions.space35,
                                    width: Dimensions.space60,
                                  ),
                                  child: CupertinoSwitch(
                                    value: isNotificationEnabled,
                                    onChanged: (value) async {
                                      controller.toggleSn();
                                      controller.saveNotificationSettingsStatusInfo();
                                    },
                                  ),
                                );
                              },
                            ),
                          ),
                          onPressed: () {},
                        ),
                        _buildMenuListTile(
                          showBorder: false,
                          title: MyStrings.promotionalOffers,
                          subtitle: MyStrings.promotionalOfferDescription,
                          iconPath: MyIcons.couponOfferIcon,
                          trailing: IntrinsicWidth(
                            child: StatefulBuilder(
                              builder: (
                                BuildContext context,
                                StateSetter setState,
                              ) {
                                // Fetch the current notification status from SharedPreferences
                                bool isNotificationEnabled = controller.pmn;

                                return Skeleton.replace(
                                  replace: true,
                                  replacement: Bone.button(
                                    uniRadius: Dimensions.radiusProMax,
                                    height: Dimensions.space35,
                                    width: Dimensions.space60,
                                  ),
                                  child: CupertinoSwitch(
                                    value: isNotificationEnabled,
                                    onChanged: (value) async {
                                      controller.togglePmn();
                                      controller.saveNotificationSettingsStatusInfo();
                                    },
                                  ),
                                );
                              },
                            ),
                          ),
                          onPressed: () {},
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildMenuListTile({
    required String title,
    required String subtitle,
    required String iconPath,
    required VoidCallback onPressed,
    bool showBorder = true,
    Widget? trailing,
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
      trailing: trailing ??
          Icon(
            Icons.arrow_forward_ios_rounded,
            color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
            size: Dimensions.space16.w,
          ),
      onPressed: onPressed,
    );
  }
}
