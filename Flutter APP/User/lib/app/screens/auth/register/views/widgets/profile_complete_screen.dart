import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/packages/expandable_page_view/src/expandable_page_view.dart';
import 'package:ovopay/app/screens/auth/register/controller/registration_controller.dart';

import '../../../../../../core/data/services/service_exporter.dart';
import '../../../../../../core/utils/util_exporter.dart';

class ProfileCompleteScreen extends StatefulWidget {
  final PageController pageController;
  final int currentPage;
  final Function({int? goToPage}) nextPage;
  final Function({int? goToPage}) previousPage;

  const ProfileCompleteScreen({
    super.key,
    required this.pageController,
    required this.currentPage,
    required this.nextPage,
    required this.previousPage,
  });

  @override
  State<ProfileCompleteScreen> createState() => _ProfileCompleteScreenState();
}

class _ProfileCompleteScreenState extends State<ProfileCompleteScreen> {
  final formkey1 = GlobalKey<FormState>();
  final formkey2 = GlobalKey<FormState>();
  final formkey3 = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<RegistrationController>(
      builder: (controller) {
        return Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    CustomAppCard(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Steps
                          Row(
                            children: [
                              Expanded(
                                child: CustomAppCard(
                                  onPressed: () {
                                    widget.nextPage(goToPage: 0);
                                  },
                                  backgroundColor: MyColor.getPrimaryColor(),
                                  radius: Dimensions.radiusProMax.r,
                                  showBorder: false,
                                  width: double.infinity,
                                  height: Dimensions.space7,
                                  child: SizedBox.shrink(),
                                ),
                              ),
                              spaceSide(Dimensions.space10),
                              Expanded(
                                child: CustomAppCard(
                                  onPressed: () {
                                    widget.nextPage(goToPage: 1);
                                  },
                                  backgroundColor: widget.currentPage >= 1 ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                                  radius: Dimensions.radiusProMax.r,
                                  showBorder: false,
                                  width: double.infinity,
                                  height: Dimensions.space7,
                                  child: SizedBox.shrink(),
                                ),
                              ),
                              spaceSide(Dimensions.space10),
                              Expanded(
                                child: CustomAppCard(
                                  onPressed: () {
                                    widget.nextPage(goToPage: 2);
                                  },
                                  backgroundColor: widget.currentPage == 2 ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                                  radius: Dimensions.radiusProMax.r,
                                  showBorder: false,
                                  width: double.infinity,
                                  height: Dimensions.space7,
                                  child: SizedBox.shrink(),
                                ),
                              ),
                            ],
                          ),
                          spaceDown(Dimensions.space20),
                          HeaderText(
                            text: widget.currentPage == 0
                                ? MyStrings.personalInformation.tr
                                : widget.currentPage == 1
                                    ? MyStrings.addressInformation.tr
                                    : MyStrings.setUpPIN.tr,
                            textStyle: MyTextStyle.sectionTitle2.copyWith(
                              color: MyColor.getHeaderTextColor(),
                            ),
                          ),
                          spaceDown(Dimensions.space5),
                          ExpandablePageView(
                            controller: widget.pageController,
                            children: [
                              //User information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  spaceDown(Dimensions.space20),
                                  Form(
                                    key: formkey1,
                                    child: Column(
                                      children: [
                                        //Text Field
                                        RoundedTextField(
                                          controller: controller.fNameController,
                                          labelText: MyStrings.firstName,
                                          hintText: MyStrings.enterYourFirstName,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.name,
                                          validator: (value) {
                                            if (value.toString().trim().isEmpty) {
                                              return MyStrings.kFirstNameNullError.tr;
                                            } else {
                                              return null;
                                            }
                                          },
                                        ),
                                        spaceDown(Dimensions.space20),
                                        RoundedTextField(
                                          controller: controller.lNameController,
                                          labelText: MyStrings.lastName,
                                          hintText: MyStrings.enterYourLastName,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.name,
                                          validator: (value) {
                                            if (value.toString().trim().isEmpty) {
                                              return MyStrings.kLastNameNullError.tr;
                                            } else {
                                              return null;
                                            }
                                          },
                                        ),
                                        spaceDown(Dimensions.space20),
                                        RoundedTextField(
                                          controller: controller.uNameController,
                                          labelText: MyStrings.username,
                                          hintText: MyStrings.enterYourUsername,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.name,
                                          validator: (value) {
                                            if (value.toString().trim().isEmpty) {
                                              return MyStrings.kUsernameIsRequired.tr;
                                            } else {
                                              return null;
                                            }
                                          },
                                        ),
                                        spaceDown(Dimensions.space20),
                                        RoundedTextField(
                                          controller: controller.emailController,
                                          labelText: MyStrings.email,
                                          hintText: MyStrings.enterYourEmailExample,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.emailAddress,
                                          validator: (value) {
                                            if (value.toString().trim().isEmpty) {
                                              return MyStrings.invalidEmailMsg.tr;
                                            } else if (!GetUtils.isEmail(
                                              value.toString().trim(),
                                            )) {
                                              return MyStrings.invalidEmailMsg.tr;
                                            } else {
                                              return null;
                                            }
                                          },
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                              //Address information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  spaceDown(Dimensions.space20),
                                  Form(
                                    key: formkey2,
                                    child: Column(
                                      children: [
                                        RoundedTextField(
                                          controller: controller.addressController,
                                          labelText: MyStrings.address,
                                          hintText: MyStrings.enterYourAddress,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.name,
                                        ),
                                        spaceDown(Dimensions.space20),
                                        RoundedTextField(
                                          controller: controller.stateController,
                                          labelText: MyStrings.state,
                                          hintText: MyStrings.enterYourState,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.name,
                                        ),
                                        spaceDown(Dimensions.space20),
                                        RoundedTextField(
                                          controller: controller.cityController,
                                          labelText: MyStrings.city,
                                          hintText: MyStrings.enterYourCity,
                                          textInputAction: TextInputAction.done,
                                          keyboardType: TextInputType.name,
                                        ),
                                        spaceDown(Dimensions.space20),
                                        RoundedTextField(
                                          controller: controller.zipCodeController,
                                          labelText: MyStrings.zipCode,
                                          hintText: MyStrings.enterYourZipCode,
                                          textInputAction: TextInputAction.next,
                                          keyboardType: TextInputType.name,
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                              //User Pins
                              Column(
                                children: [
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      spaceDown(Dimensions.space20),
                                      Form(
                                        key: formkey3,
                                        child: Column(
                                          children: [
                                            //Pin
                                            RoundedTextField(
                                              controller: controller.pinController,
                                              labelText: MyStrings.newPin,
                                              hintText: MyStrings.enterYourPinCode,
                                              textInputAction: TextInputAction.next,
                                              keyboardType: TextInputType.number,
                                              textInputFormatter: [
                                                FilteringTextInputFormatter.digitsOnly, // Allow only digits
                                                LengthLimitingTextInputFormatter(
                                                  SharedPreferenceService.getMaxPinNumberDigit(),
                                                ), // Limit to 5 characters
                                              ],
                                              isPassword: true,
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
                                            spaceDown(Dimensions.space20),
                                            //Confirm pin
                                            RoundedTextField(
                                              controller: controller.cPinController,
                                              labelText: MyStrings.confirmPin,
                                              keyboardType: TextInputType.number,
                                              textInputFormatter: [
                                                FilteringTextInputFormatter.digitsOnly, // Allow only digits
                                                LengthLimitingTextInputFormatter(
                                                  SharedPreferenceService.getMaxPinNumberDigit(),
                                                ), // Limit to 5 characters
                                              ],
                                              hintText: MyStrings.enterYourConfirmPinCode,
                                              textInputAction: TextInputAction.done,
                                              isPassword: true,
                                              validator: (value) {
                                                if (controller.pinController.text != controller.cPinController.text) {
                                                  return MyStrings.kMatchPinError.tr;
                                                } else {
                                                  return null;
                                                }
                                              },
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    spaceDown(Dimensions.space15),
                    CustomElevatedBtn(
                      radius: Dimensions.largeRadius.r,
                      bgColor: MyColor.getPrimaryColor(),
                      isLoading: controller.submitProfileCompleteLoading,
                      text: widget.currentPage == 2 ? MyStrings.confirm : MyStrings.continueText,
                      onTap: () {
                        MyUtils.clearAllTypeFocusNodes();
                        if (widget.pageController.page?.toInt() == 0) {
                          if (formkey1.currentState?.validate() ?? false) {
                            printW("validated");

                            widget.nextPage();
                          } else {
                            // Form is invalid, show validation errors
                          }
                        } else if (widget.pageController.page?.toInt() == 1) {
                          if (formkey2.currentState?.validate() ?? false) {
                            printW("validated");

                            widget.nextPage();
                          } else {
                            // Form is invalid, show validation errors
                          }
                        } else {
                          if (formkey3.currentState?.validate() ?? false) {
                            printW("validated");
                            controller.profileCompleteSubmit();
                          } else {
                            // Form is invalid, show validation errors
                          }
                        }
                      },
                    ),
                  ],
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}
