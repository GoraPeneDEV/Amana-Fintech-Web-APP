import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/controller/profile_controller.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';

class DeleteAccountScreen extends StatefulWidget {
  const DeleteAccountScreen({super.key});

  @override
  State<DeleteAccountScreen> createState() => _DeleteAccountScreenState();
}

class _DeleteAccountScreenState extends State<DeleteAccountScreen> {
  @override
  void initState() {
    Get.put(ProfileRepo());
    final controller = Get.put(ProfileController(profileRepo: Get.find()));

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadProfileInfo();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProfileController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.deleteAccount,
          body: SingleChildScrollView(
            clipBehavior: Clip.none,
            child: Column(
              children: [
                CustomAppCard(
                  padding: EdgeInsets.symmetric(
                    horizontal: Dimensions.space16.w,
                  ),
                  child: Column(
                    children: [
                      spaceDown(Dimensions.space20),
                      MyAssetImageWidget(
                        isSvg: true,
                        assetPath: MyIcons.profileActive,
                        color: MyColor.getBodyTextColor().withValues(
                          alpha: 0.3,
                        ),
                        width: Dimensions.space100.w,
                        height: Dimensions.space100.w,
                      ),
                      spaceDown(Dimensions.space20),
                      Text(
                        MyStrings.deleteYourAccount.tr,
                        textAlign: TextAlign.center,
                        style: MyTextStyle.sectionTitle.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                      spaceDown(Dimensions.space10),
                      Text(
                        MyStrings.deleteAccountSubtitle.tr,
                        textAlign: TextAlign.center,
                        style: MyTextStyle.sectionSubTitle1.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                      spaceDown(Dimensions.space30),
                      if (controller.isShowDeleteAccountPinBox == false) ...[
                        CustomElevatedBtn(
                          radius: Dimensions.largeRadius.r,
                          bgColor: MyColor.error,
                          text: MyStrings.deleteAccount,
                          onTap: () {
                            controller.toggleIsShowDeleteAccountPinBox();
                          },
                        ),
                        spaceDown(Dimensions.space10),
                      ] else ...[
                        RoundedTextField(
                          controller: controller.pinController,
                          labelText: MyStrings.pin,
                          hintText: MyStrings.enterYourPinCode,
                          textInputAction: TextInputAction.done,
                          keyboardType: TextInputType.number,
                          isPassword: true,
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
                              }).tr;
                            } else {
                              return null;
                            }
                          },
                        ),
                        spaceDown(Dimensions.space15),
                        CustomElevatedBtn(
                          isLoading: controller.isDeleteAccountLoading,
                          radius: Dimensions.largeRadius.r,
                          bgColor: MyColor.getPrimaryColor(),
                          text: MyStrings.confirm,
                          onTap: () {
                            MyUtils.clearAllTypeFocusNodes();
                            AppDialogs.confirmDialogForAll(
                              subTitle: MyStrings.areYouSureWantToDeleteYourAccount,
                              context,
                              isConfirmLoading: controller.isDeleteAccountLoading,
                              onConfirmTap: () {
                                controller.deleteAccount();
                              },
                            );
                          },
                        ),
                      ],
                      spaceDown(Dimensions.space20),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
