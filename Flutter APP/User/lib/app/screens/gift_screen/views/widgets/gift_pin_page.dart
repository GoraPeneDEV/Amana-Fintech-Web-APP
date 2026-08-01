import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/amount_details_card.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_contact_list_tile_card.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/divider/custom_divider.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../../core/utils/util_exporter.dart';
import '../../controller/gift_controller.dart';

class GiftCardPinVerificationPage extends StatelessWidget {
  const GiftCardPinVerificationPage({super.key, required this.context});

  final BuildContext context;

  // Reusable Contact List Tile
  Widget _buildContactTile(
    GiftController controller, {
    bool showBorder = true,
    EdgeInsetsGeometry? padding,
  }) {
    return CustomContactListTileCard(
      leading: MyNetworkImageWidget(
        imageUrl: controller.selectedGiftCard.logoUrls?.first ?? "",
        width: Dimensions.space50.w,
        height: Dimensions.space50.w,
        boxFit: BoxFit.contain,
      ),
      imagePath: controller.selectedGiftCard.logoUrls?.first ?? "",
      title: "${controller.selectedGiftCard.productName}",
      subtitle: "${MyStrings.recipient.tr} : ${controller.emailController.text.toLowerCase()}",
      showBorder: showBorder,
      padding: padding ?? EdgeInsets.zero,
    );
  }

  // Reusable Amount Details Card
  Widget _buildAmountDetailsCard(GiftController controller) {
    return AmountDetailsCard(
      amount: "${controller.currencySymbol}${controller.subTotal}",
      charge: '+${controller.currencySymbol}${controller.totalCharge}',
      total: "${controller.currencySymbol}${controller.total}",
      quantity: controller.quantityController.text.trim(),
      firstTitle: MyStrings.subTotal,
    );
  }

  // Reusable Confirm Dialog
  Future<void> _showConfirmDialog(GiftController controller) async {
    MyUtils.clearAllTypeFocusNodes();
    if (controller.pinController.text.toString().length < SharedPreferenceService.getMaxPinNumberDigit()) {
      CustomSnackBar.error(
        errorList: [
          MyStrings.kPinMaxNumberError.tr.rKv({
            "digit": "${SharedPreferenceService.getMaxPinNumberDigit()}",
          }).tr,
        ],
      );
      return;
    }
    await AppDialogs.confirmDialog(
      context,
      title: MyStrings.giftPurchase,
      userDetailsWidget: CustomAppCard(
        radius: Dimensions.largeRadius.r,
        child: _buildContactTile(controller, showBorder: false),
      ),
      cashDetailsWidget: CustomAppCard(
        radius: Dimensions.largeRadius.r,
        child: _buildAmountDetailsCard(controller),
      ),
      onFinish: () async {
        await controller.pinVerificationProcess(
          onSuccessCallback: (value) async {
            // Handle the completed progress here
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
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildAmountDetailsCard(controller),
                    Container(
                      height: 1,
                      width: double.infinity,
                      color: MyColor.getBorderColor(),
                      margin: const EdgeInsets.symmetric(vertical: 10),
                    ),
                    CardColumn(
                      headerTextStyle: MyTextStyle.caption1Style.copyWith(
                        color: MyColor.getBodyTextColor(),
                      ),
                      header: MyStrings.transactionId.tr,
                      isCopyable: true,
                      body: value.data?.transaction?.trx ?? "",
                      space: 5,
                      crossAxisAlignment: CrossAxisAlignment.start,
                    ),
                  ],
                ),
              ),
            );
            return;
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<GiftController>(
      builder: (controller) {
        return SingleChildScrollView(
          padding: EdgeInsets.symmetric(horizontal: Dimensions.space14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              spaceDown(Dimensions.space16),
              CustomAppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildContactTile(
                      controller,
                      padding: EdgeInsetsDirectional.only(
                        bottom: Dimensions.space10,
                      ),
                    ),
                    spaceDown(Dimensions.space16),
                    _buildAmountDetailsCard(controller),
                    spaceDown(Dimensions.space8),
                    CustomDivider(
                      space: 5,
                      thickness: .9,
                    ),
                    spaceDown(Dimensions.space8),
                    Text(
                      "${MyStrings.unitPrice.tr} : ${controller.unitPrice} ${controller.currency} (${controller.amountController.text} ${controller.selectedGiftCard.recipientCurrencyCode ?? ""})",
                      style: MyTextStyle.caption1Style.copyWith(fontWeight: FontWeight.w600),
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
                isPassword: true,
                forceShowSuffixDesign: true,
                fillColor: MyColor.getWhiteColor(),
                textInputAction: TextInputAction.next,
                keyboardType: TextInputType.number,
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
