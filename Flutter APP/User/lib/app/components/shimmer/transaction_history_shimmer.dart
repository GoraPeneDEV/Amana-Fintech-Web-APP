import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/screens/transaction_history/views/widgets/custom_main_transactions_list_tile_card.dart';
import 'package:ovopay/core/utils/app_style.dart';
import 'package:ovopay/core/utils/dimensions.dart';
import 'package:ovopay/core/utils/my_color.dart';
import 'package:ovopay/core/utils/text_style.dart';
import 'package:skeletonizer/skeletonizer.dart';

class TransactionHistoryShimmer extends StatelessWidget {
  final bool isGrid;
  const TransactionHistoryShimmer({super.key, this.isGrid = false});

  @override
  Widget build(BuildContext context) {
    return Skeletonizer(
      enabled: true,
      enableSwitchAnimation: true,
      child: isGrid
          ? GridView.builder(
              padding: const EdgeInsets.symmetric(
                horizontal: 0,
                vertical: Dimensions.space20,
              ),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 1,
                crossAxisSpacing: 10,
                mainAxisSpacing: 10,
              ),
              itemCount: 10,
              itemBuilder: (context, index) {
                return CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      SizedBox(
                        width: double.infinity,
                        child: Bone.square(size: Dimensions.space60),
                      ),
                      spaceDown(Dimensions.space10),
                      Bone.text(width: 100),
                    ],
                  ),
                );
              },
            )
          : ListView.builder(
              itemCount: 10,
              itemBuilder: (context, index) {
                return CustomMainTransactionListTileCard(
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
                  onPressed: () {},
                  remark: "1",
                );
              },
            ),
    );
  }
}
