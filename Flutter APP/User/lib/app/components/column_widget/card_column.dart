import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';

import '../../../core/utils/util_exporter.dart';

class CardColumn extends StatelessWidget {
  final String header;
  final String? body;
  final CrossAxisAlignment? crossAxisAlignment;
  final bool isDate;
  final Color? textColor;
  final String? subBody;
  final TextStyle? headerTextStyle;
  final TextStyle? bodyTextStyle;
  final TextStyle? subBodyTextStyle;
  final bool? isOnlyHeader;
  final bool isBodyEllipsis;
  final bool? isCopyable;
  final int bodyMaxLine;
  final double? space;
  final int maxLine;

  const CardColumn({
    super.key,
    this.maxLine = 1,
    this.bodyMaxLine = 1,
    this.crossAxisAlignment,
    required this.header,
    this.isDate = false,
    this.textColor,
    this.headerTextStyle,
    this.bodyTextStyle,
    this.body,
    this.subBody,
    this.isOnlyHeader = false,
    this.isBodyEllipsis = true,
    this.space = 5,
    this.isCopyable = false,
    this.subBodyTextStyle,
  });

  @override
  Widget build(BuildContext context) {
    return isOnlyHeader!
        ? Column(
            crossAxisAlignment: crossAxisAlignment ?? CrossAxisAlignment.start,
            children: [
              Text(
                header.tr,
                style: headerTextStyle ?? MyTextStyle.caption1Style,
                overflow: TextOverflow.ellipsis,
              ),
              SizedBox(height: space),
            ],
          )
        : Column(
            crossAxisAlignment: crossAxisAlignment ?? CrossAxisAlignment.start,
            children: [
              Text(
                header.tr,
                style: headerTextStyle ?? MyTextStyle.caption1Style,
                overflow: TextOverflow.ellipsis,
                maxLines: maxLine,
              ),
              if (body != null) ...[
                SizedBox(height: space),
                if (isCopyable == true) ...[
                  FittedBox(
                    fit: BoxFit.scaleDown,
                    child: GestureDetector(
                      onTap: () {
                        Clipboard.setData(
                          ClipboardData(text: body.toString()),
                        ).then((_) {
                          CustomSnackBar.showToast(
                            message: MyStrings.copiedToClipBoard.tr,
                          );
                        });
                      },
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        mainAxisAlignment: crossAxisAlignment == CrossAxisAlignment.end
                            ? MainAxisAlignment.end
                            : crossAxisAlignment == CrossAxisAlignment.center
                                ? MainAxisAlignment.center
                                : MainAxisAlignment.start,
                        children: [
                          Text(
                            (body ?? "").tr,
                            maxLines: bodyMaxLine,
                            style: isDate ? MyTextStyle.sectionTitle : bodyTextStyle ?? MyTextStyle.sectionTitle,
                            overflow: TextOverflow.ellipsis,
                          ),
                          Padding(
                            padding: const EdgeInsetsDirectional.only(
                              start: Dimensions.space8,
                            ),
                            child: MyAssetImageWidget(
                              color: MyColor.getPrimaryColor(),
                              isSvg: true,
                              assetPath: MyIcons.copyIcon,
                              width: Dimensions.space20.w,
                              height: Dimensions.space20.w,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ] else ...[
                  if (isBodyEllipsis) ...[
                    Text(
                      (body ?? "").tr,
                      // maxLines: bodyMaxLine,
                      style: isDate ? MyTextStyle.sectionTitle : bodyTextStyle ?? MyTextStyle.sectionTitle,
                      overflow: isBodyEllipsis ? TextOverflow.ellipsis : TextOverflow.visible,
                    )
                  ] else ...[
                    FittedBox(
                      fit: BoxFit.scaleDown,
                      child: Text(
                        (body ?? "").tr,
                        // maxLines: bodyMaxLine,
                        style: isDate ? MyTextStyle.sectionTitle : bodyTextStyle ?? MyTextStyle.sectionTitle,
                        overflow: isBodyEllipsis ? TextOverflow.ellipsis : TextOverflow.visible,
                      ),
                    ),
                  ]
                ],
              ],
              const SizedBox(height: Dimensions.space5),
              subBody != null
                  ? Text(
                      subBody!.tr,
                      maxLines: bodyMaxLine,
                      style: isDate ? MyTextStyle.sectionSubTitle1 : subBodyTextStyle ?? MyTextStyle.sectionSubTitle1,
                    )
                  : const SizedBox.shrink(),
            ],
          );
  }
}
