import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/shimmer/ticket_details_shimmer.dart';
import 'package:ovopay/app/screens/support_ticket/controller/ticket_details_controller.dart';
import 'package:ovopay/app/screens/support_ticket/views/ticket_details_screen/sections/message_list_section.dart';
import 'package:ovopay/app/screens/support_ticket/views/ticket_details_screen/sections/reply_section.dart';
import 'package:ovopay/app/screens/support_ticket/views/ticket_details_screen/widget/ticket_status_widget.dart';

import 'package:ovopay/core/data/repositories/support/support_repo.dart';

import '../../../../../core/utils/util_exporter.dart';

class TicketDetailsScreen extends StatefulWidget {
  const TicketDetailsScreen({super.key});

  @override
  State<TicketDetailsScreen> createState() => _TicketDetailsScreenState();
}

class _TicketDetailsScreenState extends State<TicketDetailsScreen> {
  String title = "";
  @override
  void initState() {
    String ticketId = Get.arguments[0];
    title = Get.arguments[1];

    Get.put(SupportRepo());
    var controller = Get.put(
      TicketDetailsController(repo: Get.find(), ticketId: ticketId),
    );

    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadSupportTicketData();
    });
  }

  void _showCupertinoPickerOptions(BuildContext context) {
    showCupertinoModalPopup(
      context: context,
      builder: (BuildContext context) => CupertinoActionSheet(
        title: Text(
          MyStrings.selectFiles.tr,
          style: MyTextStyle.sectionTitle.copyWith(
            color: MyColor.getHeaderTextColor(),
          ),
        ),
        message: Text(MyStrings.chooseAnOptionForSelectImageOrFiles.tr),
        actions: [
          CupertinoActionSheetAction(
            child: Text(
              MyStrings.pickImagesFormGallery.tr,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
            ),
            onPressed: () async {
              Navigator.of(context).pop();

              Get.find<TicketDetailsController>().pickImages();
            },
          ),
          CupertinoActionSheetAction(
            child: Text(
              MyStrings.pickMultipleFiles.tr,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
            ),
            onPressed: () async {
              Navigator.of(context).pop();

              Get.find<TicketDetailsController>().pickFile();
            },
          ),
        ],
        cancelButton: CupertinoActionSheetAction(
          child: Text(
            MyStrings.cancel.tr,
            style: MyTextStyle.sectionTitle3.copyWith(
              color: MyColor.getBodyTextColor(),
            ),
          ),
          onPressed: () {
            Navigator.of(context).pop();
          },
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TicketDetailsController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.replyTicket,
          actionButton: [
            if (!controller.isLoading && controller.model.data?.myTickets?.status != '3') ...[
              CustomAppCard(
                borderColor: MyColor.redLightColor,
                onPressed: () {
                  AppDialogs.confirmDialogForAll(
                    subTitle: MyStrings.areYouSureWantToCloseThisTicket.tr,
                    context,
                    isConfirmLoading: controller.closeLoading,
                    onConfirmTap: () {
                      controller.closeTicket(
                        controller.model.data?.myTickets?.id.toString() ?? '-1',
                      );
                    },
                  );
                },
                width: Dimensions.space35.w,
                height: Dimensions.space35.h,
                padding: EdgeInsetsDirectional.all(Dimensions.space8.w),
                radius: Dimensions.largeRadius.r,
                child: FittedBox(
                  fit: BoxFit.scaleDown,
                  child: Icon(
                    Icons.close_rounded,
                    size: Dimensions.space35.h,
                    color: MyColor.redLightColor,
                  ),
                ),
              ),
              spaceSide(Dimensions.space16.w),
            ],
          ],
          body: controller.isLoading
              ? TicketDetailsShimmer()
              : NestedScrollView(
                  headerSliverBuilder: (
                    BuildContext context,
                    bool innerBoxIsScrolled,
                  ) {
                    return [
                      SliverToBoxAdapter(
                        child: Column(
                          children: [
                            CustomAppCard(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  TicketStatusWidget(controller: controller),
                                  ReplySection(
                                    onClickUploadButton: () {
                                      _showCupertinoPickerOptions(context);
                                    },
                                  ),
                                ],
                              ),
                            ),
                            spaceDown(Dimensions.space20),
                          ],
                        ),
                      ),
                    ];
                  },
                  body: CustomAppCard(child: MessageListSection()),
                ),
        );
      },
    );
  }
}
