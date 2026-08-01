import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/screens/global/views/widgets/country_bottom_sheet.dart';
import 'package:ovopay/app/screens/virtual_cards/views/widgets/header_text_card.dart';
import 'package:ovopay/app/components/drop_down/my_drop_down_widget.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/label_text.dart';
import 'package:ovopay/app/components/text/small_text.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/app/screens/virtual_cards/controller/virtual_cards_controller.dart';
import 'package:ovopay/environment.dart';

import '../../../../../core/utils/util_exporter.dart';
import '../../../../core/data/models/modules/virtual_cards/virtual_cards_response_model.dart';
import '../../../components/dialog/app_dialog.dart';

class CreateVcCardScreen extends StatefulWidget {
  const CreateVcCardScreen({super.key});

  @override
  State<CreateVcCardScreen> createState() => _CreateVcCardScreenState();
}

class _CreateVcCardScreenState extends State<CreateVcCardScreen> {
  final cardHolderFormKey1 = GlobalKey<FormState>();
  final cardHolderFormKey2 = GlobalKey<FormState>();
  final cardHolderFormKey3 = GlobalKey<FormState>();
  final cardHolderFormKey4 = GlobalKey<FormState>();

  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);
  }

  void _pageChangeListener() {
    int newPage = _pageController.page?.round() ?? 0;
    if (newPage != _currentPage) {
      setState(() {
        _currentPage = newPage;
      });
    }
  }

  @override
  void dispose() {
    // Remove listener when the widget is disposed to avoid memory leaks
    _pageController.removeListener(_pageChangeListener);
    _pageController.dispose();
    super.dispose();
  }

  void _nextPage({int? goToPage}) {
    setState(() {
      _pageController.animateToPage(
        goToPage ?? ++_currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  void _previousPage({int? goToPage}) {
    setState(() {
      _pageController.animateToPage(
        goToPage ?? --_currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VirtualCardsController>(
      builder: (controller) {
        return WillPopWidget(
          action: () {
            controller.clearCreationData();
            Get.back();
          },
          child: MyCustomScaffold(
            padding: EdgeInsets.zero,
            onBackButtonTap: () {
              if (_currentPage != 0) {
                _previousPage();
              } else {
                controller.clearCreationData();
                Get.back();
              }
            },
            pageTitle: MyStrings.createNewVirtualCard,
            body: PageView(
              controller: _pageController,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                _buildVccFormPage(controller: controller),
                _buildVccCardHolderForm(controller: controller),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildVccFormPage({required VirtualCardsController controller}) {
    return SingleChildScrollView(
      child: Padding(
        padding: EdgeInsetsDirectional.symmetric(
          horizontal: Dimensions.space16.w,
          vertical: Dimensions.space16.w,
        ),
        child: Column(
          children: [
            //Identity Verification
            CustomAppCard(
              width: double.infinity,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  LabelText(
                    textAlign: TextAlign.start,
                    text: MyStrings.cardHolder.tr,
                    isRequired: true,
                    textStyle: MyTextStyle.sectionTitle.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                  ),
                  SmallText(
                    text: MyStrings.chooseCardHolderSubText.tr,
                    textAlign: TextAlign.start,
                    maxLine: 100,
                    textStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                  ),
                  spaceDown(Dimensions.space10),
                  IntrinsicHeight(
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: List.generate(2, (index) {
                        return Expanded(
                          child: CustomAppCard(
                            margin: index == 0
                                ? EdgeInsetsDirectional.only(
                                    end: Dimensions.space10,
                                  )
                                : EdgeInsets.zero,
                            onPressed: () {
                              controller.selectCardHolderType(
                                (index + 1).toString(),
                              );
                            },
                            backgroundColor: controller.cardHolderType == "${index + 1}"
                                ? MyColor.getPrimaryColor().withValues(
                                    alpha: 0.1,
                                  )
                                : MyColor.getWhiteColor(),
                            borderColor: controller.cardHolderType == "${index + 1}" ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                            padding: EdgeInsets.symmetric(
                              vertical: Dimensions.space20.w,
                              horizontal: Dimensions.space20.w,
                            ),
                            radius: Dimensions.cardExtraRadius.r,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                MyAssetImageWidget(
                                  assetPath: index == 0 ? MyIcons.existHolderIcon : MyIcons.createNewHolderIcon,
                                  boxFit: BoxFit.contain,
                                  width: double.infinity,
                                  height: Dimensions.space25.h,
                                ),
                                spaceDown(Dimensions.space5),
                                HeaderText(
                                  text: index == 0 ? MyStrings.existingCardHolder.tr : MyStrings.createNewCardHolder.tr,
                                  textStyle: MyTextStyle.caption1Style.copyWith(
                                    color: MyColor.getHeaderTextColor(),
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),
                        );
                      }),
                    ),
                  ),
                  if (controller.cardHolderType == "1") ...[
                    spaceDown(Dimensions.space25),
                    AppDropdownWidget(
                      items: controller.cardHolderList,
                      itemToString: (v) {
                        return "${v.name}-${v.email}";
                      },
                      onItemSelected: (value) {
                        controller.selectCardHolder(value);
                      },
                      selectedItem: controller.selectedCardHolder,
                      child: RoundedTextField(
                        isRequired: true,
                        readOnly: true,
                        labelText: MyStrings.chooseCardHolder.tr,
                        hintText: MyStrings.selectOne.tr,
                        controller: TextEditingController(
                          text: controller.selectedCardHolder?.name ?? "",
                        ),
                        textInputAction: TextInputAction.next,
                        keyboardType: TextInputType.text,
                        onTap: () {},
                        suffixIcon: Icon(
                          Icons.keyboard_arrow_down_rounded,
                          color: MyColor.getDarkColor(),
                        ),
                        validator: (value) {
                          return null;
                        },
                      ),
                    ),
                  ] else
                    ...[],
                  spaceDown(Dimensions.space15),
                  LabelText(
                    textAlign: TextAlign.start,
                    text: MyStrings.usabilityType.tr,
                    isRequired: true,
                    textStyle: MyTextStyle.sectionTitle.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                  ),
                  SmallText(
                    text: MyStrings.usabilityTypeSubtext.tr,
                    textAlign: TextAlign.start,
                    maxLine: 100,
                    textStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                  ),
                  spaceDown(Dimensions.space10),
                  IntrinsicHeight(
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: List.generate(2, (index) {
                        return Expanded(
                          child: CustomAppCard(
                            margin: index == 0
                                ? EdgeInsetsDirectional.only(
                                    end: Dimensions.space10,
                                  )
                                : EdgeInsets.zero,
                            onPressed: () {
                              controller.selectCardUsabilityType(
                                (index + 1).toString(),
                              );
                            },
                            backgroundColor: controller.cardUsabilityType == "${index + 1}"
                                ? MyColor.getPrimaryColor().withValues(
                                    alpha: 0.1,
                                  )
                                : MyColor.getWhiteColor(),
                            borderColor: controller.cardUsabilityType == "${index + 1}" ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                            padding: EdgeInsets.symmetric(
                              vertical: Dimensions.space20.w,
                              horizontal: Dimensions.space20.w,
                            ),
                            radius: Dimensions.cardExtraRadius.r,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                MyAssetImageWidget(
                                  assetPath: index == 0 ? MyIcons.reusableIcon : MyIcons.onTimeIcon,
                                  boxFit: BoxFit.contain,
                                  width: double.infinity,
                                  height: Dimensions.space25.h,
                                ),
                                spaceDown(Dimensions.space5),
                                HeaderText(
                                  text: index == 0 ? MyStrings.reusable.tr : MyStrings.oneTime.tr,
                                  textStyle: MyTextStyle.caption1Style.copyWith(
                                    color: MyColor.getHeaderTextColor(),
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),
                        );
                      }),
                    ),
                  ),
                ],
              ),
            ),

            spaceDown(Dimensions.space15),
            CustomElevatedBtn(
              isLoading: controller.isCardCreateLoadings,
              radius: Dimensions.largeRadius.r,
              bgColor: MyColor.getPrimaryColor(),
              text: controller.cardHolderType == "1" ? MyStrings.confirm : MyStrings.continueText,
              onTap: () {
                if (controller.cardUsabilityType == "0") {
                  CustomSnackBar.error(
                    errorList: [MyStrings.pleaseSelectCardUsability],
                  );
                  return;
                }
                if (controller.cardHolderType == "1") {
                  if (controller.selectedCardHolder == null || controller.selectedCardHolder?.id == null) {
                    CustomSnackBar.error(
                      errorList: [MyStrings.pleaseSelectCardHolder],
                    );
                    return;
                  }
                  controller.createVirtualCard(
                    onSuccessCallback: (value) {
                      AppDialogs.successDialogForAll(
                        context,
                        title: MyStrings.createNewVirtualCard.tr,
                        subTitle: value,
                        barrierDismissible: false,
                        onTap: () async {
                          Get.back();
                          Get.back();
                          await controller.initController();
                        },
                      );
                    },
                  );
                } else {
                  controller.selectCardHolder(CardHolder());
                  _nextPage(goToPage: 1);
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildVccCardHolderForm({required VirtualCardsController controller}) {
    final steps = [
      Step(
        title: Text(MyStrings.nameAndDateOfBirth.tr),
        content: _buildIdentitySection(controller),
        isActive: controller.currentStep >= 0,
      ),
      Step(
        title: Text(MyStrings.billingAddress.tr),
        content: _buildBillingSection(controller),
        isActive: controller.currentStep >= 1,
      ),
      Step(
        title: Text(MyStrings.contactInformation.tr),
        content: _buildContactSection(controller),
        isActive: controller.currentStep >= 2,
      ),
      Step(
        title: Text(MyStrings.govIssueID.tr),
        content: _buildGovernmentIdSection(controller),
        isActive: controller.currentStep >= 3,
      ),
    ];

    return Column(
      children: [
        Expanded(
          child: Stepper(
            connectorColor: WidgetStateColor.resolveWith((states) {
              if (states.contains(WidgetState.selected)) {
                return MyColor.getPrimaryColor();
              }
              if (states.contains(WidgetState.disabled)) {
                return MyColor.getPrimaryColor().withValues(alpha: 0.5);
              }
              return MyColor.getBorderColor();
            }),
            type: StepperType.vertical,
            currentStep: controller.currentStep.clamp(0, steps.length - 1),
            steps: steps,
            onStepContinue: () {
              if (controller.currentStep == 0) {
                if (cardHolderFormKey1.currentState?.validate() ?? false) {
                  controller.setCurrentStep(
                    controller.currentStep + 1,
                    steps.length,
                  );
                  return;
                }
              }
              if (controller.currentStep == 1) {
                if (cardHolderFormKey2.currentState?.validate() ?? false) {
                  controller.setCurrentStep(
                    controller.currentStep + 1,
                    steps.length,
                  );
                  return;
                }
              }
              if (controller.currentStep == 2) {
                if (cardHolderFormKey3.currentState?.validate() ?? false) {
                  controller.setCurrentStep(
                    controller.currentStep + 1,
                    steps.length,
                  );
                  return;
                }
              }
              if (controller.currentStep == 3) {
                if (cardHolderFormKey4.currentState?.validate() ?? false) {
                  controller.createVirtualCard(
                    onSuccessCallback: (value) {
                      AppDialogs.successDialogForAll(
                        context,
                        title: MyStrings.createNewVirtualCard.tr,
                        subTitle: value,
                        barrierDismissible: false,
                        onTap: () async {
                          Get.back();
                          Get.back();
                          await controller.initController();
                        },
                      );
                    },
                  );
                }
              }
            },
            onStepCancel: () {
              if (controller.currentStep > 0) {
                controller.setCurrentStep(
                  controller.currentStep - 1,
                  steps.length,
                );
              }
            },
            controlsBuilder: (context, details) {
              return Padding(
                padding: const EdgeInsets.only(top: Dimensions.space16),
                child: Row(
                  children: [
                    if (controller.currentStep > 0)
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(
                            end: Dimensions.space15,
                          ),
                          child: CustomElevatedBtn(
                            radius: Dimensions.largeRadius.r,
                            borderColor: MyColor.getBorderColor(),
                            bgColor: MyColor.getWhiteColor(),
                            textColor: MyColor.getBodyTextColor(),
                            text: MyStrings.previous,
                            onTap: details.onStepCancel ?? () {},
                          ),
                        ),
                      ),
                    Expanded(
                      child: CustomElevatedBtn(
                        radius: Dimensions.largeRadius.r,
                        bgColor: MyColor.getPrimaryColor(),
                        text: controller.currentStep == steps.length - 1 ? MyStrings.confirm : MyStrings.next,
                        isLoading: controller.currentStep == steps.length - 1 ? controller.isCardCreateLoadings : false,
                        onTap: details.onStepContinue ?? () {},
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  // Identity Verification Section
  Widget _buildIdentitySection(VirtualCardsController controller) {
    return Form(
      key: cardHolderFormKey1,
      child: CustomAppCard(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            HeaderWithText(
              header: MyStrings.nameOnCard.tr,
              text: MyStrings.nameOnCardSubText.tr,
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: false,
              instructions: MyStrings.nameOnCardSubText.tr,
              labelText: MyStrings.cardName.tr,
              hintText: MyStrings.cardName.tr,
              textInputAction: TextInputAction.next,
              keyboardType: TextInputType.text,
              onChanged: (value) {
                controller.selectedCardHolder?.name = value;
              },
              validator: (value) {
                controller.selectedCardHolder?.name = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.kNameNullError.tr;
                } else {
                  return null;
                }
              },
            ),
            spaceDown(Dimensions.space16),
            HeaderWithText(
              header: MyStrings.legalName.tr,
              text: MyStrings.legalNameSubText.tr,
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: false,
              labelText: MyStrings.firstName.tr,
              hintText: MyStrings.firstName.tr,
              textInputAction: TextInputAction.next,
              keyboardType: TextInputType.text,
              onChanged: (value) {
                controller.selectedCardHolder?.firstName = value;
              },
              validator: (value) {
                controller.selectedCardHolder?.firstName = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.kFirstNameNullError.tr;
                } else {
                  return null;
                }
              },
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: false,
              labelText: MyStrings.lastName.tr,
              hintText: MyStrings.lastName.tr,
              textInputAction: TextInputAction.next,
              keyboardType: TextInputType.text,
              onChanged: (value) {
                controller.selectedCardHolder?.lastName = value;
              },
              validator: (value) {
                controller.selectedCardHolder?.lastName = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.kLastNameNullError.tr;
                } else {
                  return null;
                }
              },
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: false,
              labelText: MyStrings.dateOfBirth.tr,
              hintText: MyStrings.dateOfBirth.tr,
              controller: TextEditingController(
                text: controller.selectedCardHolder?.dob?.fullText ?? "",
              ),
              textInputAction: TextInputAction.next,
              keyboardType: TextInputType.datetime,
              readOnly: true,
              onTap: () {
                selectDateOfBirth(controller, context);
              },
              prefixIcon: Icon(
                Icons.calendar_month_outlined,
                color: MyColor.getHeaderTextColor().withValues(alpha: 0.5),
              ),
              validator: (value) {
                if (value.toString().trim().isEmpty) {
                  return MyStrings.fieldErrorMsg.tr;
                } else {
                  return null;
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  // Contact Information Section
  Widget _buildContactSection(VirtualCardsController controller) {
    return Form(
      key: cardHolderFormKey3,
      child: CustomAppCard(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            HeaderWithText(
              header: MyStrings.contactInformation.tr,
              text: MyStrings.contactInformationSubText.tr,
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: false,
              labelText: MyStrings.email.tr,
              hintText: MyStrings.email.tr,
              textInputAction: TextInputAction.next,
              keyboardType: TextInputType.emailAddress,
              onChanged: (value) {
                controller.selectedCardHolder?.email = value;
              },
              validator: (value) {
                controller.selectedCardHolder?.email = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.fieldErrorMsg.tr;
                } else if (!(value.toString().trim().isEmail)) {
                  return MyStrings.invalidEmailMsg.tr;
                } else {
                  return null;
                }
              },
            ),
            spaceDown(Dimensions.space16),
            //phone
            RoundedTextField(
              isRequired: true,
              showLabelText: false,
              labelText: MyStrings.phoneNumber.tr,
              hintText: MyStrings.phoneNumber.tr,
              controller: controller.phoneNumberOrUserNameController,
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
                          "+${controller.countryData?.dialCode ?? ""}",
                          style: MyTextStyle.bodyTextStyle2.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                        ),
                        spaceSide(Dimensions.space8),
                        Container(
                          color: MyColor.getBodyTextColor().withValues(
                            alpha: 0.5,
                          ),
                          width: 1.2.w,
                          height: Dimensions.space25.h,
                        ),
                      ],
                    ),
                  ),
                ),
              ),
              onChanged: (value) {
                controller.selectedCardHolder?.phoneNumber = value;
              },
              textInputFormatter: [
                FilteringTextInputFormatter.digitsOnly,
                LengthLimitingTextInputFormatter(
                  controller.countryData?.getMobileNumberDigit(),
                ),
              ],
              forceShowSuffixDesign: true,
              isPassword: false,
              validator: (value) {
                controller.selectedCardHolder?.phoneNumber = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.fieldErrorMsg.tr;
                } else {
                  return null;
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  // Billing Address Section
  Widget _buildBillingSection(VirtualCardsController controller) {
    return Form(
      key: cardHolderFormKey2,
      child: CustomAppCard(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            HeaderWithText(
              header: MyStrings.billingAddress.tr,
              text: MyStrings.billingAddressSubText.tr,
            ),
            spaceDown(Dimensions.space16),
            Row(
              children: [
                Expanded(
                  flex: 2,
                  child: RoundedTextField(
                    isRequired: true,
                    showLabelText: false,
                    labelText: MyStrings.address,
                    hintText: MyStrings.address,
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.text,
                    onChanged: (value) {
                      controller.selectedCardHolder?.address = value;
                    },
                    validator: (value) {
                      controller.selectedCardHolder?.address = value;
                      if (value.toString().trim().isEmpty) {
                        return MyStrings.fieldErrorMsg.tr;
                      } else {
                        return null;
                      }
                    },
                  ),
                ),
                spaceSide(Dimensions.space16),
                Expanded(
                  child: RoundedTextField(
                    isRequired: true,
                    showLabelText: false,
                    labelText: MyStrings.state,
                    hintText: MyStrings.state,
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.text,
                    onChanged: (value) {
                      controller.selectedCardHolder?.state = value;
                    },
                    validator: (value) {
                      if (value.toString().trim().isEmpty) {
                        controller.selectedCardHolder?.state = value;
                        return MyStrings.fieldErrorMsg.tr;
                      } else {
                        return null;
                      }
                    },
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space16),
            Row(
              children: [
                Expanded(
                  child: RoundedTextField(
                    isRequired: true,
                    showLabelText: false,
                    labelText: MyStrings.zipCode,
                    hintText: MyStrings.zipCode,
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.text,
                    onChanged: (value) {
                      controller.selectedCardHolder?.postalCode = value;
                    },
                    validator: (value) {
                      controller.selectedCardHolder?.postalCode = value;
                      if (value.toString().trim().isEmpty) {
                        return MyStrings.fieldErrorMsg.tr;
                      } else {
                        return null;
                      }
                    },
                  ),
                ),
                spaceSide(Dimensions.space16),
                Expanded(
                  child: RoundedTextField(
                    isRequired: true,
                    showLabelText: false,
                    labelText: MyStrings.city,
                    hintText: MyStrings.city,
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.text,
                    onChanged: (value) {
                      controller.selectedCardHolder?.city = value;
                    },
                    validator: (value) {
                      controller.selectedCardHolder?.city = value;
                      if (value.toString().trim().isEmpty) {
                        return MyStrings.fieldErrorMsg.tr;
                      } else {
                        return null;
                      }
                    },
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space16),
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
          ],
        ),
      ),
    );
  }

  // Government Information
  Widget _buildGovernmentIdSection(VirtualCardsController controller) {
    return Form(
      key: cardHolderFormKey4,
      child: CustomAppCard(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            HeaderWithText(
              header: MyStrings.govIssueID.tr,
              text: MyStrings.govIssueIDSubText.tr,
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: true,
              labelText: MyStrings.uploadFontSide,
              contentPadding: EdgeInsets.symmetric(
                vertical: Dimensions.space30,
              ),
              prefixIcon: CustomAppCard(
                showBorder: false,
                margin: EdgeInsetsDirectional.only(
                  start: Dimensions.space10,
                  end: Dimensions.space10,
                ),
                padding: EdgeInsetsDirectional.zero,
                width: 50,
                height: 40,
                radius: Dimensions.largeRadius.r,
                backgroundColor: MyColor.getPrimaryColor(),
                child: Icon(
                  controller.isImageFile(extensions: controller.fileExtensions) ? Icons.camera_alt_outlined : Icons.file_present_outlined,
                  color: MyColor.getWhiteColor(),
                ),
              ),
              onTap: () async {
                var file = await controller.pickFile(
                  extention: controller.fileExtensions,
                );
                if (file != null) {
                  controller.selectedCardHolder?.documentFront = file.name;
                  controller.govDocFile1 = File(file.path ?? "");

                  controller.update();
                }
              },
              controller: TextEditingController(
                text: controller.selectedCardHolder?.documentFront?.toString() ?? "",
              ),
              readOnly: true,
              hintText: controller.selectedCardHolder?.documentFront == null
                  ? controller.isImageFile(
                      extensions: controller.fileExtensions,
                    )
                      ? "${MyStrings.chooseAImage.tr} ${controller.fileExtensions}"
                      : "${MyStrings.chooseAFile.tr} ${controller.fileExtensions}"
                  : controller.selectedCardHolder?.documentFront?.toString() ?? "",
              textInputAction: TextInputAction.done,
              keyboardType: TextInputType.text,
              validator: (value) {
                controller.selectedCardHolder?.documentFront = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.fieldErrorMsg.tr;
                } else {
                  return null;
                }
              },
            ),
            spaceDown(Dimensions.space16),
            RoundedTextField(
              isRequired: true,
              showLabelText: true,
              labelText: MyStrings.uploadBackSide,
              contentPadding: EdgeInsets.symmetric(
                vertical: Dimensions.space30,
              ),
              prefixIcon: CustomAppCard(
                showBorder: false,
                margin: EdgeInsetsDirectional.only(
                  start: Dimensions.space10,
                  end: Dimensions.space10,
                ),
                padding: EdgeInsetsDirectional.zero,
                width: 50,
                height: 40,
                radius: Dimensions.largeRadius.r,
                backgroundColor: MyColor.getPrimaryColor(),
                child: Icon(
                  controller.isImageFile(extensions: controller.fileExtensions) ? Icons.camera_alt_outlined : Icons.file_present_outlined,
                  color: MyColor.getWhiteColor(),
                ),
              ),
              onTap: () async {
                var file = await controller.pickFile(
                  extention: controller.fileExtensions,
                );
                if (file != null) {
                  controller.selectedCardHolder?.documentBack = file.name;
                  controller.govDocFile2 = File(file.path ?? "");

                  controller.update();
                }
              },
              controller: TextEditingController(
                text: controller.selectedCardHolder?.documentBack?.toString() ?? "",
              ),
              readOnly: true,
              hintText: controller.selectedCardHolder?.documentBack == null
                  ? controller.isImageFile(
                      extensions: controller.fileExtensions,
                    )
                      ? "${MyStrings.chooseAImage.tr} ${controller.fileExtensions}"
                      : "${MyStrings.chooseAFile.tr} ${controller.fileExtensions}"
                  : controller.selectedCardHolder?.documentBack?.toString() ?? "",
              textInputAction: TextInputAction.done,
              keyboardType: TextInputType.text,
              validator: (value) {
                controller.selectedCardHolder?.documentBack = value;
                if (value.toString().trim().isEmpty) {
                  return MyStrings.fieldErrorMsg.tr;
                } else {
                  return null;
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  void selectDateOfBirth(
    VirtualCardsController controller,
    BuildContext context,
  ) async {
    DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now().subtract(Duration(days: (365 * 13) + 7)),
      firstDate: DateTime(1900),
      lastDate: DateTime.now().subtract(Duration(days: (365 * 13) + 7)),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: ColorScheme.light(
              primary: MyColor.getPrimaryColor(), // Selection color
            ),
          ),
          child: child!,
        );
      },
    );
    if (pickedDate != null) {
      final DateTime selectedDateTime = DateTime(
        pickedDate.year,
        pickedDate.month,
        pickedDate.day,
      );

      controller.selectedCardHolder?.dob = DateOfBirthModel(
        day: DateConverter.estimatedDateOrTime(selectedDateTime, customFormat: "dd"),
        month: DateConverter.estimatedDateOrTime(selectedDateTime, customFormat: "MM"),
        year: DateConverter.estimatedDateOrTime(selectedDateTime, customFormat: "yyyy"),
        fullText: DateConverter.estimatedDateOrTime(selectedDateTime),
      );
      controller.update();
    }
  }
}
