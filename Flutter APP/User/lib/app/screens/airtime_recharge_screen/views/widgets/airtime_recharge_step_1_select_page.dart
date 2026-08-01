import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/controller/airtime_recharge_controller.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/views/widgets/airtime_bottom_sheet.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class AirTimeRechargeStep1SelectPage extends StatefulWidget {
  const AirTimeRechargeStep1SelectPage({
    super.key,
    required this.onSuccessCallback,
  });
  final VoidCallback onSuccessCallback;

  @override
  State<AirTimeRechargeStep1SelectPage> createState() => _AirTimeRechargeStep1SelectPageState();
}

class _AirTimeRechargeStep1SelectPageState extends State<AirTimeRechargeStep1SelectPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<AirTimeRechargeController>(
      builder: (controller) {
        return Form(
          key: formKey,
          child: SingleChildScrollView(
            child: Skeletonizer(
              enabled: controller.isPageLoading,
              child: Padding(
                padding: EdgeInsets.only(bottom: Dimensions.space16.h),
                child: CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
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
                            isSvg: true,
                            width: Dimensions.space24.w,
                            height: Dimensions.space16.w,
                            boxFit: BoxFit.contain,
                            imageUrl: controller.selectedCountry?.flagUrl ?? "",
                            customErrorWidget: Icon(
                              Icons.flag_outlined,
                              color: MyColor.getBodyTextColor(),
                            ),
                          ),
                        ),
                        suffixIcon: Icon(
                          Icons.keyboard_arrow_down_rounded,
                          color: MyColor.getDarkColor(),
                        ),
                        onTap: () {
                          AirTimeBottomSheet.countryBottomSheet(
                            context,
                            controller: controller,
                            onSelectedData: (v) async {
                              await controller.selectAnCountryOnTap(v);
                            },
                          );
                        },
                        validator: (value) {
                          if (value.toString().isEmpty) {
                            return MyStrings.countryIsRequired.tr;
                          }
                          return null;
                        },
                      ),
                      spaceDown(Dimensions.space20),
                      //Country
                      RoundedTextField(
                        readOnly: true,
                        labelText: MyStrings.selectOperator.tr,
                        hintText: MyStrings.selectOperator.tr,
                        textInputAction: TextInputAction.next,
                        keyboardType: TextInputType.text,
                        controller: controller.operatorController,
                        prefixIcon: Container(
                          margin: const EdgeInsetsDirectional.only(
                            start: Dimensions.space15,
                            end: Dimensions.space8,
                          ),
                          child: MyNetworkImageWidget(
                            width: Dimensions.space24.sp,
                            height: Dimensions.space16.sp,
                            boxFit: BoxFit.contain,
                            imageUrl: controller.selectedOperator?.logoUrls?.first ?? "",
                            customErrorWidget: Icon(
                              Icons.signal_cellular_alt,
                              color: MyColor.getBodyTextColor(),
                            ),
                          ),
                        ),
                        suffixIcon: Icon(
                          Icons.keyboard_arrow_down_rounded,
                          color: MyColor.getDarkColor(),
                        ),
                        onTap: () {
                          if (controller.selectedCountry?.id == null) {
                            CustomSnackBar.error(
                              errorList: [
                                MyStrings.pleaseSelectACountryFirst.tr,
                              ],
                            );
                            return;
                          }
                          AirTimeBottomSheet.operatorsBottomSheet(
                            context,
                            controller: controller,
                            onSelectedData: (v) {
                              controller.selectAnOperatorOnTap(v);
                            },
                          );
                        },
                        validator: (value) {
                          if (value.toString().isEmpty) {
                            return MyStrings.operatorIsRequired.tr;
                          }
                          return null;
                        },
                      ),
                      spaceDown(Dimensions.space20),
                      //phone
                      RoundedTextField(
                        labelText: MyStrings.phoneNumber.tr,
                        hintText: MyStrings.phoneNumber.tr,
                        controller: controller.phoneNumberOrUserNameController,
                        textInputAction: TextInputAction.done,
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
                                  if (controller.selectedCountry?.id == null || controller.selectedCountry?.callingCodes?.isEmpty == true) ...[
                                    Icon(
                                      Icons.phone_rounded,
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                  ] else ...[
                                    Text(
                                      controller.selectedCountry?.callingCodes?.firstOrNull ?? "",
                                      style: MyTextStyle.bodyTextStyle2.copyWith(
                                        color: MyColor.getBodyTextColor(),
                                      ),
                                    ),
                                  ],
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
                            18,
                          ), // Limit to characters
                        ],
                        validator: (value) {
                          if (value.toString().isEmpty) {
                            return MyStrings.kPhoneNumberIsRequired.tr;
                          } else {
                            return null;
                          }
                        },
                      ),
                      spaceDown(Dimensions.space20),
                      AppMainSubmitButton(
                        text: MyStrings.continueText,
                        onTap: () {
                          if (formKey.currentState?.validate() ?? false) {
                            widget.onSuccessCallback();
                          }
                        },
                      ),
                      //Amount Number
                      spaceDown(Dimensions.space20),
                    ],
                  ),
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
