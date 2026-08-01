import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/my_qr_code_screen/controller/my_qr_code_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../core/utils/util_exporter.dart';

class MyQrCodeScreen extends StatefulWidget {
  const MyQrCodeScreen({super.key});

  @override
  State<MyQrCodeScreen> createState() => _MyQrCodeScreenState();
}

class _MyQrCodeScreenState extends State<MyQrCodeScreen> {
  @override
  void initState() {
    super.initState();
    var controller = Get.put(MyQrCodeController());
    WidgetsBinding.instance.addPostFrameCallback((v) {
      controller.getMyQrCodeData();
    });
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.qrCode,
      body: GetBuilder<MyQrCodeController>(
        builder: (controller) {
          return SingleChildScrollView(
            child: CustomAppCard(
              child: Skeletonizer(
                enabled: controller.isLoading,
                child: Column(
                  children: [
                    HeaderText(
                      text: SharedPreferenceService.getUserFullName(),
                      textAlign: TextAlign.center,
                      textStyle: MyTextStyle.headerH1.copyWith(
                        color: MyColor.getHeaderTextColor(),
                      ),
                    ),
                    spaceDown(Dimensions.space8),
                    HeaderText(
                      text: SharedPreferenceService.getUserPhoneNumber(),
                      textAlign: TextAlign.center,
                      textStyle: MyTextStyle.headerH3.copyWith(
                        fontWeight: FontWeight.w400,
                        color: MyColor.getBodyTextColor(),
                      ),
                    ),
                    spaceDown(Dimensions.space30),
                    Stack(
                      children: [
                        MyAssetImageWidget(
                          isSvg: true,
                          assetPath: MyIcons.qrCodeBgImage,
                          color: MyColor.getPrimaryColor(),
                          width: (context.width / 1.8).w,
                          height: (context.width / 1.8).w,
                          boxFit: BoxFit.contain,
                        ),
                        Positioned.fill(
                          child: Padding(
                            padding: const EdgeInsets.all(Dimensions.space30),
                            child: FittedBox(
                              child: Column(
                                children: [
                                  if (controller.qrCodeLink == "") ...[
                                    Center(
                                      child: Container(
                                        width: 220.w,
                                        height: 220.h,
                                        color: MyColor.black,
                                      ),
                                    ),
                                  ] else ...[
                                    Center(
                                      child: MyNetworkImageWidget(
                                        imageUrl: controller.qrCodeLink,
                                        width: 220.w,
                                        height: 220.h,
                                        boxFit: BoxFit.contain,
                                      ),
                                    ),
                                  ],
                                ],
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    spaceDown(Dimensions.space24),
                    HeaderText(
                      text: MyStrings.shareQrCode.tr,
                      textAlign: TextAlign.center,
                      textStyle: MyTextStyle.headerH3.copyWith(
                        fontWeight: FontWeight.w400,
                        color: MyColor.getBodyTextColor(),
                      ),
                    ),
                    spaceDown(Dimensions.space30),
                    CustomElevatedBtn(
                      radius: Dimensions.largeRadius.r,
                      isLoading: controller.isDownloadLoading,
                      icon: Padding(
                        padding: EdgeInsets.all(Dimensions.space10.w),
                        child: MyAssetImageWidget(
                          color: MyColor.getPrimaryColor(),
                          assetPath: MyIcons.downloadNewIcon,
                          width: Dimensions.space24.w,
                          height: Dimensions.space24.w,
                          isSvg: true,
                        ),
                      ),
                      bgColor: MyColor.getPrimaryColor(),
                      text: MyStrings.downloadQRCode.tr,
                      onTap: () {
                        controller.downloadAttachment(
                          controller.qrCodeLink,
                          "jpeg",
                        );
                      },
                    ),
                    spaceDown(Dimensions.space10),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}
