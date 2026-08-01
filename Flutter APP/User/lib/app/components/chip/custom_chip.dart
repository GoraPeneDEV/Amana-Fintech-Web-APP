import 'package:flutter/material.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class CustomAppChip extends StatelessWidget {
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final Color backgroundColor;
  final Color? selectedColor;
  final double radius;
  final String text;
  final VoidCallback? onTap;
  final bool isSelected;

  const CustomAppChip({
    super.key,
    this.backgroundColor = MyColor.screenBGColor,
    this.selectedColor,
    this.radius = Dimensions.largeRadius,
    required this.text,
    this.onTap,
    this.padding,
    this.margin,
    this.isSelected = false,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: padding ?? EdgeInsets.all(14.w),
        margin: margin ?? EdgeInsetsDirectional.only(end: 16.w),
        decoration: BoxDecoration(
          color: (isSelected ? selectedColor ?? MyColor.getPrimaryColor() : backgroundColor).withValues(alpha: 0.09),
          borderRadius: BorderRadius.circular(radius.r),
          border: Border.all(
            color: isSelected ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
            width: 1,
          ),
        ),
        child: Text(
          text,
          style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(
            color: isSelected ? MyColor.getPrimaryColor() : MyColor.getHeaderTextColor(),
          ),
        ),
      ),
    );
  }
}
