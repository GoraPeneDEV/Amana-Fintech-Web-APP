import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:dots_indicator/dots_indicator.dart';
import 'package:ovopay/app/components/annotated_region/annotated_region_widget.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/screens/onboard/controller/onboard_controller.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/environment.dart';

import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';
import 'widget/onboard_body.dart';

class OnboardScreen extends StatefulWidget {
  const OnboardScreen({super.key});

  @override
  State<OnboardScreen> createState() => _OnboardScreenState();
}

class _OnboardScreenState extends State<OnboardScreen> {
  int currentIndex = 0;

  @override
  void initState() {
    super.initState();
    Get.put(OnBoardController());
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<OnBoardController>(
      builder: (controller) {
        return AnnotatedRegionWidget(
          statusBarBrightness: MyUtils.getOppositeBrightness(
            MyColor.screenBGColor,
          ),
          statusBarIconBrightness: MyUtils.getOppositeBrightness(
            MyColor.screenBGColor,
          ),
          systemNavigationBarIconBrightness: MyUtils.getOppositeBrightness(
            MyColor.screenBGColor,
          ),
          statusBarColor: MyColor.screenBGColor,
          systemNavigationBarColor: MyColor.screenBGColor,
          child: Scaffold(
            backgroundColor: MyColor.screenBGColor,
            body: SafeArea(
              child: Padding(
                padding: Dimensions.screenPadding,
                child: Column(
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Visibility(
                          visible: SharedPreferenceService.isSupportMultiLanguage(),
                          child: Align(
                            alignment: AlignmentDirectional.centerEnd,
                            child: GestureDetector(
                              onTap: () {
                                Get.toNamed(RouteHelper.languageScreen);
                              },
                              child: Container(
                                padding: const EdgeInsetsDirectional.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                decoration: BoxDecoration(
                                  border: Border.all(
                                    color: MyColor.getBorderColor(),
                                    width: 2,
                                  ),
                                  borderRadius: BorderRadius.circular(
                                    Dimensions.cardRadius,
                                  ),
                                  // boxShadow: MyUtils.getCardShadow(),
                                ),
                                child: Row(
                                  children: [
                                    if (SharedPreferenceService.getString(
                                          SharedPreferenceService.languageImagePath,
                                          defaultValue: "",
                                        ) ==
                                        "")
                                      Icon(
                                        Icons.g_translate,
                                        size: 16,
                                        color: MyColor.getPrimaryColor(),
                                      )
                                    else
                                      MyNetworkImageWidget(
                                        imageUrl: SharedPreferenceService.getString(
                                          SharedPreferenceService.languageImagePath,
                                          defaultValue: '',
                                        ),
                                        width: 16,
                                        height: 16,
                                      ),
                                    spaceSide(Dimensions.space5),
                                    Text(
                                      SharedPreferenceService.getString(
                                        SharedPreferenceService.languageCode,
                                        defaultValue: Environment.defaultLangCode.toUpperCase(),
                                      ).toUpperCase(),
                                      style: MyTextStyle.sectionSubTitle1.copyWith(
                                        fontWeight: FontWeight.bold,
                                        color: MyColor.getBodyTextColor(),
                                      ),
                                    ),
                                    Icon(
                                      Icons.keyboard_arrow_down_rounded,
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    Expanded(
                      flex: 1,
                      child: PageView.builder(
                        controller: controller.controller,
                        itemCount: controller.appOnboardDataList.length,
                        onPageChanged: (i) {
                          controller.setCurrentIndex(i);
                        },
                        itemBuilder: (_, index) {
                          return OnBoardBody(
                            data: controller.appOnboardDataList[index],
                          );
                        },
                      ),
                    ),
                    spaceDown(Dimensions.space5),
                    DotsIndicator(
                      dotsCount: controller.appOnboardDataList.length,
                      position: controller.currentIndex.toDouble(),
                      mainAxisSize: MainAxisSize.min,
                      decorator: DotsDecorator(
                        size: const Size.square(9.0),
                        activeSize: const Size(25.0, 9.0),
                        activeColor: MyColor.getPrimaryColor(),
                        color: MyColor.getBorderColor(),
                        activeShape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(5.0),
                        ),
                      ),
                    ),
                    spaceDown(Dimensions.space15),
                    CustomElevatedBtn(
                      radius: Dimensions.largeRadius.r,
                      bgColor: MyColor.getPrimaryColor(),
                      text: MyStrings.login,
                      onTap: () async {
                        await SharedPreferenceService.setOnBoardStatus(
                          false,
                        ).whenComplete(() {
                          Get.toNamed(RouteHelper.loginScreen);
                        });
                      },
                    ),
                    spaceDown(Dimensions.space15),
                    CustomElevatedBtn(
                      elevation: 0,
                      radius: Dimensions.largeRadius.r,
                      bgColor: MyColor.getWhiteColor(),
                      textColor: MyColor.getDarkColor(),
                      text: MyStrings.register,
                      borderColor: MyColor.getBorderColor(),
                      shadowColor: MyColor.getWhiteColor(),
                      onTap: () async {
                        await SharedPreferenceService.setOnBoardStatus(
                          false,
                        ).whenComplete(() {
                          Get.toNamed(RouteHelper.loginScreen);
                        });
                      },
                    ),
                    spaceDown(Dimensions.space15),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
