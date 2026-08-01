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
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/investment/controller/investment_controller.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../../core/utils/util_exporter.dart';

class InvestmentPaymentPinVerificationPage extends StatelessWidget {
  const InvestmentPaymentPinVerificationPage({super.key, required this.context});

  final BuildContext context;

  // Reusable Amount Details Card
  Widget _buildAmountDetailsCard(InvestmentController controller) {
    return AmountDetailsCard(
      firstTitle: MyStrings.investmentAmount.tr,
      centerTitle: MyStrings.perInterestAmount.tr,
      endTitle: MyStrings.totalReturn.tr,
      amount: MyUtils.getUserAmount(controller.amountController.text.toString()),
      charge: MyUtils.getUserAmount(controller.perInterestAmount[controller.selectedPlanIndex].toString()),
      total: controller.investmentPlanList[controller.selectedPlanIndex].investType == "1" ? MyStrings.unlimited.tr : MyUtils.getUserAmount(controller.totalReturns[controller.selectedPlanIndex].toString()),
    );
  }

  // Reusable Amount Details Card
  Widget _buildPlanDetailsCard(InvestmentController controller) {
    return CustomContactListTileCard(
      padding: EdgeInsets.zero,
      showBorder: false,
      showLeading: false,
      title: controller.selectedPlanData?.name ?? '',
      subtitle: controller.selectedPlanData?.description ?? '',
      titleStyle: MyTextStyle.sectionTitle2.copyWith(
        color: MyColor.getHeaderTextColor(),
      ),
      subtitleStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
        color: MyColor.getBodyTextColor(),
      ),
    );
  }

  // Reusable Confirm Dialog
  Future<void> _showConfirmDialog(InvestmentController controller) async {
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
      title: MyStrings.investment,
      userDetailsWidget: CustomAppCard(radius: Dimensions.largeRadius.r, child: _buildPlanDetailsCard(controller)),
      cashDetailsWidget: CustomAppCard(
        radius: Dimensions.largeRadius.r,
        child: _buildAmountDetailsCard(controller),
      ),
      onFinish: () async {
        await controller.pinVerificationProcess(
          onSuccessCallback: (value) async {
            Navigator.pop(context);
            await AppDialogs.successDialog(
              context,
              title: value.message?.first ?? "",
              userDetailsWidget: CustomAppCard(radius: Dimensions.largeRadius.r, child: _buildPlanDetailsCard(controller)),
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
                      body: value.data?.investment?.trx ?? "",
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
    return GetBuilder<InvestmentController>(
      builder: (controller) {
        return SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CustomAppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildPlanDetailsCard(
                      controller,
                    ),
                    spaceDown(Dimensions.space8),
                    CustomDivider(
                      space: 5,
                      thickness: .9,
                    ),
                    spaceDown(Dimensions.space8),
                    _buildAmountDetailsCard(controller),
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
