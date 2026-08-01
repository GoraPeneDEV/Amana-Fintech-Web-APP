import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/controller/profile_controller.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/widgets/profile_image_with_upload_button_widget.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../core/utils/util_exporter.dart';

class ProfileEditScreen extends StatefulWidget {
  const ProfileEditScreen({super.key});

  @override
  State<ProfileEditScreen> createState() => _ProfileEditScreenState();
}

class _ProfileEditScreenState extends State<ProfileEditScreen> {
  @override
  void initState() {
    Get.put(ProfileRepo());
    final controller = Get.put(ProfileController(profileRepo: Get.find()));

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadProfileInfo();
    });
  }

  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.editProfile,
      body: GetBuilder<ProfileController>(
        builder: (controller) {
          return SingleChildScrollView(
            clipBehavior: Clip.none,
            child: Skeletonizer(
              enabled: controller.isLoading,
              child: Column(
                children: [
                  CustomAppCard(
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            ProfileImageWithUploadButtonWidget(
                              imageUrl: controller.imageUrl,
                              imageAlt: SharedPreferenceService.getUserFullName(),
                              onChanged: (File file) {
                                controller.imageFile = file;
                              },
                            ),
                            CustomElevatedBtn(
                              bgColor: MyColor.getScreenBgColor(),
                              borderColor: MyColor.getBorderColor(),
                              textColor: MyColor.getDarkColor(),
                              width: Dimensions.space80.w,
                              height: Dimensions.space40.h,
                              text: MyStrings.cancel,
                              onTap: () {
                                Get.back();
                              },
                            ),
                          ],
                        ),
                        spaceDown(Dimensions.space25),
                        Form(
                          key: formKey,
                          child: Column(
                            children: [
                              //Text Field
                              RoundedTextField(
                                labelText: MyStrings.firstName.tr,
                                hintText: 'e.g. John',
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.name,
                                validator: (value) {
                                  if (value.toString().isEmpty) {
                                    return MyStrings.kFirstNameNullError.tr;
                                  } else {
                                    return null;
                                  }
                                },
                                controller: controller.firstNameController,
                                focusNode: controller.firstNameFocusNode,
                                nextFocus: controller.lastNameFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                labelText: MyStrings.lastName.tr,
                                hintText: 'e.g. Doe',
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.name,
                                validator: (value) {
                                  if (value.toString().isEmpty) {
                                    return MyStrings.kFirstNameNullError.tr;
                                  } else {
                                    return null;
                                  }
                                },
                                controller: controller.lastNameController,
                                focusNode: controller.lastNameFocusNode,
                                nextFocus: controller.addressFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                labelText: MyStrings.address.tr,
                                hintText: MyStrings.enterYourAddress.tr,
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.name,
                                controller: controller.addressController,
                                focusNode: controller.addressFocusNode,
                                nextFocus: controller.stateFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                labelText: MyStrings.state.tr,
                                hintText: MyStrings.enterYourState.tr,
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.name,
                                controller: controller.stateController,
                                focusNode: controller.stateFocusNode,
                                nextFocus: controller.zipCodeFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                labelText: MyStrings.zipCode.tr,
                                hintText: MyStrings.enterYourZipCode.tr,
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.name,
                                controller: controller.zipCodeController,
                                focusNode: controller.zipCodeFocusNode,
                                nextFocus: controller.cityFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                labelText: MyStrings.city.tr,
                                hintText: MyStrings.enterYourCity.tr,
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.name,
                                controller: controller.cityController,
                                focusNode: controller.cityFocusNode,
                                nextFocus: controller.emailFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                forceFillColor: false,
                                readOnly: true,
                                labelText: MyStrings.email.tr,
                                hintText: 'e.g. user@example.com',
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.emailAddress,
                                controller: controller.emailController,
                                focusNode: controller.addressFocusNode,
                              ),
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                forceFillColor: false,
                                readOnly: true,
                                labelText: MyStrings.phoneNumber.tr,
                                hintText: MyStrings.phoneNumber.tr,
                                textInputAction: TextInputAction.next,
                                textInputFormatter: [
                                  FilteringTextInputFormatter.allow(
                                    RegExp(r'[0-9]'),
                                  ),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                                keyboardType: TextInputType.phone,
                                controller: controller.mobileNoController,
                                focusNode: controller.addressFocusNode,
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
                                            "+${SharedPreferenceService.getDialCode()}",
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
                              spaceDown(Dimensions.space20),
                              RoundedTextField(
                                forceFillColor: false,
                                readOnly: true,
                                labelText: MyStrings.country.tr,
                                hintText: MyStrings.selectACountry.tr,
                                textInputAction: TextInputAction.next,
                                keyboardType: TextInputType.text,
                                controller: controller.countryController,
                                onTap: () {},
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  spaceDown(Dimensions.space20),
                  CustomElevatedBtn(
                    isLoading: controller.isSubmitLoading,
                    radius: Dimensions.largeRadius.r,
                    bgColor: MyColor.getPrimaryColor(),
                    text: MyStrings.save,
                    onTap: () {
                      if (formKey.currentState!.validate()) {
                        controller.updateProfile();
                      }
                    },
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
