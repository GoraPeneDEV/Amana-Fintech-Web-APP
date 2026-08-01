import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../../core/utils/util_exporter.dart';

class EnableQRCodeWidget extends StatelessWidget {
  final String qrImage;
  final String secret;
  const EnableQRCodeWidget({
    super.key,
    required this.qrImage,
    required this.secret,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: [
        if (qrImage == "") ...[
          Center(
            child: Container(width: 220.w, height: 220.h, color: MyColor.black),
          ),
        ] else ...[
          Center(
            child: MyNetworkImageWidget(
              imageUrl: qrImage,
              width: 220.w,
              height: 220.h,
              boxFit: BoxFit.contain,
            ),
          ),
        ],

        //COPY
        const SizedBox(height: Dimensions.space20),
        Text(
          MyStrings.useQRCODETips3.tr,
          textAlign: TextAlign.center,
          style: MyTextStyle.sectionSubTitle1.copyWith(
            color: MyColor.getBodyTextColor(),
          ),
        ),
        const SizedBox(height: Dimensions.space20),
        Padding(
          padding: const EdgeInsets.symmetric(vertical: Dimensions.space10),
          child: GestureDetector(
            onTap: () {
              Clipboard.setData(ClipboardData(text: secret)).then((_) {
                CustomSnackBar.showToast(
                  message: MyStrings.copiedToClipBoard.tr,
                );
              });
            },
            child: CustomAppCard(
              radius: Dimensions.largeRadius.r,
              backgroundColor: MyColor.getScreenBgColor(),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  Expanded(
                    child: Text(
                      secret == "" ? "ABCD1231334654656" : secret,
                      style: MyTextStyle.sectionTitle.copyWith(
                        color: MyColor.getBodyTextColor(),
                      ),
                    ),
                  ),
                  Container(
                    margin: EdgeInsets.symmetric(horizontal: Dimensions.space3),
                    width: 1.w,
                    height: Dimensions.space25.h,
                    decoration: BoxDecoration(
                      color: MyColor.getBodyTextColor(),
                      borderRadius: BorderRadius.circular(
                        Dimensions.cardRadius,
                      ),
                    ),
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
        ),
        const SizedBox(height: Dimensions.space12),

        Center(
          child: Text.rich(
            TextSpan(
              children: [
                TextSpan(
                  text: MyStrings.useQRCODETips2.tr,
                  style: MyTextStyle.sectionSubTitle1.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                ),
                TextSpan(
                  text: ' ${MyStrings.download.tr}',
                  style: MyTextStyle.sectionSubTitle1.copyWith(
                    color: MyColor.getPrimaryColor(),
                    fontWeight: FontWeight.w600,
                  ),
                  recognizer: TapGestureRecognizer()
                    ..onTap = () async {
                      final Uri url = Uri.parse(
                        "https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en",
                      );

                      if (!await launchUrl(
                        url,
                        mode: LaunchMode.externalApplication,
                      )) {
                        throw Exception('Could not launch $url');
                      }
                    },
                  // style: boldExtraLarge.copyWith(color: MyColor.getRedColor()),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
