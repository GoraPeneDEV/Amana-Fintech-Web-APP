import 'package:flutter/material.dart';

import 'package:flutter_spinkit/flutter_spinkit.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/screens/support_ticket/controller/ticket_details_controller.dart';
import 'package:ovopay/core/data/models/support_ticket/support_ticket_view_response_model.dart';

import '../../../../../../core/utils/util_exporter.dart';

class TicketListItem extends StatelessWidget {
  const TicketListItem({
    super.key,
    required this.index,
    required this.messages,
  });

  final SupportMessage messages;
  final int index;

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TicketDetailsController>(
      builder: (controller) => Container(
        padding: const EdgeInsets.all(Dimensions.space10),
        margin: const EdgeInsets.only(bottom: Dimensions.space15),
        decoration: BoxDecoration(
          color: messages.adminId == "1" ? MyColor.warning.withValues(alpha: 0.1) : MyColor.getWhiteColor(),
          borderRadius: BorderRadius.circular(Dimensions.mediumRadius.r),
          border: Border.all(
            color: messages.adminId == "1" ? MyColor.warning : MyColor.getBorderColor(),
            strokeAlign: 1,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.start,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (messages.admin == null)
                        Text(
                          '${messages.ticket?.name}',
                          style: MyTextStyle.sectionTitle3.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        )
                      else
                        Text(
                          '${messages.admin?.name}',
                          style: MyTextStyle.sectionTitle3.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                      spaceDown(Dimensions.space5),
                      Text(
                        messages.adminId == "1" ? MyStrings.admin.tr : MyStrings.you.tr,
                        style: MyTextStyle.caption2Style.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                    ],
                  ),
                ),
                spaceSide(Dimensions.space5),
                Text(
                  DateConverter.getTimeAgo(
                    messages.createdAt ?? '',
                  ),
                  style: MyTextStyle.caption2Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space5),
            Row(
              children: [
                Expanded(
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                      vertical: Dimensions.space5,
                    ),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(
                        Dimensions.defaultRadius,
                      ),
                    ),
                    child: Text(
                      messages.message ?? "",
                      style: MyTextStyle.caption2Style.copyWith(
                        color: MyColor.getBlackColor(),
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            if (messages.attachments?.isNotEmpty ?? false)
              Container(
                height: MediaQuery.of(context).size.width > 500 ? 100 : 100,
                padding: const EdgeInsets.symmetric(
                  horizontal: Dimensions.space10,
                  vertical: Dimensions.space5,
                ),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: messages.attachments != null
                        ? List.generate(
                            messages.attachments!.length,
                            (i) => controller.selectedIndex == (messages.attachments?[i].id ?? -1)
                                ? Container(
                                    width: MediaQuery.of(
                                              context,
                                            ).size.width >
                                            500
                                        ? 100
                                        : 100,
                                    height: MediaQuery.of(
                                              context,
                                            ).size.width >
                                            500
                                        ? 100
                                        : 100,
                                    margin: const EdgeInsets.only(
                                      right: 10,
                                    ),
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: Dimensions.space30,
                                      vertical: Dimensions.space10,
                                    ),
                                    decoration: BoxDecoration(
                                      color: MyColor.getScreenBgColor(),
                                      borderRadius: BorderRadius.circular(
                                        Dimensions.mediumRadius.r,
                                      ),
                                    ),
                                    child: SpinKitThreeBounce(
                                      size: 20.0,
                                      color: MyColor.getPrimaryColor(),
                                    ),
                                  )
                                : GestureDetector(
                                    onTap: () {
                                      String url = '${UrlContainer.supportImagePath}${messages.attachments?[i].attachment}';
                                      String ext = messages.attachments?[i].attachment?.split('.')[1] ?? 'pdf';
                                      if (MyUtils.isImage(
                                        messages.attachments?[i].attachment.toString() ?? "",
                                      )) {
                                        //Next Version
                                        // Get.toNamed(
                                        //   RouteHelper.previewImageScreen,
                                        //   arguments: "${UrlContainer.supportImagePath}${messages.attachments?[i].attachment}",
                                        // );
                                        controller.downloadAttachment(
                                          url,
                                          messages.attachments?[i].id ?? -1,
                                          ext,
                                        );
                                      } else {
                                        controller.downloadAttachment(
                                          url,
                                          messages.attachments?[i].id ?? -1,
                                          ext,
                                        );
                                      }
                                    },
                                    child: Container(
                                      width: MediaQuery.of(
                                                context,
                                              ).size.width >
                                              500
                                          ? 100
                                          : 100,
                                      height: MediaQuery.of(
                                                context,
                                              ).size.width >
                                              500
                                          ? 100
                                          : 100,
                                      margin: const EdgeInsets.only(
                                        right: 10,
                                      ),
                                      decoration: BoxDecoration(
                                        border: Border.all(
                                          color: MyColor.getBorderColor(),
                                        ),
                                        borderRadius: BorderRadius.circular(
                                          Dimensions.mediumRadius.r,
                                        ),
                                      ),
                                      child: MyUtils.isImage(
                                        messages.attachments?[i].attachment.toString() ?? "",
                                      )
                                          ? MyNetworkImageWidget(
                                              imageUrl: "${UrlContainer.supportImagePath}${messages.attachments?[i].attachment}",
                                              width: MediaQuery.of(
                                                        context,
                                                      ).size.width >
                                                      500
                                                  ? 100
                                                  : 100,
                                              height: MediaQuery.of(
                                                        context,
                                                      ).size.width >
                                                      500
                                                  ? 100
                                                  : 100,
                                            )
                                          : MyUtils.isDoc(
                                              messages.attachments?[i].attachment.toString() ?? "",
                                            )
                                              ? Center(
                                                  child: MyUtils.getFileIcon(
                                                    messages.attachments?[i].attachment.toString() ?? "",
                                                    size: Dimensions.space40,
                                                  ),
                                                )
                                              : Center(
                                                  child: MyUtils.getFileIcon(
                                                    messages.attachments?[i].attachment.toString() ?? "",
                                                    size: Dimensions.space40,
                                                  ),
                                                ),
                                    ),
                                  ),
                          )
                        : const [SizedBox.shrink()],
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
