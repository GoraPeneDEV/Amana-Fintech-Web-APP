import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_information_widget_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/screens/auth/kyc/controller/kyc_controller.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../../core/utils/util_exporter.dart';

class AlreadyVerifiedWidget extends StatefulWidget {
  final bool isPending;

  const AlreadyVerifiedWidget({super.key, this.isPending = false});

  @override
  State<AlreadyVerifiedWidget> createState() => _AlreadyVerifiedWidgetState();
}

class _AlreadyVerifiedWidgetState extends State<AlreadyVerifiedWidget> {
  @override
  Widget build(BuildContext context) {
    return GetBuilder<KycController>(
      builder: (controller) {
        return widget.isPending == false
            ? SingleChildScrollView(
                child: CustomAppCard(
                  width: double.infinity,
                  child: Column(
                    children: [
                      MyAssetImageWidget(
                        color: MyColor.getPrimaryColor(),
                        assetPath: MyIcons.privacyIcon,
                        isSvg: true,
                      ),
                      const SizedBox(height: Dimensions.space3),
                      Text(
                        MyStrings.kycAlreadyVerifiedMsg.tr,
                        style: MyTextStyle.bodyTextStyle1.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                      spaceDown(Dimensions.space24),
                      CustomElevatedBtn(
                        radius: Dimensions.largeRadius.r,
                        bgColor: MyColor.getPrimaryColor(),
                        text: MyStrings.home,
                        onTap: () {
                          Get.offAllNamed(RouteHelper.dashboardScreen);
                        },
                      ),
                    ],
                  ),
                ),
              )
            : widget.isPending == true && controller.pendingData.isNotEmpty
                ? RefreshIndicator(
                    color: MyColor.getPrimaryColor(),
                    onRefresh: () async {
                      controller.beforeInitLoadKycData();
                    },
                    child: CustomAppCard(
                      child: ListView.separated(
                        shrinkWrap: true,
                        itemCount: controller.pendingData.length,
                        itemBuilder: (context, index) {
                          var item = controller.pendingData[index];
                          String ext = item.type == "file" ? item.value?.split('.')[1] ?? '' : "";
                          String url = "${UrlContainer.domainUrl}/${controller.path}/${item.value.toString()}";
                          return CustomListTileInformationWidgetCard(
                            onPressed: () {
                              if (item.type == "file") {
                                controller.downloadAttachment(url, index, ext);
                              }
                            },
                            width: double.infinity,
                            title: "${item.name}",
                            subtitle: item.type == "file"
                                ? null
                                : AppConverter.removeQuotationAndSpecialCharacterFromString(
                                    "${item.value}",
                                  ),
                            customWidget: item.type == "file" && controller.isImageFile(extensions: [ext])
                                ? Padding(
                                    padding: const EdgeInsets.only(
                                      top: Dimensions.space10,
                                    ),
                                    child: MyNetworkImageWidget(imageUrl: url),
                                  )
                                : ext != ""
                                    ? MyUtils.getFileIcon(
                                        ".$ext",
                                        size: Dimensions.space60,
                                      )
                                    : SizedBox(),
                          );
                        },
                        separatorBuilder: (BuildContext context, int index) {
                          return spaceDown(Dimensions.space10);
                        },
                      ),
                    ),
                  )
                : NoDataWidget();
      },
    );
  }
}
