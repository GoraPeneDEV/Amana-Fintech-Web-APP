import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/data/models/onboard/onboard_model.dart';

import '../../../../core/utils/util_exporter.dart';

class OnBoardController extends GetxController {
  List<OnBoardModel> appOnboardDataList = [
    OnBoardModel(
      title: MyStrings.onboardTitle1,
      subtitle: MyStrings.onboardSubtitle1,
      image: MyImages.onBoardImage1,
      isSvg: false,
    ),
    OnBoardModel(
      title: MyStrings.onboardTitle2,
      subtitle: MyStrings.onboardSubtitle2,
      image: MyImages.onBoardImage2,
      isSvg: false,
    ),
    OnBoardModel(
      title: MyStrings.onboardTitle3,
      subtitle: MyStrings.onboardSubtitle3,
      image: MyImages.onBoardImage3,
      isSvg: false,
    ),
  ];

  int currentIndex = 0;
  PageController? controller = PageController();

  void setCurrentIndex(int index) {
    currentIndex = index;
    update();
  }
}
