import 'package:flutter/material.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class CustomAppCard extends StatelessWidget {
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final double? width;
  final double? height;
  final Color backgroundColor;
  final Color borderColor;
  final double borderWidth;
  final double radius;
  final VoidCallback? onPressed;
  final Widget child;
  final BoxBorder? boxBorder;
  final bool showBorder;

  const CustomAppCard({
    super.key,
    this.width,
    this.height,
    this.backgroundColor = MyColor.white,
    this.borderColor = MyColor.borderColor1,
    this.borderWidth = 1,
    this.radius = Dimensions.cardExtraRadius,
    this.onPressed,
    required this.child,
    this.padding,
    this.margin,
    this.boxBorder,
    this.showBorder = true,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onPressed,
      child: Container(
        width: width,
        height: height,
        padding: padding ?? EdgeInsets.all(16.w),
        margin: margin,
        decoration: BoxDecoration(
          color: backgroundColor,
          borderRadius: BorderRadius.circular(radius.r),
          border: showBorder ? boxBorder ?? Border.all(color: borderColor, width: borderWidth) : null,
        ),
        child: child,
      ),
    );
  }
}
