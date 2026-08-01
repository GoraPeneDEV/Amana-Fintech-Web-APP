import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_contact_list_tile_card.dart';
import 'package:ovopay/app/components/chip/custom_chip.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/add_money/controller/add_money_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class AddMoneyAmountPage extends StatefulWidget {
  const AddMoneyAmountPage({
    super.key,
    required this.context,
    required this.onPaymentGatewayClick,
  });

  final BuildContext context;
  final Function() onPaymentGatewayClick;

  @override
  State<AddMoneyAmountPage> createState() => _AddMoneyAmountPageState();
}

class _AddMoneyAmountPageState extends State<AddMoneyAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<AddMoneyController>(
      builder: (addMoneyController) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      CustomContactListTileCard(
                        leading: GestureDetector(
                          onTap: () {
                            widget.onPaymentGatewayClick();
                          },
                          child: Skeleton.replace(
                            replace: true,
                            replacement: Bone.square(
                              size: Dimensions.space48.h,
                            ),
                            child: MyNetworkImageWidget(
                              radius: Dimensions.radiusMax.r,
                              imageUrl: "${addMoneyController.imagePath}/${addMoneyController.selectedAddMoneyMethod?.method?.image}",
                              isProfile: true,
                              width: Dimensions.space48.w,
                              height: Dimensions.space48.w,
                              imageAlt: addMoneyController.selectedAddMoneyMethod?.name ?? "",
                            ),
                          ),
                        ),
                        padding: EdgeInsetsDirectional.zero,
                        title: addMoneyController.selectedAddMoneyMethod?.name ?? "",
                        subtitle: addMoneyController.selectedAddMoneyMethod?.currency ?? "",
                        showBorder: false,
                      ),
                    ],
                  ),
                ),
                spaceDown(Dimensions.space16),
                CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      HeaderText(
                        text: MyStrings.enterAmount.tr,
                        textStyle: MyTextStyle.headerH3.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                      ),
                      spaceDown(Dimensions.space24),
                      RoundedTextField(
                        controller: addMoneyController.amountController,
                        showLabelText: false,
                        labelText: MyStrings.enterAmount.tr,
                        hintText: '',
                        textInputAction: TextInputAction.done,
                        keyboardType: TextInputType.numberWithOptions(
                          decimal: true,
                        ),
                        textStyle: MyTextStyle.headerH3.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                        focusBorderColor: MyColor.getPrimaryColor(),
                        textInputFormatter: [
                          FilteringTextInputFormatter.allow(
                            RegExp(r'[0-9.]'),
                          ), // Allows digits and a decimal point
                          FilteringTextInputFormatter.deny(
                            RegExp(r'(\.\d{30,})'),
                          ), // Limits decimal places (optional, adjust as needed)
                        ],
                        onChanged: (value) {
                          addMoneyController.onChangeAmountControllerText(
                            value,
                          );
                        },
                        validator: (value) {
                          return MyUtils().validateAmountForm(
                            value: value ?? '0',
                            userCurrentBalance: 0,
                            showCurrentBalance: false,
                            minLimit: AppConverter.formatNumberDouble(
                              addMoneyController.selectedAddMoneyMethod?.minAmount ?? "0",
                            ),
                            maxLimit: AppConverter.formatNumberDouble(
                              addMoneyController.selectedAddMoneyMethod?.maxAmount ?? "0",
                            ),
                          );
                        },
                      ),
                      spaceDown(Dimensions.space8),
                      Text.rich(
                        TextSpan(
                          children: [
                            TextSpan(
                              text: "${MyStrings.limit.tr}: ",
                              style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                color: MyColor.getBodyTextColor(),
                              ),
                            ),
                            TextSpan(
                              text: addMoneyController.depositLimit,
                              style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getPrimaryColor()),
                            ),
                          ],
                        ),
                      ),
                      spaceDown(Dimensions.space24),
                      SingleChildScrollView(
                        clipBehavior: Clip.hardEdge,
                        scrollDirection: Axis.horizontal, // Allows horizontal scrolling
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.center,
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: SharedPreferenceService.getQuickMoneyList().map((
                            value,
                          ) {
                            return CustomAppChip(
                              isSelected: value == addMoneyController.getAmount ? true : false,
                              text: value,
                              onTap: () {
                                addMoneyController.onChangeAmountControllerText(value);
                              },
                            );
                          }).toList(),
                        ),
                      ),
                    ],
                  ),
                ),
                spaceDown(Dimensions.space15),
                AnimatedSwitcher(
                  duration: const Duration(
                    milliseconds: 300,
                  ), // Duration for animation
                  transitionBuilder: (child, animation) {
                    return FadeTransition(opacity: animation, child: child);
                  },
                  child: addMoneyController.mainAmount > 0
                      ? Column(
                          key: ValueKey(
                            'visibleWidget',
                          ), // Key to differentiate between states
                          children: [
                            buildChargeInfoWidget(addMoneyController),
                            spaceDown(Dimensions.space15),
                          ],
                        )
                      : SizedBox(
                          key: ValueKey('hiddenWidget'),
                        ), // Empty widget when hidden
                ),
                AppMainSubmitButton(
                  isActive: addMoneyController.amountController.text.trim().isNotEmpty,
                  text: MyStrings.continueText,
                  isLoading: addMoneyController.submitLoading,
                  onTap: () {
                    if (formKey.currentState?.validate() ?? false) {
                      addMoneyController.submitDeposit();
                    }
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  //Charge Calculator
  Widget buildChargeInfoWidget(AddMoneyController addMoneyController) {
    return CustomAppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          buildChargeRow(
            firstText: MyStrings.amount.tr,
            lastText: "${AppConverter.formatNumber(addMoneyController.mainAmount.toString(), precision: 2)} ${addMoneyController.currency}",
          ),
          buildChargeRow(
            firstText: MyStrings.charge.tr,
            lastText: addMoneyController.charge,
          ),
          buildChargeRow(
            firstText: MyStrings.conversionRate.tr,
            lastText: addMoneyController.conversionRate,
          ),
          buildChargeRow(
            firstText: "${MyStrings.in_.tr} ${addMoneyController.selectedAddMoneyMethod?.currency ?? ""}",
            lastText: addMoneyController.inMethodPayable,
          ),
          buildChargeRow(
            firstText: MyStrings.payable,
            lastText: addMoneyController.payableText,
          ),
        ],
      ),
    );
  }

  Widget buildChargeRow({required String firstText, required String lastText}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: Dimensions.space5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Flexible(
            child: Text(
              firstText.tr,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getHeaderTextColor(),
              ),
              overflow: TextOverflow.ellipsis,
              maxLines: 1,
            ),
          ),
          Flexible(
            child: Text(
              lastText,
              maxLines: 2,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getBodyTextColor(),
                fontWeight: FontWeight.normal,
              ),
              overflow: TextOverflow.ellipsis,
              textAlign: TextAlign.end,
            ),
          ),
        ],
      ),
    );
  }
}
