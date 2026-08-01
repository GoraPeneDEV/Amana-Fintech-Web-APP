import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:ovopay/core/utils/app_style.dart';
import 'package:ovopay/core/utils/dimensions.dart';
import 'package:skeletonizer/skeletonizer.dart';

class GridCategoryShimmer extends StatelessWidget {
  const GridCategoryShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Skeletonizer(
      child: GridView.builder(
        padding: EdgeInsets.zero,
        shrinkWrap: true,
        itemCount: 8, // Dummy count for shimmer placeholders
        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 4,
        ),
        itemBuilder: (context, index) {
          return Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(Dimensions.largeRadius.r),
                child: Container(
                  width: Dimensions.space48.w,
                  height: Dimensions.space48.h,
                  color: Colors.green,
                ),
              ),
              spaceDown(Dimensions.space4),
              Container(height: 12, width: 50, color: Colors.green),
            ],
          );
        },
      ),
    );
  }
}
