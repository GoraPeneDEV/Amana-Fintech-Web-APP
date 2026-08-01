import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../packages/auto_height_grid_view/src/auto_height_grid_view.dart';

class BuildShimmerGridWidget extends StatelessWidget {
  const BuildShimmerGridWidget({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return Skeletonizer.zone(
      enabled: true,
      child: CustomAppCard(
        padding: EdgeInsets.symmetric(
          vertical: Dimensions.space20.w,
          horizontal: Dimensions.space20.w,
        ),
        radius: Dimensions.cardExtraRadius.r,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Bone.square(
              size: 50,
            ),
            spaceDown(Dimensions.space5.h),
            Bone.text(fontSize: 15),
            spaceDown(Dimensions.space4.h),
            Bone.text()
          ],
        ),
      ),
    );
  }
}

class GiftScreenShimmer extends StatelessWidget {
  final int itemCount;
  final ScrollPhysics? physics;
  const GiftScreenShimmer({super.key, this.itemCount = 20, this.physics});

  @override
  Widget build(BuildContext context) {
    return Skeletonizer.zone(
      enabled: true,
      child: AutoHeightGridView(
        itemCount: itemCount,
        crossAxisCount: 2,
        mainAxisSpacing: Dimensions.space15.w,
        crossAxisSpacing: Dimensions.space15.w,
        physics: physics ?? const BouncingScrollPhysics(),
        padding: EdgeInsets.all(Dimensions.space16),
        shrinkWrap: true,
        builder: (context, index) {
          return BuildShimmerGridWidget();
        },
      ),
    );
  }
}
