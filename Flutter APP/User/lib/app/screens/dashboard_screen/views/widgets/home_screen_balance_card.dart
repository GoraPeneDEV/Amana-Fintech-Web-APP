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
        boxShadow: [
          BoxShadow(
            color: MyColor.getPrimaryColor().withValues(alpha: 0.25),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
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
                  padding: EdgeInsets.zero,
                  icon: CustomAppCard(
                    height: Dimensions.space40,
                    width: Dimensions.space40,
                    showBorder: false,
                    radius: Dimensions.radiusProMax,
                    backgroundColor: MyColor.black.withValues(alpha: 0.5),
                    padding: EdgeInsets.all(Dimensions.space8),
                    child: MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.walletQrCodeIcon,
                      color: MyColor.getWhiteColor(),
                      width: Dimensions.space22.w,
                      height: Dimensions.space22.w,
                    ),
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
                        AnimatedSwitcher(
                          duration: const Duration(milliseconds: 250),
                          transitionBuilder: (child, animation) => FadeTransition(
                            opacity: animation,
                            child: ScaleTransition(scale: animation, child: child),
                          ),
                          child: Text(
                            homeController.isBalanceVisible ? MyUtils.getUserAmount(homeController.accountBalanceFormatted) : "•••••••••",
                            key: ValueKey(homeController.isBalanceVisible),
                            overflow: TextOverflow.ellipsis,
                            style: MyTextStyle.balanceCardTextStyle.copyWith(
                              color: MyColor.getWhiteColor(),
                              fontSize: homeController.isBalanceVisible ? Dimensions.space35.sp : Dimensions.space50.sp,
                            ),
                            maxLines: 1,
                          ),
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
                            child: AnimatedSwitcher(
                              duration: const Duration(milliseconds: 250),
                              transitionBuilder: (child, animation) => FadeTransition(
                                opacity: animation,
                                child: ScaleTransition(scale: animation, child: child),
                              ),
                              child: Icon(
                                homeController.isBalanceVisible == true ? CupertinoIcons.eye : CupertinoIcons.eye_slash,
                                key: ValueKey(homeController.isBalanceVisible),
                                color: MyColor.getWhiteColor(),
                                size: Dimensions.space30,
                              ),
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
          spaceDown(Dimensions.space16),
        ],
      ),
    );
  }
}
