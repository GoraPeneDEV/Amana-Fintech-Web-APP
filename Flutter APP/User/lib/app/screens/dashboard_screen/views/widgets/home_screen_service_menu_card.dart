import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class MenuItem {
  final String icon;
  final String label;
  final bool isActive;
  final Color activeColor;
  final VoidCallback? onTap;

  MenuItem({
    this.onTap,
    required this.icon,
    required this.label,
    this.isActive = false,
    required this.activeColor,
  });
}

class HomeScreenServiceMenuCard extends StatefulWidget {
  const HomeScreenServiceMenuCard({super.key});

  @override
  State<HomeScreenServiceMenuCard> createState() => _HomeScreenServiceMenuCardState();
}

class _HomeScreenServiceMenuCardState extends State<HomeScreenServiceMenuCard> {
  bool isExpanded = false;
  static const int visibleItemCount = 11;

  final List<MenuItem> allServices = [
    MenuItem(
      icon: MyIcons.rechargeIcon,
      label: MyStrings.recharge,
      isActive: SharedPreferenceService.getModuleStatusByKey("mobile_recharge"),
      activeColor: MyColor.violateColor,
      onTap: () {
        Get.toNamed(RouteHelper.airTimeScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.cardIcon,
      label: MyStrings.card,
      isActive: SharedPreferenceService.getModuleStatusByKey("virtual_card"),
      activeColor: MyColor.greenLightColor,
      onTap: () {
        Get.toNamed(RouteHelper.virtualCardsScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.billPay,
      label: MyStrings.billPay,
      isActive: SharedPreferenceService.getModuleStatusByKey("utility_bill"),
      activeColor: MyColor.indigoColor,
      onTap: () {
        Get.toNamed(RouteHelper.billPayScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.giftIcon,
      label: MyStrings.giftCard,
      isActive: SharedPreferenceService.getModuleStatusByKey("gift_card"),
      activeColor: MyColor.skyBlueColor,
      onTap: () {
        Get.toNamed(RouteHelper.giftCardScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.educationIcon,
      label: MyStrings.education,
      isActive: SharedPreferenceService.getModuleStatusByKey("education_fee"),
      activeColor: MyColor.violateColor,
      onTap: () {
        Get.toNamed(RouteHelper.educationFeeScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.bankTransferIcon,
      label: MyStrings.bankTransfer,
      isActive: SharedPreferenceService.getModuleStatusByKey("bank_transfer"),
      activeColor: MyColor.goldenColor,
      onTap: () {
        Get.toNamed(RouteHelper.bankTransferScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.savingsIcon,
      label: MyStrings.microFinance,
      isActive: SharedPreferenceService.getModuleStatusByKey("microfinance"),
      activeColor: MyColor.greenLightColor,
      onTap: () {
        Get.toNamed(RouteHelper.microFinanceScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.investment,
      label: MyStrings.investment,
      isActive: SharedPreferenceService.getModuleStatusByKey("investment"),
      activeColor: MyColor.violateColor,
      onTap: () {
        Get.toNamed(RouteHelper.investmentScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.donationIcon,
      label: MyStrings.donation,
      isActive: SharedPreferenceService.getModuleStatusByKey("donation"),
      activeColor: MyColor.redLightColor,
      onTap: () {
        Get.toNamed(RouteHelper.donationScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.requestIcon,
      label: MyStrings.requestMoney,
      isActive: SharedPreferenceService.getModuleStatusByKey("request_money"),
      activeColor: MyColor.indigoColor,
      onTap: () {
        Get.toNamed(RouteHelper.requestMoneyScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.sendIcon,
      label: MyStrings.sendMoney,
      isActive: SharedPreferenceService.getModuleStatusByKey("send_money"),
      activeColor: MyColor.goldenColor,
      onTap: () {
        Get.toNamed(RouteHelper.sendMoneyScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.paymentIcon,
      label: MyStrings.payment,
      isActive: SharedPreferenceService.getModuleStatusByKey("make_payment"),
      activeColor: MyColor.greenLightColor,
      onTap: () {
        Get.toNamed(RouteHelper.paymentScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.cashOutIcon,
      label: MyStrings.cashOut,
      isActive: SharedPreferenceService.getModuleStatusByKey("cash_out"),
      activeColor: MyColor.violateColor,
      onTap: () {
        Get.toNamed(RouteHelper.cashOutScreen);
      },
    ),
    MenuItem(
      icon: MyIcons.walletAddIcon,
      label: MyStrings.addMoney,
      isActive: SharedPreferenceService.getModuleStatusByKey("add_money"),
      activeColor: MyColor.primary,
      onTap: () {
        Get.toNamed(RouteHelper.addMoneyScreen);
      },
    ),
  ];
  List<MenuItem> get getAllServices {
    return allServices.where((v) => v.isActive).toList();
  }

  List<MenuItem> get visibleServices {
    if (getAllServices.isEmpty) return [];
    return isExpanded
        ? getAllServices
        : getAllServices.sublist(
            0,
            visibleItemCount > getAllServices.length ? getAllServices.length : visibleItemCount,
          );
  }

  List<MenuItem> get getVisibleServices {
    return visibleServices.where((v) => v.isActive).toList();
  }

  @override
  Widget build(BuildContext context) {
    return getVisibleServices.isNotEmpty
        ? Padding(
            padding: EdgeInsetsDirectional.only(top: Dimensions.space20.w),
            child: CustomAppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  HeaderText(
                    text: MyStrings.otherServices.tr,
                    textStyle: MyTextStyle.sectionTitle2.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                  ),
                  spaceDown(Dimensions.space16),
                  //Action Icons
                  GridView.count(
                    padding: EdgeInsets.zero,
                    scrollDirection: Axis.vertical,
                    physics: const NeverScrollableScrollPhysics(),
                    shrinkWrap: true,
                    // crossAxisCount: 4,
                    crossAxisCount: ScreenUtil().screenWidth < 600 ? 4 : 6, // Set to 4 columns
                    crossAxisSpacing: 10,
                    mainAxisSpacing: 10,
                    children: [
                      // Display visible services
                      ...getVisibleServices.map(
                        (service) => buildServiceItem(service),
                      ),
                      // Show "Less" button if expanded
                      if (isExpanded)
                        GestureDetector(
                          onTap: () {
                            setState(() {
                              isExpanded = false;
                            });
                          },
                          child: buildServiceItem(
                            MenuItem(
                              icon: MyIcons.moreIcon,
                              label: MyStrings.less,
                              isActive: true,
                              activeColor: MyColor.orangeColor,
                            ),
                          ),
                        ),
                      // Show "Other" button if not expanded and there are more items
                      if (!isExpanded && getAllServices.length > visibleItemCount)
                        GestureDetector(
                          onTap: () {
                            setState(() {
                              isExpanded = true;
                            });
                          },
                          child: buildServiceItem(
                            MenuItem(
                              icon: MyIcons.moreIcon,
                              label: MyStrings.more,
                              isActive: true,
                              activeColor: MyColor.orangeColor,
                            ),
                          ),
                        ),
                    ],
                  ),
                ],
              ),
            ),
          )
        : SizedBox.shrink();
  }

  Widget buildServiceItem(MenuItem service) {
    return GestureDetector(
      onTap: service.onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          CustomAppCard(
            backgroundColor: service.activeColor.withValues(alpha: 0.1),
            borderColor: service.activeColor,
            padding: EdgeInsets.all(Dimensions.space12.w),
            width: Dimensions.space48.h,
            height: Dimensions.space48.h,
            radius: Dimensions.largeRadius,
            child: MyAssetImageWidget(
              isSvg: true,
              assetPath: service.icon,
              width: Dimensions.space24.w,
              height: Dimensions.space24.w,
              color: service.activeColor,
            ),
          ),
          spaceDown(Dimensions.space4),
          Flexible(
            child: FittedBox(
              fit: BoxFit.scaleDown,
              child: Text(
                service.label.tr,
                textAlign: TextAlign.center,
                overflow: TextOverflow.ellipsis,
                style: MyTextStyle.caption1Style.copyWith(
                  fontSize: Dimensions.fontSmall,
                  color: MyColor.getBodyTextColor(),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
