import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/text/default_text.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/statements/controller/statement_history_controller.dart';

import '../../../../../core/utils/util_exporter.dart';
import '../../../../core/data/services/service_exporter.dart';

class StatementBalanceHistoryCard extends StatelessWidget {
  const StatementBalanceHistoryCard({super.key, required this.controller});
  final StatementHistoryController controller;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CustomAppCard(
          child: Column(
            children: [
              buildHistoryLIstTile(
                title: MyStrings.startingBalance,
                subTitle: "${AppConverter.formatNumber(controller.statementsData?.startingBalance ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
              ),
              buildHistoryLIstTile(
                title: MyStrings.monthlyTransaction,
                subTitle: "${AppConverter.formatNumber(controller.statementsData?.totalTransactionAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
              ),
              buildHistoryLIstTile(
                title: MyStrings.numberOfTransaction,
                subTitle: (controller.statementsData?.totalTransactionCount ?? "0"),
                showBorder: false,
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget buildHistoryLIstTile({
    required String title,
    required String subTitle,
    bool showBorder = true,
  }) {
    return Container(
      padding: EdgeInsetsDirectional.symmetric(vertical: Dimensions.space10.h),
      decoration: BoxDecoration(
        border: showBorder == false ? null : Border(bottom: BorderSide(color: MyColor.getBorderColor())),
      ),
      child: Row(
        children: [
          Expanded(
            child: DefaultText(
              text: title,
              textStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
            ),
          ),
          Expanded(
            flex: 2,
            child: HeaderTextSmaller(
              text: subTitle,
              textAlign: TextAlign.end,
              textStyle: MyTextStyle.sectionBodyBoldTextStyle.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
