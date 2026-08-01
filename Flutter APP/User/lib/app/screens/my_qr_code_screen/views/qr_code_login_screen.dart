import 'package:flutter/material.dart';
import 'package:flutter_spinkit/flutter_spinkit.dart';
import 'package:get/get.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/loading_border/loading_border.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/components/text/small_text.dart';
import 'package:ovopay/app/screens/my_qr_code_screen/controller/my_qr_code_controller.dart';

import '../../../../core/utils/util_exporter.dart';

class QrCodeLoginScreen extends StatefulWidget {
  const QrCodeLoginScreen({super.key, this.scanSubTitle});
  final String? scanSubTitle;

  @override
  State<QrCodeLoginScreen> createState() => _QrCodeLoginScreenState();
}

class _QrCodeLoginScreenState extends State<QrCodeLoginScreen> {
  final MobileScannerController codeController = MobileScannerController();
  Barcode? _barcode;
  bool isLoadingPage = false;
  @override
  void initState() {
    super.initState();
    // Initialize MyQrCodeController only once
    if (Get.isRegistered<MyQrCodeController>() == false) {
      Get.put(MyQrCodeController());
    }
  }

  void _handleBarcode(BarcodeCapture barcodes) {
    if (mounted) {
      try {
        setState(() {
          isLoadingPage = true;
        });

        _barcode = barcodes.barcodes.firstOrNull;
        printE(_barcode?.rawValue);
        printW('Barcode found! $_barcode');

        if (_barcode?.rawValue != null) {
          // Stop scanning after a successful scan
          codeController.stop();

          WidgetsBinding.instance.addPostFrameCallback((_) async {
            // Perform async actions here
            if (_barcode?.rawValue?.toString() != "null" || _barcode?.rawValue?.toString() != "") {
              CustomBottomSheetPlus(
                child: SafeArea(
                  child: buildQrCodeLoginConfirmDialog(
                    code: _barcode?.rawValue?.toString() ?? "",
                  ),
                ),
              ).show(context);
            }
            // Update loading state
            if (mounted) {
              setState(() {
                isLoadingPage = false;
              });
            }
          });
        }
      } catch (e) {
        printE(e.toString());
        if (mounted) {
          setState(() {
            isLoadingPage = false;
          });
        }
      }
    }
  }

  @override
  void dispose() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      // Stop the scanner when the widget is disposed
      codeController.stop();
      // Optionally, you can also dispose of the controller if needed
      codeController.dispose();
    });

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.qrCodeLogin,
      padding: EdgeInsets.zero,
      body: GetBuilder<MyQrCodeController>(
        builder: (controller) {
          return MobileScanner(
            controller: codeController,
            onDetect: _handleBarcode,
            overlayBuilder: (context, child) {
              return Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  spaceDown(Dimensions.space10),
                  Stack(
                    children: [
                      MyAssetImageWidget(
                        isSvg: true,
                        assetPath: MyIcons.qrCodeBgImage,
                        color: MyColor.getPrimaryColor(),
                        width: (context.width / 1.5).w,
                        height: (context.width / 1.5).w,
                        boxFit: BoxFit.contain,
                      ),
                      if (isLoadingPage)
                        Positioned.fill(
                          child: SpinKitFadingCube(
                            color: MyColor.getPrimaryColor(),
                            size: Dimensions.space50,
                          ),
                        ),
                    ],
                  ),
                  CustomAppCard(
                    margin: EdgeInsetsDirectional.symmetric(
                      horizontal: Dimensions.space16,
                      vertical: Dimensions.space50,
                    ),
                    radius: 0,
                    child: Row(
                      children: [
                        MyAssetImageWidget(
                          isSvg: true,
                          assetPath: MyIcons.scanQrCodeIconIcon,
                          color: MyColor.getPrimaryColor(),
                          width: Dimensions.space24.w,
                          height: Dimensions.space24.h,
                          boxFit: BoxFit.contain,
                        ),
                        spaceSide(Dimensions.space10),
                        Expanded(
                          child: HeaderTextSmaller(
                            text: widget.scanSubTitle ?? MyStrings.qrCodeLoginSubTitle,
                            textStyle: MyTextStyle.sectionTitle3.copyWith(
                              color: MyColor.getPrimaryColor(),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              );
            },
          );
        },
      ),
    );
  }

  Widget buildQrCodeLoginConfirmDialog({required String code}) {
    return PopScope(
      canPop: true,
      onPopInvokedWithResult: (didPop, result) {
        codeController.start();
      },
      child: GetBuilder<MyQrCodeController>(
        builder: (controller) {
          return ConstrainedBox(
            constraints: BoxConstraints(
              minWidth: MediaQuery.of(context).size.width,
              maxHeight: ScreenUtil().screenHeight / 1.2,
            ),
            child: Column(
              children: [
                BottomSheetHeaderRow(header: MyStrings.confirmQrCodeLogin),
                spaceDown(Dimensions.space10),
                Container(
                  height: 1,
                  width: double.infinity,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                spaceDown(Dimensions.space20),
                Column(
                  children: [
                    LoadingBorderIndicator(
                      animate: !controller.isQrCodeLoginLoading,
                      borderRadius: Dimensions.badgeRadius,
                      strokeWidth: Dimensions.space3,
                      color: MyColor.getPrimaryColor(),
                      bgColor: MyColor.getWhiteColor(),
                      child: MyAssetImageWidget(
                        isSvg: true,
                        assetPath: MyIcons.walletQrCodeIcon,
                        width: Dimensions.space50.w,
                        height: Dimensions.space50.w,
                        color: MyColor.getPrimaryColor(),
                      ),
                    ),
                  ],
                ),
                spaceDown(Dimensions.space20),
                CustomAppCard(
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      Icon(
                        Icons.info_outline_rounded,
                        color: MyColor.getBodyTextColor(),
                      ),
                      spaceSide(Dimensions.space10),
                      Expanded(
                        child: SmallText(
                          text: MyStrings.confirmQrCodeLoginSubText,
                          textStyle: MyTextStyle.caption1Style.copyWith(
                            color: MyColor.getBodyTextColor(),
                          ),
                          maxLine: 100,
                        ),
                      ),
                    ],
                  ),
                ),
                spaceDown(Dimensions.space15),
                CustomElevatedBtn(
                  isLoading: controller.isQrCodeLoginLoading,
                  radius: Dimensions.largeRadius.r,
                  bgColor: MyColor.getPrimaryColor(),
                  text: MyStrings.confirm,
                  icon: Icon(
                    Icons.account_circle_outlined,
                    color: MyColor.getWhiteColor(),
                  ),
                  onTap: () {
                    controller.qrCodeLogin(inputText: code).then((value) {
                      if (value) {
                        Get.back();
                        Get.back();
                      }
                    });
                  },
                ),
                spaceDown(Dimensions.space10),
                CustomElevatedBtn(
                  radius: Dimensions.largeRadius.r,
                  borderColor: MyColor.getPrimaryColor(),
                  bgColor: MyColor.getWhiteColor(),
                  textColor: MyColor.getPrimaryColor(),
                  text: MyStrings.cancel,
                  icon: Icon(Icons.close, color: MyColor.getPrimaryColor()),
                  onTap: () {
                    Get.back();
                  },
                ),
                spaceDown(Dimensions.space20),
              ],
            ),
          );
        },
      ),
    );
  }
}
