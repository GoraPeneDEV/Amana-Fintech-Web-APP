import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/core/utils/app_style.dart';
import 'package:ovopay/core/utils/dimensions.dart';
import 'package:skeletonizer/skeletonizer.dart';

class InvestmentPlanShimmer extends StatelessWidget {
  const InvestmentPlanShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Skeletonizer(
      enabled: true,
      enableSwitchAnimation: true,
      child: ListView.separated(
        itemCount: 10,
        separatorBuilder: (context, index) => spaceDown(Dimensions.space10),
        padding: EdgeInsets.symmetric(vertical: Dimensions.space16),
        itemBuilder: (context, index) {
          return CustomAppCard(
            margin: EdgeInsets.zero,
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Bone.text(),
                      spaceDown(Dimensions.space8),
                      Bone.text(
                        words: 2,
                      ),
                    ],
                  ),
                ),
                spaceSide(Dimensions.space16),
                Bone.circle(size: Dimensions.space40),
              ],
            ),
          );
        },
      ),
    );
  }
}
