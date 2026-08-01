import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:get/get.dart';

class CustomListTileInformationWidgetCard extends StatelessWidget {
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final double width;
  final Color backgroundColor;
  final double radius;
  final VoidCallback? onPressed;
  final String? title;
  final String? subtitle;
  final Widget? customWidget;
  final TextStyle? titleStyle;
  final TextStyle? subtitleStyle;

  const CustomListTileInformationWidgetCard({
    super.key,
    this.width = double.infinity,
    this.backgroundColor = MyColor.transparentColor,
    this.radius = Dimensions.largeRadius,
    this.onPressed,
    this.title,
    this.subtitle,
    this.customWidget,
    this.titleStyle,
    this.subtitleStyle,
    this.padding,
    this.margin,
  });

  @override
  Widget build(BuildContext context) {
    return CustomAppCard(
      onPressed: onPressed,
      width: width,
      radius: radius.r,
      margin: margin,
      padding: padding ??
          EdgeInsetsDirectional.symmetric(
            vertical: Dimensions.space12.w,
            horizontal: Dimensions.space16.w,
          ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            (title ?? "").tr,
            style: titleStyle ??
                MyTextStyle.caption1Style.copyWith(
                  color: MyColor.getBodyTextColor(),
                ),
          ),
          if (subtitle != null) ...[
            spaceDown(Dimensions.space4),
            SelectableText(
              (subtitle ?? "").tr,
              style: subtitleStyle ??
                  MyTextStyle.sectionTitle2.copyWith(
                    color: MyColor.getDarkColor(),
                  ),
            ),
          ],
          if (customWidget != null) ...[
            spaceDown(Dimensions.space4),
            customWidget!,
          ],
        ],
      ),
    );
  }
}
