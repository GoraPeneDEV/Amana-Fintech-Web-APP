import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/screens/transaction_history/views/widgets/custom_main_transactions_list_tile_card.dart';
import 'package:ovopay/core/utils/app_style.dart';
import 'package:ovopay/core/utils/dimensions.dart';
import 'package:ovopay/core/utils/my_color.dart';
import 'package:ovopay/core/utils/text_style.dart';
import 'package:skeletonizer/skeletonizer.dart';

class InvestmentHistoryShimmer extends StatelessWidget {
  const InvestmentHistoryShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Skeletonizer(
      enabled: true,
      enableSwitchAnimation: true,
      child: ListView.separated(
        itemCount: 10,
        separatorBuilder: (context, index) => spaceDown(Dimensions.space10),
        itemBuilder: (context, index) {
          return CustomAppCard(
            radius: Dimensions.largeRadius,
            padding: EdgeInsets.symmetric(horizontal: Dimensions.space16),
            child: CustomMainTransactionListTileCard(
              leading: Container(
                width: 40,
                height: 40,
                color: MyColor.accent1,
              ),
              title: "Jacob Jones",
              subtitle: "Completed",
              subtitleStyle: MyTextStyle.sectionSubTitle1,
              balance: "\$56.00",
              date: "12-10-2024 6:45",
              remark: "1",
            ),
          );
        },
      ),
    );
  }
}
