import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class CustomContactListTileCard extends StatelessWidget {
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final double width;
  final Color backgroundColor;
  final double radius;
  final VoidCallback? onPressed;
  final String? imagePath;
  final String? title;
  final String? subtitle;
  final TextStyle? titleStyle;
  final TextStyle? subtitleStyle;
  final Widget? leading;
  final Widget? trailing;
  final bool? showBorder;
  final bool showLeading;
  final bool isPress;

  const CustomContactListTileCard({
    super.key,
    this.width = double.infinity,
    this.backgroundColor = MyColor.transparentColor,
    this.radius = 0,
    this.onPressed,
    this.imagePath,
    this.title,
    this.subtitle,
    this.titleStyle,
    this.subtitleStyle,
    this.leading,
    this.trailing,
    this.isPress = false,
    this.padding,
    this.margin,
    this.showBorder = true,
    this.showLeading = true,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      type: MaterialType.transparency,
      child: InkWell(
        borderRadius: BorderRadius.circular(5),
        onTap: onPressed,
        child: Container(
          width: width,
          padding: padding ?? EdgeInsetsDirectional.symmetric(vertical: Dimensions.space10.w),
          margin: margin,
          decoration: BoxDecoration(
            color: backgroundColor,
            borderRadius: BorderRadius.circular(radius),
            border: showBorder == true
                ? Border(
                    bottom: BorderSide(
                      color: MyColor.getBorderColor(),
                      width: 1,
                    ),
                  )
                : null,
          ),
          child: ListTile(
            minTileHeight: 0,
            minVerticalPadding: 0,
            horizontalTitleGap: showLeading ? 8.w : 0.w,
            minLeadingWidth: !showLeading ? 0 : null,
            leading: showLeading
                ? leading ??
                    Ink(
                      width: Dimensions.space40.w,
                      height: Dimensions.space40.w,
                      decoration: BoxDecoration(
                        image: DecorationImage(
                          image: CachedNetworkImageProvider(imagePath ?? ""),
                          fit: BoxFit.cover,
                        ),
                      ),
                    )
                : SizedBox(width: 0.w),
            title: title?.isEmptyString == true
                ? null
                : Padding(
                    padding: EdgeInsetsDirectional.only(
                      bottom: Dimensions.space3.w,
                    ),
                    child: Text(
                      title ?? "",
                      style: titleStyle ?? MyTextStyle.sectionTitle3,
                    ),
                  ),
            subtitle: subtitle?.isEmptyString == true
                ? null
                : Text(
                    subtitle ?? "",
                    style: subtitleStyle ??
                        MyTextStyle.sectionSubTitle1.copyWith(
                          color: MyColor.getPrimaryColor(),
                        ),
                  ),
            trailing: trailing,
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
    );
  }
}
