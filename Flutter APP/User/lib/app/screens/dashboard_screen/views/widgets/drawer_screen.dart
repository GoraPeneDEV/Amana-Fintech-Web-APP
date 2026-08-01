import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/drawer/user_drawer_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';

class DrawerScreen extends StatelessWidget {
  const DrawerScreen({super.key, required this.dashboardKey});
  final GlobalKey<ScaffoldState> dashboardKey;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: MyColor.getWhiteColor(),
        borderRadius: BorderRadius.circular(Dimensions.drawerRadius.r),
      ),
      width: ScreenUtil().screenWidth / 1.15,
      child: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  Align(
                    alignment: AlignmentDirectional.topEnd,
                    child: Padding(
                      padding: const EdgeInsetsDirectional.only(
                        end: Dimensions.space15,
                      ),
                      child: IconButton(
                        padding: EdgeInsets.all(Dimensions.space3.w),
                        style: IconButton.styleFrom(),
                        onPressed: () {
                          dashboardKey.currentState?.closeDrawer();
                        },
                        icon: MyAssetImageWidget(
                          color: MyColor.getPrimaryColor(),
                          isSvg: true,
                          assetPath: MyIcons.closeButton,
                          width: Dimensions.space40.w,
                          height: Dimensions.space40.w,
                        ),
                      ),
                    ),
                  ),
                  DrawerUserCard(
                    image: SharedPreferenceService.getUserImage(),
                    fullName: SharedPreferenceService.getUserFullName(),
                    username: SharedPreferenceService.getUserPhoneNumber(),
                    rightWidget: GestureDetector(
                      onTap: () {
                        dashboardKey.currentState?.closeDrawer();
                        Get.toNamed(RouteHelper.myQrCodeScreen);
                      },
                      child: MyAssetImageWidget(
                        assetPath: MyIcons.walletQrCodeIcon,
                        color: MyColor.getPrimaryColor(),
                        width: Dimensions.space48.w,
                        height: Dimensions.space48.w,
                        isSvg: true,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            //Logout
            CustomAppCard(
              radius: Dimensions.largeRadius.r,
              backgroundColor: MyColor.getScreenBgColor(),
              margin: EdgeInsetsDirectional.symmetric(
                horizontal: Dimensions.space15,
              ),
              padding: EdgeInsetsDirectional.symmetric(
                horizontal: Dimensions.space15,
                vertical: Dimensions.space15,
              ),
              child: CustomListTileCard(
                padding: EdgeInsetsDirectional.zero,
                title: MyStrings.logout.tr,
                showBorder: false,
                titleStyle: MyTextStyle.sectionTitle.copyWith(
                  color: MyColor.getBodyTextColor(),
                ),
                leading: MyAssetImageWidget(
                  color: MyColor.getPrimaryColor(),
                  isSvg: true,
                  assetPath: MyIcons.logoutIcon,
                  width: Dimensions.space24.w,
                  height: Dimensions.space24.w,
                  boxFit: BoxFit.scaleDown,
                ),
                onPressed: () {
                  Get.offAllNamed(RouteHelper.loginScreen);
                },
              ),
            ),
            spaceDown(Dimensions.space10),
          ],
        ),
      ),
    );
  }
}
