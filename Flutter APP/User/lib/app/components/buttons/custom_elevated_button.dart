import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class CustomElevatedBtn extends StatelessWidget {
  final String text;
  final void Function() onTap;
  final double radius;
  final double elevation;
  final Color? bgColor;
  final Color? textColor;
  final Color borderColor;
  final Color? shadowColor;
  final double width;
  final double height;
  final Widget? icon;
  final bool isLoading;
  final IconAlignment iconAlignment;
  const CustomElevatedBtn({
    super.key,
    required this.text,
    required this.onTap,
    this.radius = Dimensions.largeRadius,
    this.elevation = 0,
    this.bgColor,
    this.shadowColor,
    this.width = double.infinity,
    this.height = Dimensions.defaultButtonH,
    this.icon,
    this.isLoading = false,
    this.textColor = MyColor.white,
    this.borderColor = MyColor.transparentColor,
    this.iconAlignment = IconAlignment.start,
  });

  @override
  Widget build(BuildContext context) {
    return icon != null
        ? ElevatedButton.icon(
            iconAlignment: iconAlignment,
            icon: isLoading ? const SizedBox.shrink() : icon ?? const SizedBox.shrink(),
            onPressed: () {
              if (isLoading == false) {
                MyUtils.clearAllTypeFocusNodes();
                onTap();
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: bgColor ?? MyColor.getPrimaryColor(),
              elevation: elevation,
              surfaceTintColor: bgColor ?? MyColor.getPrimaryColor().withValues(alpha: 0.5),
              overlayColor: bgColor ??
                  MyColor.getPrimaryColor().withValues(
                    alpha: 0.1,
                  ), // Set your splash color
              shadowColor: shadowColor ?? MyColor.getPrimaryColor().withValues(alpha: 0.5),
              shape: RoundedRectangleBorder(
                side: BorderSide(color: borderColor, width: 1),
                borderRadius: BorderRadius.circular(radius.r),
              ),
              maximumSize: Size.fromHeight(height),
              minimumSize: Size(width, height),
              splashFactory: InkRipple.splashFactory,
            ),
            label: isLoading
                ? SizedBox(
                    height: 20,
                    width: 20,
                    child: CircularProgressIndicator(color: textColor),
                  )
                : Text(
                    text.tr,
                    style: MyTextStyle.buttonTextStyle1.copyWith(
                      color: textColor,
                    ),
                  ),
          )
        : ElevatedButton(
            onPressed: () {
              if (isLoading == false) {
                MyUtils.clearAllTypeFocusNodes();
                onTap();
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: bgColor ?? MyColor.getPrimaryColor(),
              elevation: elevation,
              shadowColor: shadowColor ?? MyColor.getPrimaryColor().withValues(alpha: 0.5),
              overlayColor: bgColor ??
                  MyColor.getPrimaryColor().withValues(
                    alpha: 0.1,
                  ), // Set your splash color h
              splashFactory: InkRipple.splashFactory,
              shape: RoundedRectangleBorder(
                side: BorderSide(color: borderColor, width: 1),
                borderRadius: BorderRadius.circular(radius.r),
              ),
              maximumSize: Size.fromHeight(height),
              minimumSize: Size(width, height),
            ),
            child: isLoading
                ? SizedBox(
                    height: 20,
                    width: 20,
                    child: CircularProgressIndicator(color: textColor),
                  )
                : Text(
                    text.tr,
                    style: MyTextStyle.buttonTextStyle1.copyWith(
                      color: textColor,
                    ),
                  ),
          );
  }
}
