import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_contact_list_tile_card.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/components/text/small_text.dart';
import 'package:ovopay/app/screens/request_money_screen/controller/request_money_controller.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../../core/utils/util_exporter.dart';

class RequestMoneyPinVerificationPage extends StatelessWidget {
  const RequestMoneyPinVerificationPage({super.key, required this.context});

  final BuildContext context;

  // Reusable Contact List Tile
  Widget _buildContactTile(
    RequestMoneyController controller, {
    bool showBorder = true,
    EdgeInsetsGeometry? padding,
  }) {
    return CustomContactListTileCard(
      padding: padding ?? EdgeInsetsDirectional.symmetric(vertical: Dimensions.space5),
      leading: MyNetworkImageWidget(
        width: Dimensions.space40.w,
        height: Dimensions.space40.w,
        isProfile: true,
        imageUrl: '${controller.existUserModel?.getUserImageUrl()}',
        imageAlt: controller.existUserModel?.getFullName() ?? "",
      ),
      imagePath: controller.existUserModel?.getUserImageUrl(),
      title: controller.existUserModel?.getFullName(),
      subtitle: "+${controller.existUserModel?.getUserMobileNo(withCountryCode: true)}",
      showBorder: showBorder,
      trailing: CardColumn(
        headerTextStyle: MyTextStyle.caption1Style.copyWith(
          color: MyColor.getBodyTextColor(),
        ),
        header: MyStrings.amount.tr,
        body: MyUtils.getUserAmount(
          controller.amountController.text.toString(),
        ),
        space: 5,
        crossAxisAlignment: CrossAxisAlignment.end,
      ),
    );
  }

  // Reusable Amount Details Card
  Widget _buildNoteDetailsCard(RequestMoneyController controller) {
    return SizedBox(
      width: double.infinity,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          HeaderTextSmaller(text: MyStrings.note.tr),
          spaceDown(Dimensions.space5),
          SmallText(text: controller.noteController.text),
        ],
      ),
    );
  }

  // Reusable Confirm Dialog
  Future<void> _showConfirmDialog(RequestMoneyController controller) async {
    MyUtils.clearAllTypeFocusNodes();
    if (controller.pinController.text.toString().length < SharedPreferenceService.getMaxPinNumberDigit()) {
      CustomSnackBar.error(
        errorList: [
          MyStrings.kPinMaxNumberError.tr.rKv({
            "digit": "${SharedPreferenceService.getMaxPinNumberDigit()}",
          }),
        ],
      );
      return;
    }
    await AppDialogs.confirmDialog(
      context,
      title: MyStrings.requestMoney,
      userDetailsWidget: CustomAppCard(
        radius: Dimensions.largeRadius.r,
        child: _buildContactTile(controller, showBorder: false),
      ),
      cashDetailsWidget: CustomAppCard(
        radius: Dimensions.largeRadius.r,
        child: _buildNoteDetailsCard(controller),
      ),
      onFinish: () async {
        await controller.pinVerificationProcess(
          onSuccessCallback: (value) async {
            Navigator.pop(context);
            await AppDialogs.successDialog(
              context,
              title: value.message?.first ?? "",
              userDetailsWidget: CustomAppCard(
                radius: Dimensions.largeRadius.r,
                child: _buildContactTile(controller, showBorder: false),
              ),
              cashDetailsWidget: CustomAppCard(
                radius: Dimensions.largeRadius.r,
                child: _buildNoteDetailsCard(controller),
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<RequestMoneyController>(
      builder: (controller) {
        return SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CustomAppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        HeaderText(
                          text: MyStrings.requestReview,
                          textStyle: MyTextStyle.sectionTitle2.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        // spaceDown(Dimensions.space20),
                        CustomContactListTileCard(
                          padding: EdgeInsetsDirectional.symmetric(
                            vertical: Dimensions.space20,
                          ),
                          leading: MyNetworkImageWidget(
                            width: Dimensions.space40.w,
                            height: Dimensions.space40.w,
                            isProfile: true,
                            imageUrl: controller.existUserModel?.getUserImageUrl() ?? '',
                            imageAlt: controller.existUserModel?.getFullName(),
                          ),
                          title: controller.existUserModel?.getFullName(),
                          subtitle: "+${controller.existUserModel?.getUserMobileNo(withCountryCode: true)}",
                          showBorder: true,
                          trailing: CardColumn(
                            headerTextStyle: MyTextStyle.caption1Style.copyWith(
                              color: MyColor.getBodyTextColor(),
                            ),
                            header: MyStrings.amount.tr,
                            body: MyUtils.getUserAmount(
                              controller.amountController.text.toString(),
                            ),
                            space: 5,
                            crossAxisAlignment: CrossAxisAlignment.end,
                          ),
                        ),
                        spaceDown(Dimensions.space16),
                        RoundedTextField(
                          controller: controller.noteController,
                          maxLine: 5,
                          labelText: MyStrings.noteOptional.tr,
                          hintText: "${MyStrings.enterNote.tr}...",
                          textInputAction: TextInputAction.newline,
                          keyboardType: TextInputType.multiline,
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              spaceDown(Dimensions.space16),
              RoundedTextField(
                showLabelText: false,
                controller: controller.pinController,
                labelText: MyStrings.pin,
                hintText: MyStrings.enterYourPinCode,
                fillColor: MyColor.getWhiteColor(),
                textInputAction: TextInputAction.next,
                keyboardType: TextInputType.number,
                isPassword: true,
                forceShowSuffixDesign: true,
                textInputFormatter: [
                  FilteringTextInputFormatter.digitsOnly,
                  LengthLimitingTextInputFormatter(
                    SharedPreferenceService.getMaxPinNumberDigit(),
                  ),
                ],
                prefixIcon: Container(
                  margin: const EdgeInsetsDirectional.only(
                    start: Dimensions.space15,
                    end: Dimensions.space8,
                  ),
                  child: MyAssetImageWidget(
                    color: MyColor.getPrimaryColor(),
                    width: 22.sp,
                    height: 16.sp,
                    boxFit: BoxFit.contain,
                    assetPath: MyIcons.lock,
                    isSvg: true,
                  ),
                ),
                suffixIcon: IconButton(
                  onPressed: () => _showConfirmDialog(controller),
                  icon: MyAssetImageWidget(
                    color: MyColor.getPrimaryColor(),
                    width: 20.sp,
                    height: 20.sp,
                    boxFit: BoxFit.contain,
                    assetPath: MyIcons.arrowForward,
                    isSvg: true,
                  ),
                ),
              ),
              spaceDown(Dimensions.space15),
              AppMainSubmitButton(
                text: MyStrings.confirm,
                onTap: () {
                  _showConfirmDialog(controller);
                },
              ),
            ],
          ),
        );
      },
    );
  }
}
