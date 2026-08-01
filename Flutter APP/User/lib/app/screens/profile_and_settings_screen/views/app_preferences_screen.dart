import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/environment.dart';
import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';
import '../../../components/image/my_network_image_widget.dart';

class AppPreferencesScreen extends StatelessWidget {
  const AppPreferencesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.appPreference,
      body: SingleChildScrollView(
        clipBehavior: Clip.none,
        child: Column(
          children: [
            CustomAppCard(
              padding: EdgeInsets.symmetric(horizontal: Dimensions.space16.w),
              child: Column(
                children: [
                  _buildMenuListTile(
                    title: MyStrings.language,
                    trailing: IntrinsicWidth(
                      child: Row(
                        children: [
                          if (SharedPreferenceService.getString(
                                SharedPreferenceService.languageImagePath,
                                defaultValue: "",
                              ) ==
                              "")
                            Icon(
                              Icons.g_translate,
                              size: 16.sp,
                              color: MyColor.getHeaderTextColor(),
                            )
                          else
                            MyNetworkImageWidget(
                              imageUrl: SharedPreferenceService.getString(
                                SharedPreferenceService.languageImagePath,
                                defaultValue: '',
                              ),
                              width: 16.sp,
                              height: 16.sp,
                            ),
                          spaceSide(Dimensions.space5),
                          Text(
                            SharedPreferenceService.getString(
                              SharedPreferenceService.languageCode,
                              defaultValue: Environment.defaultLangCode.toUpperCase(),
                            ).toUpperCase(),
                            style: MyTextStyle.sectionSubTitle1.copyWith(
                              fontWeight: FontWeight.bold,
                              color: MyColor.getHeaderTextColor(),
                            ),
                          ),
                          Icon(
                            Icons.keyboard_arrow_down_rounded,
                            size: Dimensions.space15.w,
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ],
                      ),
                    ),
                    onPressed: () => Get.toNamed(RouteHelper.languageScreen),
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
    VoidCallback? onPressed,
    Widget? trailing,
    bool showBorder = true,
  }) {
    return CustomListTileCard(
      showLeading: false,
      showBorder: showBorder,
      padding: EdgeInsets.symmetric(vertical: Dimensions.space17),
      title: title.tr,
      titleStyle: MyTextStyle.sectionTitle2.copyWith(
        color: MyColor.getHeaderTextColor(),
      ),
      leading: null,
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
