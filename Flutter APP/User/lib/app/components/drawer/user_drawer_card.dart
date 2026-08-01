import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class DrawerUserCard extends StatelessWidget {
  final String? username, fullName;
  final String? image;
  final bool isAsset;
  final bool noAvatar;
  final TextStyle? titleStyle, subtitleStyle;
  final Widget? rightWidget;
  final Widget? imgWidget;
  final double? imgHeight;
  final double? imgWidth;
  const DrawerUserCard({
    super.key,
    this.username,
    this.fullName,
    this.titleStyle,
    this.subtitleStyle,
    this.image,
    this.isAsset = true,
    this.noAvatar = false,
    this.rightWidget,
    this.imgHeight,
    this.imgWidth,
    this.imgWidget,
  });

  @override
  Widget build(BuildContext context) {
    return CustomAppCard(
      radius: Dimensions.cardExtraRadius.r,
      backgroundColor: MyColor.getScreenBgColor(),
      margin: EdgeInsetsDirectional.symmetric(
        horizontal: Dimensions.space15,
        vertical: Dimensions.space10,
      ),
      padding: EdgeInsetsDirectional.symmetric(
        horizontal: Dimensions.space15,
        vertical: Dimensions.space15,
      ),
      width: double.infinity,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Expanded(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.start,
              children: [
                imgWidget ??
                    MyNetworkImageWidget(
                      imageUrl: image ?? "",
                      isProfile: false,
                      width: imgWidth ?? Dimensions.space48.w,
                      height: imgWidth ?? Dimensions.space48.w,
                    ),
                spaceSide(Dimensions.space8),
                Expanded(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Flexible(
                        child: Text(
                          "$fullName".toCapitalized(),
                          style: titleStyle ?? MyTextStyle.sectionTitle,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      const SizedBox(height: Dimensions.space3),
                      Text(
                        "$username".tr,
                        style: subtitleStyle ??
                            MyTextStyle.sectionBodyTextStyle.copyWith(
                              color: MyColor.getBodyTextColor(),
                            ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          rightWidget ?? const SizedBox.shrink(),
        ],
      ),
    );
  }
}
