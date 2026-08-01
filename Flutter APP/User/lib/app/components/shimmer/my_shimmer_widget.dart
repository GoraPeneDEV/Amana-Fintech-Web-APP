import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

import '../../../core/utils/my_color.dart';

class MyShimmerWidget extends StatelessWidget {
  final Widget child;
  final Color? baseColor;
  final Color? highlightColor;

  const MyShimmerWidget({
    super.key,
    required this.child,
    this.baseColor,
    this.highlightColor,
  });

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: baseColor ?? MyColor.getBodyTextColor().withValues(alpha: 0.1),
      highlightColor: highlightColor ?? MyColor.getPrimaryColor().withValues(alpha: 0.07),
      child: child,
    );
  }
}
