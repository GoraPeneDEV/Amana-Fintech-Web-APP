import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/chip/custom_chip.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/bill_pay_screen/controller/bill_pay_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import '../../../../../core/utils/util_exporter.dart';

class BillPayAmountPage extends StatefulWidget {
  const BillPayAmountPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final VoidCallback onSuccessCallback;
  final BuildContext context;

  @override
  State<BillPayAmountPage> createState() => _BillPayAmountPageState();
}

class _BillPayAmountPageState extends State<BillPayAmountPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<BillPayController>(
      builder: (billPayController) {
        return SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Column(
              children: [
                if (billPayController.selectedUtilityCompany != null) ...[
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CustomCompanyListTileCard(
                          padding: EdgeInsets.zero,
                          imagePath: "${billPayController.selectedUtilityCompany?.getCompanyImageUrl()}",
                          title: "${billPayController.selectedUtilityCompany?.name}",
                          subtitle: billPayController.utilityCategoryDataList
                                  .firstWhereOrNull(
                                    (e) => e.id?.toString() == billPayController.selectedUtilityCompany?.categoryId?.toString(),
                                  )
                                  ?.formattedName ??
                              "",
                          trailingTitle: MyStrings.customerID.tr,
                          trailingSubtitle: "${billPayController.selectedUtilityCompany?.id}",
                          showBorder: false,
                        ),
                      ],
                    ),
                  ),
                ],
                spaceDown(Dimensions.space16),
                CustomAppCard(
                  width: double.infinity,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      HeaderText(
                        text: MyStrings.enterAmount.tr,
                        textStyle: MyTextStyle.headerH3.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                      ),
                      if (billPayController.selectedUtilityCompany?.denominationType == AppStatus.range) ...[
                        spaceDown(Dimensions.space24),
                        RoundedTextField(
                          controller: billPayController.amountController,
                          showLabelText: false,
                          labelText: MyStrings.enterAmount.tr,
                          hintText: "${AppConverter.formatNumberDouble(billPayController.selectedUtilityCompany?.minimumAmount ?? (billPayController.globalChargeModel?.minLimit ?? "0"), precision: 2)}-${AppConverter.formatNumberDouble(billPayController.selectedUtilityCompany?.maximumAmount ?? (billPayController.globalChargeModel?.maxLimit ?? "0"), precision: 2)}",
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
                            billPayController.onChangeAmountControllerText(value);
                          },
                          validator: (value) {
                            return MyUtils().validateAmountForm(
                              value: value ?? '0',
                              userCurrentBalance: billPayController.userCurrentBalance,
                              minLimit: AppConverter.formatNumberDouble(
                                billPayController.selectedUtilityCompany?.minimumAmount ?? (billPayController.globalChargeModel?.minLimit ?? "0"),
                                precision: 2,
                              ),
                              maxLimit: AppConverter.formatNumberDouble(
                                billPayController.selectedUtilityCompany?.maximumAmount ?? (billPayController.globalChargeModel?.maxLimit ?? "0"),
                                precision: 2,
                              ),
                            );
                          },
                        ),
                      ] else ...[
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            spaceDown(Dimensions.space16),
                            Wrap(
                                runSpacing: Dimensions.space8.h,
                                spacing: Dimensions.space8.w,
                                children: List.generate(billPayController.selectedUtilityCompany?.fixedAmounts?.length ?? 0, (index) {
                                  var fixedAmount = billPayController.selectedUtilityCompany?.fixedAmounts?[index];

                                  return GestureDetector(
                                    onTap: () {
                                      billPayController.onChangeFixedAmountControllerText(fixedAmount?.amount ?? "", fixedAmount?.id ?? "");
                                    },
                                    child: CustomAppCard(
                                      radius: Dimensions.largeRadius,
                                      padding: EdgeInsets.symmetric(horizontal: Dimensions.space16, vertical: Dimensions.space10),
                                      backgroundColor: billPayController.amountController.text == (fixedAmount?.amount) ? MyColor.getPrimaryColor().withValues(alpha: .1) : MyColor.getWhiteColor(),
                                      borderColor: billPayController.amountController.text == (fixedAmount?.amount) ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                                      child: Text("${AppConverter.formatNumber(fixedAmount?.amount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}", style: MyTextStyle.caption1Style.copyWith(color: MyColor.getPrimaryColor(), fontWeight: FontWeight.w600)),
                                    ),
                                  );
                                })),
                          ],
                        ),
                      ],
                      spaceDown(Dimensions.space8),
                      Text.rich(
                        TextSpan(
                          children: [
                            TextSpan(
                              text: "${MyStrings.availableBalance.tr}: ",
                              style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                color: MyColor.getBodyTextColor(),
                              ),
                            ),
                            TextSpan(
                              text: MyUtils.getUserAmount(
                                billPayController.userCurrentBalance.toString(),
                              ),
                              style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(color: MyColor.getPrimaryColor()),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                if (billPayController.otpType.isNotEmpty) ...[
                  spaceDown(Dimensions.space16),
                  CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        HeaderText(
                          text: MyStrings.verificationType,
                          textStyle: MyTextStyle.sectionTitle2.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        Row(
                          children: billPayController.otpType.map((value) {
                            return CustomAppChip(
                              backgroundColor: MyColor.getWhiteColor(),
                              isSelected: value == billPayController.selectedOtpType,
                              text: billPayController.getOtpType(value),
                              onTap: () => billPayController.selectAnOtpType(
                                value,
                              ),
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),
                ],
                spaceDown(Dimensions.space15),
                AppMainSubmitButton(
                  isLoading: billPayController.isSubmitLoading,
                  isActive: billPayController.amountController.text.trim().isNotEmpty,
                  text: MyStrings.next,
                  onTap: () {
                    if (billPayController.selectedOtpType == "") {
                      if (billPayController.otpType.isNotEmpty) {
                        CustomSnackBar.error(
                          errorList: [MyStrings.pleaseSelectAnOtpType.tr],
                        );
                        return;
                      }
                    }
                    if (formKey.currentState?.validate() ?? false) {
                      billPayController.submitThisProcess(
                        onSuccessCallback: (value) {
                          widget.onSuccessCallback();
                        },
                        onVerifyOtpCallback: (value) async {
                          await AppDialogs.verifyOtpPopUpWidget(
                            context,
                            onSuccess: (value) async {
                              Navigator.pop(context);
                              widget.onSuccessCallback();
                            },
                            title: '',
                            actionRemark: billPayController.actionRemark,
                            otpType: billPayController.selectedOtpType,
                          );
                          return;
                        },
                      );
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
}
