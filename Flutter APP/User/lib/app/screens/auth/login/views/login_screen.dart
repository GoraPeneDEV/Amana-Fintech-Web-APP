import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/annotated_region/annotated_region_widget.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/app/screens/auth/login/controller/login_controller.dart';
import 'package:ovopay/app/screens/auth/login/views/widgtes/login_reg_form_widgets.dart';
import 'package:ovopay/core/data/repositories/auth/login_repo.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:ovopay/environment.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final formKey = GlobalKey<FormState>();

  @override
  void initState() {
    Get.put(LoginRepo());
    Get.put(LoginController(loginRepo: Get.find()));
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return WillPopWidget(
      child: AnnotatedRegionWidget(
        statusBarBrightness: MyUtils.getOppositeBrightness(
          MyColor.getPrimaryColor(),
        ),
        statusBarIconBrightness: MyUtils.getOppositeBrightness(
          MyColor.getPrimaryColor(),
        ),
        systemNavigationBarIconBrightness: MyUtils.getOppositeBrightness(
          MyColor.getPrimaryColor(),
        ),
        statusBarColor: MyColor.getPrimaryColor(),
        systemNavigationBarColor: MyColor.getPrimaryColor(),
        child: Scaffold(
          backgroundColor: MyColor.white,
          body: GetBuilder<LoginController>(
            builder: (controller) => SingleChildScrollView(
              physics: ClampingScrollPhysics(),
              clipBehavior: Clip.none,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    decoration: BoxDecoration(
                      color: MyColor.getPrimaryColor(),
                    ),
                    padding: EdgeInsetsDirectional.symmetric(
                      horizontal: Dimensions.space15.sp,
                    ),
                    child: Padding(
                      padding: EdgeInsetsDirectional.only(
                        top: MediaQuery.viewPaddingOf(context).top,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          spaceDown(Dimensions.space20),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              //Logo
                              MyAssetImageWidget(
                                assetPath: MyImages.appLogoWhite,
                                boxFit: BoxFit.contain,
                                width: (Dimensions.space100 + Dimensions.space50).sp,
                                height: null,
                              ),
                              //Language
                              Visibility(
                                visible: SharedPreferenceService.isSupportMultiLanguage(),
                                child: Align(
                                  alignment: AlignmentDirectional.centerEnd,
                                  child: GestureDetector(
                                    onTap: () {
                                      Get.toNamed(
                                        RouteHelper.languageScreen,
                                      );
                                    },
                                    child: Container(
                                      padding: EdgeInsetsDirectional.symmetric(
                                        horizontal: Dimensions.space8.w,
                                        vertical: Dimensions.space4.w,
                                      ),
                                      decoration: BoxDecoration(
                                        border: Border.all(
                                          color: MyColor.getBorderColor().withValues(alpha: 0.5),
                                          width: Dimensions.space2.w,
                                        ),
                                        borderRadius: BorderRadius.circular(
                                          Dimensions.cardRadius,
                                        ),
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
                                              size: 16.h,
                                              color: MyColor.getWhiteColor(),
                                            )
                                          else
                                            MyNetworkImageWidget(
                                              imageUrl: SharedPreferenceService.getString(
                                                SharedPreferenceService.languageImagePath,
                                                defaultValue: '',
                                              ),
                                              width: 16.w,
                                              height: 16.h,
                                            ),
                                          spaceSide(Dimensions.space5),
                                          Text(
                                            SharedPreferenceService.getString(
                                              SharedPreferenceService.languageCode,
                                              defaultValue: Environment.defaultLangCode.toUpperCase(),
                                            ).toUpperCase(),
                                            style: MyTextStyle.sectionSubTitle1.copyWith(
                                              fontWeight: FontWeight.bold,
                                              color: MyColor.getWhiteColor(),
                                            ),
                                          ),
                                          Icon(
                                            Icons.keyboard_arrow_down_rounded,
                                            color: MyColor.getWhiteColor(),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          spaceDown(Dimensions.space30),
                          Text(
                            SharedPreferenceService.getRememberMe() ? MyStrings.welcomeBack.tr : MyStrings.loginHeader.tr,
                            style: MyTextStyle.headerH1,
                          ),
                          const SizedBox(height: Dimensions.space8),
                          Text(
                            SharedPreferenceService.getRememberMe() ? MyStrings.loginSubTitle2.tr : MyStrings.loginSubTitle.tr,
                            style: MyTextStyle.bodyTextStyle1.copyWith(
                              color: MyColor.getWhiteColor(),
                            ),
                          ),
                          24.verticalSpace,
                        ],
                      ),
                    ),
                  ),
                  Padding(
                    padding: EdgeInsetsDirectional.symmetric(
                      horizontal: Dimensions.space16.sp,
                    ),
                    child: Form(
                      key: formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          spaceDown(Dimensions.space30),
                          LoginRegFormsWidgets(),
                          spaceDown(Dimensions.space30),
                          CustomElevatedBtn(
                            radius: Dimensions.largeRadius.r,
                            isLoading: controller.isSubmitLoading,
                            bgColor: MyColor.getPrimaryColor(),
                            text: SharedPreferenceService.getRememberMe() ? MyStrings.login.tr : MyStrings.continueText.tr,
                            onTap: () {
                              if (formKey.currentState!.validate()) {
                                if (SharedPreferenceService.getRememberMe()) {
                                  controller.loginUser();
                                } else {
                                  controller.registerUser();
                                }
                              }
                            },
                          ),
                          spaceDown(Dimensions.space100),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
