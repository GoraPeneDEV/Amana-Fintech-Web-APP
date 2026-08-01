import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class VirtualCardTransactionListTileCard extends StatelessWidget {
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final double width;
  final Color backgroundColor;
  final double radius;
  final VoidCallback? onPressed;
  final String? title;
  final String? subtitle;
  final String? balance;
  final String? date;
  final Widget? trailing;
  final bool? showBorder;
  final bool isPress;
  final String trxType;

  const VirtualCardTransactionListTileCard({
    super.key,
    this.width = double.infinity,
    this.backgroundColor = MyColor.transparentColor,
    this.radius = 0,
    this.onPressed,
    this.title,
    this.subtitle,
    this.trailing,
    this.balance,
    this.date,
    this.isPress = false,
    this.padding,
    this.margin,
    this.showBorder = true,
    this.trxType = "",
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
          padding: (padding ??
              EdgeInsetsDirectional.symmetric(
                vertical: Dimensions.space10.w,
              )),
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
            minVerticalPadding: Dimensions.space8.h,
            horizontalTitleGap: Dimensions.space8.w,
            leading: CustomAppCard(
              backgroundColor: (trxType == "+"
                      ? MyColor.success
                      : trxType == "-"
                          ? MyColor.error
                          : MyColor.getPrimaryColor())
                  .withValues(alpha: 0.1),
              borderColor: Colors.transparent,
              padding: EdgeInsets.all(Dimensions.space12.w),
              width: Dimensions.space48.w,
              height: Dimensions.space48.w,
              radius: Dimensions.largeRadius.r,
              child: FittedBox(
                child: Icon(
                  trxType == "+" ? Icons.add : Icons.remove,
                  size: Dimensions.space24.w,
                  color: trxType == "+"
                      ? MyColor.success
                      : trxType == "-"
                          ? MyColor.error
                          : MyColor.getPrimaryColor(),
                ),
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
                    child: Text(title ?? "", style: MyTextStyle.sectionTitle3),
                  ),
                  spaceSide(Dimensions.space10),
                  Flexible(
                    child: FittedBox(
                      fit: BoxFit.scaleDown,
                      child: Text(
                        textAlign: TextAlign.end,
                        balance ?? "",
                        style: MyTextStyle.sectionTitle3.copyWith(
                          color: trxType == "+"
                              ? MyColor.success
                              : trxType == "-"
                                  ? MyColor.error
                                  : MyColor.getHeaderTextColor(),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            subtitle: Row(
              children: [
                Expanded(
                  child: Text(
                    subtitle ?? "",
                    style: MyTextStyle.sectionSubTitle1.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                  ),
                ),
                spaceSide(Dimensions.space10),
                Expanded(
                  child: Text(
                    textAlign: TextAlign.end,
                    date ?? "",
                    style: MyTextStyle.caption2Style.copyWith(
                      fontSize: Dimensions.fontSmall.sp,
                      color: MyColor.getBodyTextColor(),
                    ),
                  ),
                ),
              ],
            ),
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
    );
  }
}
