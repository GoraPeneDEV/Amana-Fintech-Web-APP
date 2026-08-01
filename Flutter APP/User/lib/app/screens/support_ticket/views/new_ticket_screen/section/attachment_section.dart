import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/screens/support_ticket/controller/new_ticket_controller.dart';

import '../../../../../../core/utils/util_exporter.dart';

class AttachmentSection extends StatelessWidget {
  const AttachmentSection({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<NewTicketController>(
      builder: (controller) {
        return controller.attachmentList.isNotEmpty
            ? SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: List.generate(
                    controller.attachmentList.length,
                    (index) => Row(
                      children: [
                        Stack(
                          children: [
                            Container(
                              margin: const EdgeInsets.all(Dimensions.space5),
                              decoration: const BoxDecoration(),
                              child: controller.isImage(
                                controller.attachmentList[index].path,
                              )
                                  ? ClipRRect(
                                      borderRadius: BorderRadius.circular(
                                        Dimensions.mediumRadius.r,
                                      ),
                                      child: Image.file(
                                        controller.attachmentList[index],
                                        width: context.width / 5,
                                        height: context.width / 5,
                                        fit: BoxFit.cover,
                                      ),
                                    )
                                  : controller.isDoc(
                                      controller.attachmentList[index].path,
                                    )
                                      ? Container(
                                          width: context.width / 5,
                                          height: context.width / 5,
                                          decoration: BoxDecoration(
                                            color: MyColor.getWhiteColor(),
                                            borderRadius: BorderRadius.circular(
                                              Dimensions.mediumRadius.r,
                                            ),
                                            border: Border.all(
                                              color: MyColor.getBorderColor(),
                                              width: 1,
                                            ),
                                          ),
                                          child: Center(
                                            child: MyUtils.getFileIcon(
                                              controller.attachmentList[index].path,
                                              size: Dimensions.space40,
                                            ),
                                          ),
                                        )
                                      : Container(
                                          width: context.width / 5,
                                          height: context.width / 5,
                                          decoration: BoxDecoration(
                                            color: MyColor.getWhiteColor(),
                                            borderRadius: BorderRadius.circular(
                                              Dimensions.mediumRadius.r,
                                            ),
                                            border: Border.all(
                                              color: MyColor.getBorderColor(),
                                              width: 1,
                                            ),
                                          ),
                                          child: Center(
                                            child: MyUtils.getFileIcon(
                                              controller.attachmentList[index].path,
                                              size: Dimensions.space40,
                                            ),
                                          ),
                                        ),
                            ),
                            PositionedDirectional(
                              bottom: 0,
                              end: 0,
                              child: IconButton(
                                icon: Icon(
                                  Icons.close,
                                  color: MyColor.redLightColor,
                                  size: Dimensions.space25,
                                ),
                                onPressed: () {
                                  controller.removeAttachmentFromList(index);
                                },
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              )
            : const SizedBox();
      },
    );
  }
}
