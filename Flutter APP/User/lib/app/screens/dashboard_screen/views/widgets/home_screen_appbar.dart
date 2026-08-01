import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/annotated_region/annotated_region_widget.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class HomePageAppBar extends StatelessWidget implements PreferredSizeWidget {
  final GlobalKey<ScaffoldState> dashboardKey;
  const HomePageAppBar({super.key, required this.dashboardKey});
  @override
  Widget build(BuildContext context) {
    return AnnotatedRegionWidget(
      statusBarBrightness: Brightness.light,
      statusBarIconBrightness: Brightness.dark,
      statusBarColor: MyColor.getWhiteColor(),
      child: Container(
        color: MyColor.getWhiteColor(),
        padding: EdgeInsetsDirectional.only(
          start: Dimensions.space16.w,
          end: Dimensions.space16.w,
          top: MediaQuery.viewPaddingOf(context).top,
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            GestureDetector(
              onTap: () {
                // if (dashboardKey.currentState != null) {
                // dashboardKey.currentState!.openDrawer();
                // }
                Get.toNamed(RouteHelper.profileInformationScreen);
              },
              child: Row(
                children: [
                  MyNetworkImageWidget(
                    imageAlt: SharedPreferenceService.getUserFullName(),
                    radius: Dimensions.radiusMax.r,
                    imageUrl: SharedPreferenceService.getUserImage(),
                    width: Dimensions.space48.w,
                    height: Dimensions.space48.w,
                    boxFit: BoxFit.contain,
                    isProfile: true,
                  ),
                  spaceSide(Dimensions.space8),
                  Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      SelectableText(
                        "+${SharedPreferenceService.getDialCode()}${SharedPreferenceService.getUserPhoneNumber()}",
                        style: MyTextStyle.sectionSubTitle1.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                      Text(
                        SharedPreferenceService.getUserFullName(),
                        style: MyTextStyle.sectionTitle2.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            CustomAppCard(
              onPressed: () {
                Get.toNamed(RouteHelper.notificationScreen);
              },
              width: Dimensions.space40.w,
              height: Dimensions.space40.w,
              padding: EdgeInsetsDirectional.all(Dimensions.space8.w),
              radius: Dimensions.largeRadius.r,
              child: MyAssetImageWidget(
                isSvg: true,
                assetPath: MyIcons.notificationIcon,
                width: Dimensions.space24.h,
                height: Dimensions.space24.h,
                color: MyColor.getPrimaryColor(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Size get preferredSize => Size.fromHeight(70);
}
