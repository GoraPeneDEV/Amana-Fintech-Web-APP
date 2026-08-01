import 'package:flutter/material.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class CustomListTileCard extends StatelessWidget {
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final double width;
  final double? imageWidth;
  final double? imageHeight;
  final Widget? leading;
  final Color backgroundColor;
  final double radius;
  final VoidCallback? onPressed;
  final String? imagePath;
  final String? title;
  final Widget? titleWidget;
  final String? subtitle;
  final Widget? subtitleWidget;
  final TextStyle? titleStyle;
  final TextStyle? subtitleStyle;
  final Widget? trailing;
  final bool? showBorder;
  final bool? showLeading;
  final bool isPress;

  const CustomListTileCard({
    super.key,
    this.width = double.infinity,
    this.backgroundColor = MyColor.transparentColor,
    this.radius = 0,
    this.onPressed,
    this.imagePath,
    this.title,
    this.subtitle,
    this.subtitleWidget,
    this.titleStyle,
    this.subtitleStyle,
    this.trailing,
    this.isPress = false,
    this.padding,
    this.margin,
    this.showBorder = true,
    this.showLeading = true,
    this.imageWidth,
    this.imageHeight,
    this.leading,
    this.titleWidget,
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
            horizontalTitleGap: 8.w,
            leading: showLeading == true
                ? (leading ??
                    MyNetworkImageWidget(
                      imageUrl: imagePath ?? "",
                      isProfile: false,
                      width: imageWidth ?? Dimensions.space40.w,
                      height: imageWidth ?? Dimensions.space40.w,
                    ))
                : null,
            title: titleWidget ??
                (title != null
                    ? Text(
                        title ?? "",
                        style: titleStyle ?? MyTextStyle.sectionTitle3,
                      )
                    : null),
            subtitle: subtitleWidget ??
                (subtitle != null
                    ? Text(
                        subtitle ?? "",
                        style: subtitleStyle ??
                            MyTextStyle.sectionSubTitle1.copyWith(
                              color: MyColor.getPrimaryColor(),
                            ),
                      )
                    : null),
            trailing: trailing,
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
    );
  }
}
