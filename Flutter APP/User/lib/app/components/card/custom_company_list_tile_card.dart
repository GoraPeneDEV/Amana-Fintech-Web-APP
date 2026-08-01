import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class CustomCompanyListTileCard extends StatelessWidget {
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final double width;
  final Color backgroundColor;
  final double radius;
  final VoidCallback? onPressed;
  final String? imagePath;
  final String? title;
  final String? trailingTitle;
  final String? subtitle;
  final String? trailingSubtitle;
  final TextStyle? titleStyle;
  final TextStyle? subtitleStyle;
  final Widget? leading;
  final Widget? trailing;
  final Widget? subtitleWidget;
  final bool? showBorder;
  final bool isPress;

  const CustomCompanyListTileCard({
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
    this.subtitleWidget,
    this.isPress = false,
    this.padding,
    this.margin,
    this.showBorder = true,
    this.trailingTitle,
    this.trailingSubtitle,
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
            leading: leading ??
                CustomAppCard(
                  width: Dimensions.space45.w,
                  height: Dimensions.space45.w,
                  radius: Dimensions.badgeRadius.r,
                  padding: EdgeInsetsDirectional.all(Dimensions.space4.w),
                  child: MyNetworkImageWidget(
                    imageUrl: imagePath ?? "",
                    isProfile: true,
                    width: Dimensions.space40.w,
                    height: Dimensions.space40.w,
                  ),
                ),
            title: Padding(
              padding: EdgeInsetsDirectional.only(bottom: Dimensions.space3.w),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Flexible(
                    flex: 2,
                    child: Text(
                      (title ?? "").tr,
                      style: titleStyle ?? MyTextStyle.sectionTitle3,
                    ),
                  ),
                  if (trailingTitle != null) ...[
                    spaceSide(Dimensions.space10),
                    Flexible(
                      child: FittedBox(
                        fit: BoxFit.scaleDown,
                        child: Text(
                          textAlign: TextAlign.end,
                          (trailingTitle ?? "").tr,
                          style: subtitleStyle ?? MyTextStyle.sectionSubTitle1,
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ),
            subtitle: subtitleWidget ??
                (subtitle == null
                    ? null
                    : Padding(
                        padding: EdgeInsetsDirectional.only(
                          bottom: Dimensions.space3.w,
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Flexible(
                              flex: 2,
                              child: Text(
                                (subtitle ?? "").tr,
                                style: MyTextStyle.sectionSubTitle1,
                              ),
                            ),
                            if (trailingSubtitle != null) ...[
                              spaceSide(Dimensions.space10),
                              Flexible(
                                flex: 2,
                                child: FittedBox(
                                  fit: BoxFit.scaleDown,
                                  child: Text(
                                    textAlign: TextAlign.end,
                                    trailingSubtitle ?? "",
                                    style: MyTextStyle.sectionTitle3,
                                  ),
                                ),
                              ),
                            ],
                          ],
                        ),
                      )),
            trailing: trailing,
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
    );
  }
}
