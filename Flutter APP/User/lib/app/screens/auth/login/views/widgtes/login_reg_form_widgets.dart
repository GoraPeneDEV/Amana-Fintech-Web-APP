import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/default_text.dart';
import 'package:ovopay/app/screens/auth/login/controller/login_controller.dart';
import 'package:ovopay/app/screens/global/views/widgets/country_bottom_sheet.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/environment.dart';

import '../../../../../../core/utils/util_exporter.dart';

class LoginRegFormsWidgets extends StatelessWidget {
  const LoginRegFormsWidgets({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<LoginController>(
      builder: (controller) {
        return AnimatedSwitcher(
          duration: Duration(milliseconds: 300),
          transitionBuilder: (child, animation) {
            return FadeTransition(opacity: animation, child: child);
          },
          child: SharedPreferenceService.getRememberMe()
              ? Column(
                  children: [
                    //phone
                    RoundedTextField(
                      labelText: MyStrings.phoneNumber.tr,
                      hintText: MyStrings.phoneNumber.tr,
                      controller: controller.mobileController,
                      textInputAction: TextInputAction.next,
                      keyboardType: TextInputType.phone,
                      prefixIcon: IntrinsicWidth(
                        child: InkWell(
                          onTap: () {
                            CountryBottomSheet.countryBottomSheet(
                              context,
                              selectedCountry: controller.countryData,
                              onSelectedData: (v) {
                                controller.selectedCountryData(v);
                              },
                            );
                          },
                          child: Container(
                            padding: const EdgeInsetsDirectional.only(
                              start: Dimensions.space15,
                              end: Dimensions.space8,
                            ),
                            child: FittedBox(
                              fit: BoxFit.scaleDown,
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  MyNetworkImageWidget(
                                    width: Dimensions.space22.sp,
                                    height: Dimensions.space16.sp,
                                    boxFit: BoxFit.contain,
                                    imageUrl: UrlContainer.countryFlagImageLink.replaceAll(
                                      "{countryCode}",
                                      (controller.countryData?.code ?? Environment.defaultCountryCode).toLowerCase(),
                                    ),
                                  ),
                                  spaceSide(Dimensions.space5),
                                  Text(
                                    "+${controller.countryData?.dialCode ?? Environment.defaultPhoneDialCode}",
                                    style: MyTextStyle.bodyTextStyle2.copyWith(
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                  ),
                                  spaceSide(Dimensions.space8),
                                  Container(
                                    color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                                    width: 1.2.w,
                                    height: Dimensions.space25.h,
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                      textInputFormatter: [
                        FilteringTextInputFormatter.digitsOnly, // Allow only digits
                        LengthLimitingTextInputFormatter(
                          SharedPreferenceService.getMaxMobileNumberDigit(),
                        ), // Limit to 5 characters
                      ],
                      validator: (value) {
                        if (value.toString().isEmpty) {
                          return MyStrings.kPhoneNumberIsRequired.tr;
                        } else if (value.toString().length < SharedPreferenceService.getMaxMobileNumberDigit()) {
                          return '${MyStrings.kPhoneNumberDigitIsRequired.tr} ${SharedPreferenceService.getMaxMobileNumberDigit().toString()}';
                        } else {
                          return null;
                        }
                      },
                    ),
                    //PIN
                    spaceDown(Dimensions.space24),
                    RoundedTextField(
                      controller: controller.pinController,
                      labelText: MyStrings.pin,
                      hintText: MyStrings.enterYourPinCode,
                      textInputAction: TextInputAction.done,
                      keyboardType: TextInputType.number,
                      isPassword: true,
                      forceShowSuffixDesign: true,
                      suffixIcon: SharedPreferenceService.getBioMetricStatus()
                          ? Padding(
                              padding: EdgeInsetsDirectional.only(
                                end: Dimensions.space5.w,
                              ),
                              child: IconButton(
                                onPressed: () {
                                  controller.checkBiometric(
                                    fromLogin: true,
                                    onSuccess: () {
                                      controller.bioMetricLogin();
                                    },
                                  );
                                },
                                icon: MyAssetImageWidget(
                                  width: Dimensions.space40.w,
                                  height: Dimensions.space40.w,
                                  boxFit: BoxFit.contain,
                                  assetPath: Platform.isIOS ? MyIcons.loginFaceIdIcon : MyIcons.loginFingerPrintIcon,
                                  color: MyColor.getPrimaryColor(),
                                  isSvg: true,
                                ),
                              ),
                            )
                          : null,
                      textInputFormatter: [
                        FilteringTextInputFormatter.digitsOnly, // Allow only digits
                        LengthLimitingTextInputFormatter(
                          SharedPreferenceService.getMaxPinNumberDigit(),
                        ), // Limit to 5 characters
                      ],
                      validator: (value) {
                        if (value.toString().isEmpty) {
                          return MyStrings.kPinNumberError.tr;
                        } else if (value.toString().length < SharedPreferenceService.getMaxPinNumberDigit()) {
                          return MyStrings.kPinMaxNumberError.tr.rKv({
                            "digit": "${SharedPreferenceService.getMaxPinNumberDigit()}",
                          });
                        } else {
                          return null;
                        }
                      },
                    ),
                    spaceDown(Dimensions.space12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        InkWell(
                          onTap: () {
                            MyUtils.clearAllTypeFocusNodes();
                            controller.forgetPassword();
                          },
                          child: DefaultText(
                            text: MyStrings.forgetPin.tr,
                            textStyle: MyTextStyle.sectionSubTitle1.copyWith(
                              color: MyColor.error,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                )
              : Column(
                  children: [
                    //Country
                    RoundedTextField(
                      readOnly: true,
                      labelText: MyStrings.country.tr,
                      hintText: MyStrings.selectACountry.tr,
                      textInputAction: TextInputAction.next,
                      keyboardType: TextInputType.text,
                      controller: controller.countryController,
                      prefixIcon: Container(
                        margin: const EdgeInsetsDirectional.only(
                          start: Dimensions.space15,
                          end: Dimensions.space8,
                        ),
                        child: MyNetworkImageWidget(
                          width: Dimensions.space22.sp,
                          height: Dimensions.space16.sp,
                          boxFit: BoxFit.contain,
                          imageUrl: UrlContainer.countryFlagImageLink.replaceAll(
                            "{countryCode}",
                            (controller.countryData?.code ?? Environment.defaultCountryCode).toLowerCase(),
                          ),
                        ),
                      ),
                      suffixIcon: Icon(
                        Icons.keyboard_arrow_down_rounded,
                        color: MyColor.getBodyTextColor(),
                        size: Dimensions.space25.sp,
                      ),
                      onTap: () {
                        CountryBottomSheet.countryBottomSheet(
                          context,
                          selectedCountry: controller.countryData,
                          onSelectedData: (v) {
                            controller.selectedCountryData(v);
                          },
                        );
                      },
                    ),
                    spaceDown(Dimensions.space24),

                    //phone
                    RoundedTextField(
                      labelText: MyStrings.phoneNumber.tr,
                      hintText: MyStrings.phoneNumber.tr,
                      controller: controller.mobileController,
                      textInputAction: TextInputAction.next,
                      keyboardType: TextInputType.phone,
                      prefixIcon: IntrinsicWidth(
                        child: Container(
                          padding: const EdgeInsetsDirectional.only(
                            start: Dimensions.space15,
                            end: Dimensions.space8,
                          ),
                          child: FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  "+${controller.countryData?.dialCode ?? Environment.defaultPhoneDialCode}",
                                  style: MyTextStyle.bodyTextStyle2.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                                spaceSide(Dimensions.space8),
                                Container(
                                  color: MyColor.getBodyTextColor().withValues(alpha: 0.5),
                                  width: 1.2.w,
                                  height: Dimensions.space25.h,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      textInputFormatter: [
                        FilteringTextInputFormatter.digitsOnly, // Allow only digits
                        LengthLimitingTextInputFormatter(
                          SharedPreferenceService.getMaxMobileNumberDigit(),
                        ), // Limit to 5 characters
                      ],
                      validator: (value) {
                        if (value.toString().isEmpty) {
                          return MyStrings.kPhoneNumberIsRequired.tr;
                        } else if (value.toString().length < SharedPreferenceService.getMaxMobileNumberDigit()) {
                          return '${MyStrings.kPhoneNumberDigitIsRequired.tr} ${SharedPreferenceService.getMaxMobileNumberDigit().toString().tr}';
                        } else {
                          return null;
                        }
                      },
                    ),
                  ],
                ),
        );
      },
    );
  }
}
