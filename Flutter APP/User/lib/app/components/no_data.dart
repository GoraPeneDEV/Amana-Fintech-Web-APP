import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class NoDataWidget extends StatelessWidget {
  final double margin;
  final String? text;
  final Widget? customWidget;
  const NoDataWidget({
    super.key,
    this.margin = 4,
    this.text,
    this.customWidget,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      alignment: Alignment.center,
      margin: EdgeInsets.symmetric(
        vertical: MediaQuery.of(context).size.height / margin,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          MyAssetImageWidget(
            assetPath: MyIcons.dataIcon,
            isSvg: true,
            height: 120,
            width: 120,
            color: MyColor.getPrimaryColor(),
          ),
          const SizedBox(height: Dimensions.space3),
          Text(
            text?.tr ?? MyStrings.noDataToShow.tr,
            textAlign: TextAlign.center,
            style: MyTextStyle.bodyTextStyle1.copyWith(
              color: MyColor.getBodyTextColor(),
            ),
          ),
          customWidget ?? const SizedBox(),
        ],
      ),
    );
  }
}
