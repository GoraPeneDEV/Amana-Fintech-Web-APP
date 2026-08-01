import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

class CardScreenShimmer extends StatelessWidget {
  const CardScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Skeletonizer.zone(
      enabled: true,
      child: Column(
        children: [
          CustomAppCard(
            width: double.infinity,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [Bone.text(words: 2), Bone.square(size: 30)],
                ),
                spaceDown(Dimensions.space5),
                Bone.text(style: TextStyle(fontSize: 15)),
                spaceDown(Dimensions.space25),
                Bone.text(words: 1, style: TextStyle(fontSize: 30)),
                spaceDown(Dimensions.space30),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(flex: 2, child: Bone.text(words: 3)),
                    spaceSide(Dimensions.space10),
                    Expanded(child: Bone.text(words: 2)),
                  ],
                ),
              ],
            ),
          ),
          spaceDown(Dimensions.space10),
          CustomAppCard(
            width: double.infinity,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [Bone.text(style: TextStyle(fontSize: 25))],
            ),
          ),
          spaceDown(Dimensions.space10),
          Expanded(child: CustomAppCard(child: TransactionHistoryShimmer())),
        ],
      ),
    );
  }
}
