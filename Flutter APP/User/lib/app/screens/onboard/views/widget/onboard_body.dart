import 'package:flutter/material.dart';
import 'package:get/get.dart';

import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/core/data/models/onboard/onboard_model.dart';

import '../../../../../core/utils/util_exporter.dart';

class OnBoardBody extends StatelessWidget {
  final OnBoardModel data;
  const OnBoardBody({super.key, required this.data});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      clipBehavior: Clip.none,
      child: Column(
        children: [
          Center(
            child: MyAssetImageWidget(
              width: 300,
              height: 300,
              boxFit: BoxFit.contain,
              assetPath: data.image,
              isSvg: data.isSvg,
            ),
          ),
          const SizedBox(height: Dimensions.space40),
          Text(
            data.title.tr,
            style: MyTextStyle.headerH1.copyWith(color: MyColor.getDarkColor()),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: Dimensions.space25 - 1),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 2),
            child: Text(
              data.subtitle.tr,
              style: MyTextStyle.bodyTextStyle1.copyWith(
                color: MyColor.getDarkColor(),
              ),
              textAlign: TextAlign.center,
            ),
          ),
        ],
      ),
    );
  }
}
