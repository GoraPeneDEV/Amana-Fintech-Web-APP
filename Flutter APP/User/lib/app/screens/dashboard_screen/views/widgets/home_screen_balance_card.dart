import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/core/data/models/global/qr_code/scan_qr_code_response_model.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

class HomeScreenBalanceCard extends StatelessWidget {
  const HomeScreenBalanceCard({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16.0),
      decoration: BoxDecoration(
        image: const DecorationImage(
          image: AssetImage(MyImages.balanceCardBgImage),
          fit: BoxFit.cover,
        ),
        borderRadius: BorderRadius.circular(Dimensions.cardExtraRadius.r),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: EdgeInsetsDirectional.symmetric(
              horizontal: Dimensions.space12.w,
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                //Wallet
                Row(
                  children: [
                    MyAssetImageWidget(
                      // color: MyColor.getPrimaryColor(),
                      isSvg: true,
                      assetPath: MyIcons.walletIcon,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                    ),
                    spaceSide(Dimensions.space8),
                    Text(
                      MyStrings.yourWalletBalance.tr,
                      style: MyTextStyle.bodyTextStyle1.copyWith(
                        color: MyColor.getWhiteColor(),
                      ),
                    ),
                  ],
                ),

                // QR Code with PopupMenuButton
                PopupMenuButton<String>(
                  surfaceTintColor: Colors.transparent,
                  icon: MyAssetImageWidget(
                    // color: MyColor.getPrimaryColor(),
                    isSvg: true,
                    assetPath: MyIcons.walletQrCodeIcon,
                    width: Dimensions.space40.w,
                    height: Dimensions.space40.w,
                  ),
                  position: PopupMenuPosition.under,
                  color: MyColor.getScreenBgColor(),
                  // shadowColor: MyColor.transparentColor,
                  onSelected: (value) {
                    if (value == "scanQrCode") {
                      // Navigate to Scan QR Code Screen
                      Get.toNamed(RouteHelper.scanQrCodeScreen)?.then((v) {
                        ScanQrCodeResponseModel scanQrCodeResponseModel = v as ScanQrCodeResponseModel;
                        printE(scanQrCodeResponseModel.data?.userType);
                        printW(
                          scanQrCodeResponseModel.data?.userData?.getUserMobileNo(),
                        );
                        if (scanQrCodeResponseModel.data?.userType == AppStatus.USER_TYPE_USER) {
                          Get.toNamed(
                            RouteHelper.sendMoneyScreen,
                            arguments: scanQrCodeResponseModel.data?.userData,
                          );
                        } else if (scanQrCodeResponseModel.data?.userType == AppStatus.USER_TYPE_AGENT) {
                          Get.toNamed(
                            RouteHelper.cashOutScreen,
                            arguments: scanQrCodeResponseModel.data?.userData,
                          );
                        } else if (scanQrCodeResponseModel.data?.userType == AppStatus.USER_TYPE_MERCHANT) {
                          Get.toNamed(
                            RouteHelper.paymentScreen,
                            arguments: scanQrCodeResponseModel.data?.userData,
                          );
                        }
                      });
                    } else if (value == "myQrCode") {
                      // Navigate to My QR Code Screen
                      Get.toNamed(RouteHelper.myQrCodeScreen);
                    } else if (value == "qrCodeLogin") {
                      // Navigate to Qr code Login Screen
                      Get.toNamed(RouteHelper.qrCodeLoginScreen);
                    }
                  },
                  itemBuilder: (context) => [
                    PopupMenuItem(
                      value: "myQrCode",
                      child: Row(
                        children: [
                          Icon(
                            Icons.qr_code, // Replace with your desired icon
                            color: MyColor.getHeaderTextColor(),
                          ),
                          spaceSide(
                            Dimensions.space8,
                          ), // Spacing between icon and text
                          Text(
                            MyStrings.myQrCode,
                            style: MyTextStyle.bodyTextStyle1.copyWith(
                              color: MyColor.getHeaderTextColor(),
                            ),
                          ),
                        ],
                      ),
                    ),
                    PopupMenuItem(
                      value: "scanQrCode",
                      child: Row(
                        children: [
                          Icon(
                            Icons.qr_code_scanner, // Replace with your desired icon
                            color: MyColor.getHeaderTextColor(),
                          ),
                          spaceSide(
                            Dimensions.space8,
                          ), // Spacing between icon and text
                          Text(
                            MyStrings.scanQrCode,
                            style: MyTextStyle.bodyTextStyle1.copyWith(
                              color: MyColor.getHeaderTextColor(),
                            ),
                          ),
                        ],
                      ),
                    ),
                    if (SharedPreferenceService.isSupportQrCodeLogin()) ...[
                      PopupMenuItem(
                        value: "qrCodeLogin",
                        child: Row(
                          children: [
                            MyAssetImageWidget(
                              isSvg: true,
                              assetPath: MyIcons.walletQrCodeIcon,
                              width: Dimensions.space22.w,
                              height: Dimensions.space22.w,
                              color: MyColor.getHeaderTextColor(),
                            ),
                            spaceSide(
                              Dimensions.space8,
                            ), // Spacing between icon and text
                            Text(
                              MyStrings.qrCodeLogin,
                              style: MyTextStyle.bodyTextStyle1.copyWith(
                                color: MyColor.getHeaderTextColor(),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ),
          spaceDown(Dimensions.space3),
          GetBuilder<HomeController>(
            builder: (homeController) {
              return InkWell(
                onTap: () {
                  homeController.toggleBalanceVisibility();
                },
                child: Container(
                  height: Dimensions.space50,
                  padding: EdgeInsetsDirectional.symmetric(
                    horizontal: Dimensions.space12.w,
                  ),
                  child: FittedBox(
                    fit: BoxFit.scaleDown,
                    child: Row(
                      children: [
                        Text(
                          homeController.isBalanceVisible ? MyUtils.getUserAmount(homeController.accountBalanceFormatted) : "•••••••••",
                          overflow: TextOverflow.ellipsis,
                          style: MyTextStyle.balanceCardTextStyle.copyWith(
                            color: MyColor.getWhiteColor(),
                            fontSize: homeController.isBalanceVisible ? Dimensions.space35.sp : Dimensions.space50.sp,
                          ),
                          maxLines: 1,
                        ),
                        spaceSide(Dimensions.space10),
                        CustomAppCard(
                          height: Dimensions.space40,
                          width: Dimensions.space40,
                          showBorder: false,
                          radius: Dimensions.radiusProMax,
                          backgroundColor: MyColor.black.withValues(alpha: 0.5),
                          padding: EdgeInsets.all(Dimensions.space8),
                          child: FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Icon(
                              homeController.isBalanceVisible == true ? CupertinoIcons.eye : CupertinoIcons.eye_slash,
                              color: MyColor.getWhiteColor(),
                              size: Dimensions.space30,
                            ),
                          ),
                        )
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
          if (SharedPreferenceService.getModuleStatusByKey("add_money") ||
              SharedPreferenceService.getModuleStatusByKey("send_money") ||
              SharedPreferenceService.getModuleStatusByKey("make_payment") ||
              SharedPreferenceService.getModuleStatusByKey("cash_out") ||
              SharedPreferenceService.getModuleStatusByKey(
                "request_money",
              )) ...[
            spaceDown(Dimensions.space20),

            // Action Icons
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                if (SharedPreferenceService.getModuleStatusByKey(
                  "add_money",
                )) ...[
                  Flexible(
                    child: buildBalanceCardActionIcon(
                      iconImage: MyIcons.walletAddIcon,
                      titleText: MyStrings.addMoney,
                      onTap: () {
                        Get.toNamed(RouteHelper.addMoneyScreen);
                      },
                    ),
                  ),
                ],
                if (SharedPreferenceService.getModuleStatusByKey(
                  "send_money",
                )) ...[
                  Flexible(
                    child: buildBalanceCardActionIcon(
                      iconImage: MyIcons.sendIcon,
                      titleText: MyStrings.sendMoney,
                      onTap: () {
                        Get.toNamed(RouteHelper.sendMoneyScreen);
                      },
                    ),
                  ),
                ],
                if (SharedPreferenceService.getModuleStatusByKey(
                  "cash_out",
                )) ...[
                  Flexible(
                    child: buildBalanceCardActionIcon(
                      iconImage: MyIcons.cashOutIcon,
                      titleText: MyStrings.cashOut,
                      onTap: () {
                        Get.toNamed(RouteHelper.cashOutScreen);
                      },
                    ),
                  ),
                ],
                if (SharedPreferenceService.getModuleStatusByKey(
                  "make_payment",
                )) ...[
                  Flexible(
                    child: buildBalanceCardActionIcon(
                      iconImage: MyIcons.paymentIcon,
                      titleText: MyStrings.payment,
                      onTap: () {
                        Get.toNamed(RouteHelper.paymentScreen);
                      },
                    ),
                  ),
                ],
                if (SharedPreferenceService.getModuleStatusByKey(
                  "request_money",
                )) ...[
                  Flexible(
                    child: buildBalanceCardActionIcon(
                      iconImage: MyIcons.requestIcon,
                      titleText: MyStrings.requestMoney,
                      onTap: () {
                        Get.toNamed(RouteHelper.requestMoneyScreen);
                      },
                    ),
                  ),
                ],
              ],
            ),
          ] else ...[
            spaceDown(Dimensions.space10),
          ],
        ],
      ),
    );
  }

  InkWell buildBalanceCardActionIcon({
    String iconImage = "",
    String titleText = "",
    VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Skeleton.replace(
            replace: true,
            replacement: Bone.square(size: Dimensions.space40.h),
            child: CustomAppCard(
              showBorder: false,
              backgroundColor: MyColor.black.withValues(alpha: 0.5),
              padding: EdgeInsets.all(Dimensions.space10),
              width: Dimensions.space40.h,
              height: Dimensions.space40.h,
              radius: Dimensions.space12,
              child: MyAssetImageWidget(
                isSvg: true,
                assetPath: iconImage,
                color: MyColor.getWhiteColor(),
                width: Dimensions.space24.h,
                height: Dimensions.space24.h,
              ),
            ),
          ),
          spaceDown(Dimensions.space4),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              titleText.tr,
              textAlign: TextAlign.center,
              style: MyTextStyle.caption2Style.copyWith(
                fontSize: Dimensions.fontSmall,
                color: MyColor.getWhiteColor(),
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
