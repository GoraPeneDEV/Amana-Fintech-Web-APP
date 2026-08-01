import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/statements/controller/statement_history_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../../../core/utils/util_exporter.dart';

class StatementBalanceSwapCard extends StatelessWidget {
  const StatementBalanceSwapCard({super.key, required this.controller});
  final StatementHistoryController controller;
  @override
  Widget build(BuildContext context) {
    return CustomAppCard(
      backgroundColor: controller.isStatementLoading ? MyColor.getWhiteColor() : MyColor.getPrimaryColor(),
      radius: Dimensions.cardExtraRadius.r,
      padding: EdgeInsets.zero,
      margin: EdgeInsets.zero,
      showBorder: controller.isStatementLoading,
      child: Column(
        children: [
          //Date Swap card
          Container(
            padding: EdgeInsets.symmetric(
              vertical: Dimensions.space5.h,
              horizontal: Dimensions.space5.w,
            ),
            decoration: BoxDecoration(
              color: MyColor.getWhiteColor().withValues(alpha: 0.2),
              borderRadius: BorderRadiusDirectional.only(
                topStart: Radius.circular(Dimensions.cardExtraRadius.r),
                topEnd: Radius.circular(Dimensions.cardExtraRadius.r),
              ),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                IconButton(
                  onPressed: () {
                    controller.getStatementsHistoryDataList(
                      forceLoad: true,
                      forceDate: true,
                      isIncrease: false,
                    );
                  },
                  icon: Icon(
                    Icons.arrow_circle_left_outlined,
                    size: Dimensions.space25,
                    color: MyColor.getWhiteColor(),
                  ),
                ),
                HeaderTextSmaller(
                  text: "${controller.monthInText} ${controller.year}",
                  textAlign: TextAlign.center,
                  textStyle: MyTextStyle.sectionTitle.copyWith(
                    color: MyColor.getWhiteColor(),
                  ),
                ),
                IconButton(
                  onPressed: () {
                    controller.getStatementsHistoryDataList(
                      forceLoad: true,
                      forceDate: true,
                      isIncrease: true,
                    );
                  },
                  icon: Icon(
                    Icons.arrow_circle_right_outlined,
                    size: Dimensions.space25,
                    color: MyColor.getWhiteColor(),
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: EdgeInsets.symmetric(
              vertical: Dimensions.space16.h,
              horizontal: Dimensions.space16.w,
            ),
            child: Column(
              children: [
                //Wallet
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    MyAssetImageWidget(
                      isSvg: true,
                      assetPath: MyIcons.walletIcon,
                      width: Dimensions.space24.w,
                      height: Dimensions.space24.w,
                    ),
                    spaceSide(Dimensions.space8),
                    Text(
                      MyStrings.currentBalance.tr,
                      style: MyTextStyle.bodyTextStyle1.copyWith(
                        color: MyColor.getWhiteColor(),
                      ),
                    ),
                  ],
                ),
                spaceDown(Dimensions.space8),
                Text(
                  "${AppConverter.formatNumber(controller.statementsData?.currentBalance ?? "0", forceShowPrecision: true)} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                  overflow: TextOverflow.ellipsis,
                  style: MyTextStyle.balanceCardTextStyle.copyWith(
                    color: MyColor.getWhiteColor(),
                  ),
                  maxLines: 1,
                ),
                spaceDown(Dimensions.space8),
                Text(
                  "${MyStrings.lastUpdate.tr} ${DateConverter.estimatedDateOrTime(DateTime.now())}",
                  style: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getWhiteColor(),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
