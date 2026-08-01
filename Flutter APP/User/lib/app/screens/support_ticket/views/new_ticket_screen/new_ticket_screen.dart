import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/drop_down/my_drop_down_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/support_ticket/controller/new_ticket_controller.dart';
import 'package:ovopay/app/screens/support_ticket/views/new_ticket_screen/section/attachment_section.dart';
import 'package:ovopay/core/data/repositories/support/support_repo.dart';

import '../../../../../core/utils/util_exporter.dart';

class NewTicketScreen extends StatefulWidget {
  const NewTicketScreen({super.key});

  @override
  State<NewTicketScreen> createState() => _NewTicketScreenState();
}

class _NewTicketScreenState extends State<NewTicketScreen> {
  @override
  void initState() {
    Get.put(SupportRepo());
    Get.put(NewTicketController(repo: Get.find()));

    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {});
  }

  @override
  void dispose() {
    super.dispose();
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

              Get.find<NewTicketController>().pickImages();
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

              Get.find<NewTicketController>().pickFile();
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
    return GetBuilder<NewTicketController>(
      builder: (controller) => MyCustomScaffold(
        pageTitle: MyStrings.createSupportTicket,
        body: controller.isLoading
            ? const Center(child: CircularProgressIndicator())
            : SingleChildScrollView(
                child: CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: Dimensions.textToTextSpace),
                      RoundedTextField(
                        labelText: MyStrings.subject.tr,
                        hintText: MyStrings.enterYourSubject.tr,
                        controller: controller.subjectController,
                        focusNode: controller.subjectFocusNode,
                        nextFocus: controller.messageFocusNode,
                        textInputAction: TextInputAction.next,
                        keyboardType: TextInputType.text,
                      ),
                      spaceDown(Dimensions.space20),
                      AppDropdownWidget(
                        items: controller.priorityList,
                        onItemSelected: (String value) {
                          controller.setPriority(value);
                        },
                        selectedItem: controller.selectedPriority ?? "",
                        child: RoundedTextField(
                          readOnly: true,
                          labelText: MyStrings.priority.tr,
                          hintText: MyStrings.priority.tr,
                          controller: TextEditingController(
                            text: controller.selectedPriority ?? "",
                          ),
                          textInputAction: TextInputAction.next,
                          keyboardType: TextInputType.phone,
                          onTap: () {},
                          suffixIcon: Icon(
                            Icons.keyboard_arrow_down_rounded,
                            color: MyColor.getDarkColor(),
                          ),
                        ),
                      ),
                      spaceDown(Dimensions.space20),
                      RoundedTextField(
                        labelText: MyStrings.message.tr,
                        hintText: MyStrings.enterYourMessage.tr,
                        controller: controller.messageController,
                        focusNode: controller.messageFocusNode,
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
                        onTap: () {
                          _showCupertinoPickerOptions(context);
                        },
                        readOnly: true,
                        showLabelText: false,
                        labelText: MyStrings.chooseAFile.tr,
                        hintText: MyStrings.chooseAFile.tr,
                        textInputAction: TextInputAction.done,
                        keyboardType: TextInputType.text,
                      ),
                      spaceDown(Dimensions.space2),
                      spaceDown(Dimensions.space10),
                      const AttachmentSection(),
                      Text(
                        "${MyStrings.supportedFileType.tr.toTitleCase()} ${MyStrings.ext}",
                        style: MyTextStyle.caption1Style.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                      const SizedBox(height: 30),
                      Center(
                        child: CustomElevatedBtn(
                          isLoading: controller.submitLoading,
                          radius: Dimensions.space8,
                          bgColor: MyColor.getPrimaryColor(),
                          text: MyStrings.submit.tr,
                          onTap: () {
                            controller.submit();
                          },
                        ),
                      ),
                    ],
                  ),
                ),
              ),
      ),
    );
  }
}
