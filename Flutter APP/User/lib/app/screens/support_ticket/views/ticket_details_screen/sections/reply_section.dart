import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/support_ticket/controller/ticket_details_controller.dart';

import '../../../../../../core/utils/util_exporter.dart';

class ReplySection extends StatelessWidget {
  final VoidCallback onClickUploadButton;
  const ReplySection({super.key, required this.onClickUploadButton});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TicketDetailsController>(
      builder: (controller) => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          spaceDown(Dimensions.space20),
          RoundedTextField(
            labelText: MyStrings.message.tr,
            hintText: MyStrings.enterYourMessage.tr,
            controller: controller.replyController,
            maxLine: 5,
            textInputAction: TextInputAction.newline,
            keyboardType: TextInputType.multiline,
          ),
          spaceDown(Dimensions.space20),
          RoundedTextField(
            prefixIcon: CustomAppCard(
              showBorder: false,
              margin: EdgeInsetsDirectional.only(
                start: Dimensions.space10,
                end: Dimensions.space10,
              ),
              padding: EdgeInsetsDirectional.zero,
              width: 50,
              height: 40,
              radius: Dimensions.mediumRadius.r,
              backgroundColor: MyColor.getPrimaryColor(),
              child: Icon(
                Icons.camera_alt_outlined,
                color: MyColor.getWhiteColor(),
              ),
            ),
            onTap: onClickUploadButton,
            readOnly: true,
            showLabelText: false,
            labelText: MyStrings.chooseAFile.tr,
            hintText: MyStrings.chooseAFile.tr,
            textInputAction: TextInputAction.done,
            keyboardType: TextInputType.text,
          ),
          if (controller.attachmentList.isNotEmpty) ...[
            spaceDown(Dimensions.space15),
          ],
          controller.attachmentList.isNotEmpty
              ? SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: List.generate(
                          controller.attachmentList.length,
                          (index) => Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Stack(
                                children: [
                                  Container(
                                    margin: const EdgeInsets.all(
                                      Dimensions.space5,
                                    ),
                                    decoration: const BoxDecoration(),
                                    child: MyUtils.isImage(
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
                                        : MyUtils.isDoc(
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
                                        controller.removeAttachmentFromList(
                                          index,
                                        );
                                      },
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                )
              : const SizedBox(),
          spaceDown(Dimensions.space5),
          Text(
            "${MyStrings.supportedFileType.tr.toTitleCase()} ${MyStrings.ext}",
            style: MyTextStyle.caption1Style.copyWith(
              color: MyColor.getBodyTextColor(),
            ),
          ),
          spaceDown(Dimensions.space10),
          spaceDown(Dimensions.space10),
          CustomElevatedBtn(
            isLoading: controller.submitLoading,
            text: MyStrings.reply.tr,
            onTap: () {
              controller.submitReply();
            },
          ),
        ],
      ),
    );
  }
}
