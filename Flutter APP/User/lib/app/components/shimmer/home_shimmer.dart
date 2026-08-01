import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

class HomeShimmer extends StatelessWidget {
  const HomeShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      child: Padding(
        padding: EdgeInsets.all(16.w),
        child: Skeletonizer.zone(
          enabled: true,
          child: Column(
            children: [
              CustomAppCard(
                child: Padding(
                  padding: EdgeInsetsDirectional.symmetric(
                    horizontal: Dimensions.space12.w,
                  ),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          //Wallet
                          Bone.circle(size: Dimensions.space24.h),
                          spaceSide(Dimensions.space8),
                          Expanded(child: Bone.text()),
                          //Qr Code
                          Bone.circle(size: Dimensions.space40.h),
                        ],
                      ),
                      spaceDown(Dimensions.space15),
                      Bone.text(style: TextStyle(fontSize: 26)),
                      spaceDown(Dimensions.space16),
                      FittedBox(
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.center,
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Bone.circle(size: Dimensions.space50.h),
                            spaceSide(Dimensions.space10),
                            Bone.circle(size: Dimensions.space50.h),
                            spaceSide(Dimensions.space10),
                            Bone.circle(size: Dimensions.space50.h),
                            spaceSide(Dimensions.space10),
                            Bone.circle(size: Dimensions.space50.h),
                            spaceSide(Dimensions.space10),
                            Bone.circle(size: Dimensions.space50.h),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              spaceDown(Dimensions.space10),
              CustomAppCard(
                width: double.infinity,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Bone.text(words: 2),
                    spaceDown(Dimensions.space8),
                    Bone.multiText(lines: 3, style: TextStyle(fontSize: 16)),
                  ],
                ),
              ),
              spaceDown(Dimensions.space10),
              CustomAppCard(
                height: MediaQuery.of(context).size.height,
                child: TransactionHistoryShimmer(),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
