import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import 'package:get/utils.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/screens/transaction_history/views/widgets/transaction_history_bottom_sheet_details_card.dart';
import 'package:ovopay/core/data/models/transaction_history/transaction_history_model.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class TransActionIconItem {
  final String icon;
  final List<String> label;
  final bool isActive;
  final Color activeColor;
  final VoidCallback? onTap;

  TransActionIconItem({
    this.onTap,
    required this.icon,
    required this.label,
    this.isActive = false,
    required this.activeColor,
  });
}

final List<TransActionIconItem> transactionIconsList = [
  TransActionIconItem(
    icon: MyIcons.rechargeIcon,
    label: ["mobile_recharge", "reject_mobile_recharge"],
    isActive: true,
    activeColor: MyColor.getPrimaryColor(),
  ),
  TransActionIconItem(
    icon: MyIcons.rechargeIcon,
    label: ["top_up"],
    isActive: true,
    activeColor: MyColor.violateColor,
  ),
  TransActionIconItem(
    icon: MyIcons.giftIcon,
    label: ["gift_card_purchase"],
    isActive: true,
    activeColor: MyColor.skyBlueColor,
  ),
  TransActionIconItem(
    icon: MyIcons.sendIcon,
    label: ["send_money"],
    isActive: true,
    activeColor: MyColor.orangeColor,
  ),
  TransActionIconItem(
    icon: MyIcons.requestIcon,
    label: [
      "request_money_accept",
      "requested_money_fund_added",
      "receive_money",
      "cash_in",
    ],
    isActive: true,
    activeColor: MyColor.orangeColor,
  ),
  TransActionIconItem(
    icon: MyIcons.cashOutIcon,
    label: ["cash_out"],
    isActive: true,
    activeColor: MyColor.violateColor,
  ),
  TransActionIconItem(
    icon: MyIcons.walletAddIcon,
    label: ["add_money"],
    isActive: true,
    activeColor: MyColor.violateColor,
  ),
  TransActionIconItem(
    icon: MyIcons.paymentIcon,
    label: ["make_payment", "receive_payment"],
    isActive: true,
    activeColor: MyColor.goldenColor,
  ),
  TransActionIconItem(
    icon: MyIcons.paymentIcon,
    label: ["withdraw"],
    isActive: true,
    activeColor: MyColor.skyBlueColor,
  ),
  TransActionIconItem(
    icon: "balance_subtract",
    label: ["balance_subtract"],
    isActive: true,
    activeColor: MyColor.error,
  ),
  TransActionIconItem(
    icon: "balance_add",
    label: ["balance_add", "cashback"],
    isActive: true,
    activeColor: MyColor.success,
  ),
  TransActionIconItem(
    icon: MyIcons.cardIcon,
    label: ["virtual_card_add_fund"],
    isActive: true,
    activeColor: MyColor.greenLightColor,
  ),
  TransActionIconItem(
    icon: MyIcons.billPay,
    label: ["utility_bill", "reject_utility_bill"],
    isActive: true,
    activeColor: MyColor.indigoColor,
  ),
  TransActionIconItem(
    icon: MyIcons.savingsIcon,
    label: ["microfinance", "reject_microfinance"],
    isActive: true,
    activeColor: MyColor.greenLightColor,
  ),
  TransActionIconItem(
    icon: MyIcons.bankTransferIcon,
    label: ["bank_transfer", "reject_bank_transfer"],
    isActive: true,
    activeColor: MyColor.goldenColor,
  ),
  TransActionIconItem(
    icon: MyIcons.educationIcon,
    label: ["education_fee", "reject_education_fee"],
    isActive: true,
    activeColor: MyColor.violateColor,
  ),
  TransActionIconItem(
    icon: MyIcons.donationIcon,
    label: ["donation"],
    isActive: true,
    activeColor: MyColor.redLightColor,
  ),
  TransActionIconItem(
    icon: MyIcons.investment,
    label: ["investment", "investment_interest", "investment_capital_back"],
    isActive: true,
    activeColor: MyColor.violateColor,
  ),
];

class CustomMainTransactionListTileCard extends StatelessWidget {
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final double width;
  final Color backgroundColor;
  final double radius;
  final VoidCallback? onPressed;
  final String? title;
  final String? subtitle;
  final TextStyle? subtitleStyle;
  final String? balance;
  final String? date;
  final String? trxType;
  final Widget? leading;
  final Widget? trailing;
  final bool? showBorder;
  final bool isPress;
  final String remark;
  final TransactionHistoryModel? item;

  const CustomMainTransactionListTileCard({
    super.key,
    this.width = double.infinity,
    this.backgroundColor = MyColor.transparentColor,
    this.radius = 0,
    this.onPressed,
    this.title,
    this.subtitle,
    this.leading,
    this.trailing,
    this.balance,
    this.date,
    this.trxType,
    this.isPress = false,
    this.padding,
    this.margin,
    this.showBorder = true,
    this.remark = "",
    this.subtitleStyle,
    this.item,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      type: MaterialType.transparency,
      child: InkWell(
        borderRadius: BorderRadius.circular(5),
        onTap: () {
          if (item != null) {
            CustomBottomSheetPlus(
              child: SafeArea(
                child: TransactionHistoryBottomSheetDetailsCard(
                  item: item!,
                  remarkTitle: title ?? "",
                  context: context,
                ),
              ),
            ).show(context);
          }
        },
        child: Container(
          width: width,
          padding: (padding ??
              EdgeInsetsDirectional.symmetric(
                vertical: Dimensions.space10.w,
              )),
          margin: margin,
          decoration: BoxDecoration(
            color: backgroundColor,
            borderRadius: BorderRadius.circular(radius),
            border: showBorder == true
                ? Border(
                    bottom: BorderSide(
                      color: MyColor.getBorderColor(),
                      width: 1,
                    ),
                  )
                : null,
          ),
          child: ListTile(
            minTileHeight: 0,
            minVerticalPadding: Dimensions.space8.h,
            horizontalTitleGap: Dimensions.space8.w,

            leading: leading ??
                buildDynamicIconItem(
                  transactionIconsList.firstWhereOrNull(
                    (e) => e.label.contains(remark),
                  ),
                ),
            title: Padding(
              padding: EdgeInsetsDirectional.only(bottom: Dimensions.space8.w),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Flexible(
                    flex: 2,
                    child: Text(title ?? "", style: MyTextStyle.sectionTitle3),
                  ),
                  spaceSide(Dimensions.space3),
                  Flexible(
                    child: FittedBox(
                      fit: BoxFit.scaleDown,
                      child: Text(
                        textAlign: TextAlign.end,
                        "${trxType ?? ""}${balance ?? ""}",
                        style: MyTextStyle.sectionTitle3.copyWith(
                          color: trxType == "+"
                              ? MyColor.success
                              : trxType == "-"
                                  ? MyColor.error
                                  : MyColor.getHeaderTextColor(),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            subtitle: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        subtitle ?? buildSubtitleTextItem(item),
                        style: subtitleStyle ??
                            MyTextStyle.sectionSubTitle1.copyWith(
                              fontWeight: FontWeight.w500,
                              color: MyColor.getBodyTextColor(),
                            ),
                      ),
                      spaceDown(Dimensions.space3),
                      FittedBox(
                        fit: BoxFit.scaleDown,
                        child: RichText(
                          text: TextSpan(
                            children: [
                              TextSpan(
                                text: "${MyStrings.trxId.tr}: ",
                                style: subtitleStyle ??
                                    MyTextStyle.caption1Style.copyWith(
                                      fontSize: Dimensions.fontSmall.sp,
                                      color: MyColor.getBodyTextColor(),
                                    ),
                              ),
                              TextSpan(
                                text: item?.trx ?? "",
                                style: subtitleStyle ??
                                    MyTextStyle.caption1Style.copyWith(
                                      fontSize: Dimensions.fontSmall.sp,
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                recognizer: TapGestureRecognizer()
                                  ..onTap = () {
                                    Clipboard.setData(
                                      ClipboardData(text: item?.trx ?? ""),
                                    ).then((_) {
                                      CustomSnackBar.showToast(
                                        message: MyStrings.copiedToClipBoard.tr,
                                      );
                                    });
                                  },
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                spaceSide(Dimensions.space10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      FittedBox(
                        fit: BoxFit.scaleDown,
                        child: Text(
                          textAlign: TextAlign.end,
                          date ?? "",
                          style: MyTextStyle.caption2Style.copyWith(
                            fontSize: Dimensions.fontExtraSmall.sp,
                            color: MyColor.getBodyTextColor(),
                          ),
                        ),
                      ),
                      spaceDown(Dimensions.space10),
                      Icon(
                        Icons.arrow_forward_ios_rounded,
                        color: MyColor.getBodyTextColor(),
                        size: Dimensions.space10.h,
                      ),
                    ],
                  ),
                ),
              ],
            ),
            // trailing: trailing,
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
    );
  }

  Widget buildDynamicIconItem(TransActionIconItem? service) {
    if (service == null) {
      return SizedBox.shrink();
    }
    return CustomAppCard(
      backgroundColor: service.activeColor.withValues(alpha: 0.1),
      borderColor: Colors.transparent,
      padding: EdgeInsets.all(Dimensions.space12.w),
      width: Dimensions.space48.h,
      height: Dimensions.space48.h,
      radius: Dimensions.largeRadius.r,
      child: service.icon == "balance_subtract"
          ? FittedBox(
              child: Icon(
                Icons.remove,
                size: Dimensions.space24.h,
                color: service.activeColor,
              ),
            )
          : service.icon == "balance_add"
              ? FittedBox(
                  child: Icon(
                    Icons.add,
                    size: Dimensions.space24.h,
                    color: service.activeColor,
                  ),
                )
              : MyAssetImageWidget(
                  isSvg: true,
                  assetPath: service.icon,
                  width: Dimensions.space24.h,
                  height: Dimensions.space24.h,
                  color: service.activeColor,
                ),
    );
  }

  String buildSubtitleTextItem(TransactionHistoryModel? item) {
    if (item == null) return "";

    final remark = item.remark ?? "";

    // Case 1: Remarks that should display `otherData.title`
    const remarksWithOtherData = {"send_money", "cash_in", "cash_out", "make_payment", "request_money_accept", "requested_money_fund_added", "receive_money", "education_fee", "reject_education_fee", "bank_transfer", "reject_bank_transfer", "utility_bill", "reject_utility_bill", "mobile_recharge", "reject_mobile_recharge", "top_up", "microfinance", "reject_microfinance", "donation", "gift_card_purchase", "investment", "investment_interest", "investment_capital_back"};

    if (remarksWithOtherData.contains(remark)) {
      return item.otherData?.title ?? "";
    }

    // Case 2: Remarks that should display `details`
    const remarksWithDetails = {
      "balance_subtract",
      "balance_add",
      "add_money",
      "virtual_card_add_fund",
      "cashback",
    };

    if (remarksWithDetails.contains(remark)) {
      return item.details ?? "";
    }

    // Default: just return the remark itself
    return remark;
  }
}
