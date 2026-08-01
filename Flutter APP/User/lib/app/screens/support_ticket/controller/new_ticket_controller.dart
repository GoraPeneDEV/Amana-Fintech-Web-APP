import 'dart:io';

import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/support_ticket/new_ticket_store_model.dart';
import 'package:ovopay/core/data/repositories/support/support_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class NewTicketController extends GetxController {
  SupportRepo repo;
  NewTicketController({required this.repo});

  bool isLoading = false;

  final FocusNode subjectFocusNode = FocusNode();
  final FocusNode priorityFocusNode = FocusNode();
  final FocusNode messageFocusNode = FocusNode();

  TextEditingController messageController = TextEditingController();
  TextEditingController subjectController = TextEditingController();

  String noFileChosen = MyStrings.noFileChosen;
  String chooseFile = MyStrings.chooseFile;

  bool isRtl = false;
  final FileSelector _fileSelector = FileSelector();
  List<File> attachmentList = [];

  void pickFile() async {
    List<File> filesResult = await _fileSelector.selectMultipleFiles();
    if (filesResult.isEmpty) return;

    for (var i = 0; i < filesResult.length; i++) {
      attachmentList.add(File(filesResult[i].path));
    }
    update();
    return;
  }

  void pickImages() async {
    List<File> filesResult = await _fileSelector.selectMultipleImagesFromGallery();
    if (filesResult.isEmpty) return;

    for (var i = 0; i < filesResult.length; i++) {
      attachmentList.add(File(filesResult[i].path));
    }
    update();
    return;
  }

  void removeAttachmentFromList(int index) {
    if (attachmentList.length > index) {
      attachmentList.removeAt(index);
      update();
    }
  }

  void addNewAttachment() {
    if (attachmentList.length > 4) {
      CustomSnackBar.error(errorList: [MyStrings.somethingWentWrong]);
      return;
    }

    update();
  }

  void refreshAttachmentList() {
    attachmentList.clear();
    attachmentList = [];
    update();
  }

  List<String> priorityList = [
    MyStrings.low.tr,
    MyStrings.medium.tr,
    MyStrings.high.tr,
  ];
  String? selectedPriority = MyStrings.low.tr;

  int selectedIndex = 0;
  void setPriority(String? newValue) {
    selectedPriority = newValue;
    if (newValue != null) {
      selectedIndex = priorityList.indexOf(newValue);
    }
    update();
  }

  bool isImage(String path) {
    if (path.contains('.jpg')) {
      printX("its image");
      return true;
    }
    if (path.contains('.png')) {
      return true;
    }
    if (path.contains('.jpeg')) {
      return true;
    }
    return false;
  }

  bool isXlsx(String path) {
    if (path.contains('.xlsx')) {
      return true;
    }
    if (path.contains('.xls')) {
      return true;
    }
    if (path.contains('.xlx')) {
      return true;
    }
    return false;
  }

  bool isDoc(String path) {
    if (path.contains('.doc')) {
      return true;
    }
    if (path.contains('.docs')) {
      return true;
    }
    return false;
  }

  bool submitLoading = false;
  void submit() async {
    String subject = subjectController.text.toString();
    String priority = "${selectedIndex + 1}";
    String message = messageController.text.toString();

    if (subject.trim().isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.subjectRequired]);
      return;
    }

    if (message.trim().isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.messageRequired]);
      return;
    }

    submitLoading = true;
    update();

    TicketStoreModel model = TicketStoreModel(
      name: "",
      email: "",
      subject: subject,
      priority: priority,
      message: message,
      list: attachmentList,
    );

    ResponseModel response = await repo.storeTicket(model);
    AuthorizationResponseModel responseAuthorize = AuthorizationResponseModel.fromJson(response.responseJson);

    try {
      if (responseAuthorize.status == AppStatus.SUCCESS) {
        Get.back(result: "updated");
        CustomSnackBar.success(
          successList: responseAuthorize.message ?? [MyStrings.somethingWentWrong],
        );
        clearSelectedData();
      } else {
        CustomSnackBar.error(
          errorList: responseAuthorize.message ?? [MyStrings.somethingWentWrong],
        );
      }
    } catch (e) {
      printE(e);
    }

    submitLoading = false;
    update();
  }

  void clearSelectedData() {
    subjectController.text = '';
    messageController.text = '';
    refreshAttachmentList();
  }
}
