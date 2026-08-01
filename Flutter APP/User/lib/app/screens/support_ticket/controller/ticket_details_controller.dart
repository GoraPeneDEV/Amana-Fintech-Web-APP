import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/support_ticket/support_ticket_view_response_model.dart';
import 'package:ovopay/core/data/repositories/support/support_repo.dart';

import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';

class TicketDetailsController extends GetxController {
  SupportRepo repo;
  final String ticketId;
  String username = '';
  bool isRtl = false;

  TicketDetailsController({required this.repo, required this.ticketId});
  String moduleName = 'Support Ticket';

  Future loadSupportTicketData() async {
    isLoading = true;
    update();
    String languageCode = SharedPreferenceService.getString(
      SharedPreferenceService.languageCode,
      defaultValue: 'en',
    );
    if (languageCode == 'ar') {
      isRtl = true;
    }
    await loadTicketDetailsData();
    isLoading = false;
    update();
  }

  bool isLoading = false;

  final TextEditingController replyController = TextEditingController();

  MyTickets? receivedTicketModel;

  final FileSelector _fileSelector = FileSelector();
  List<File> attachmentList = [];

  String noFileChosen = MyStrings.noFileChosen;
  String chooseFile = MyStrings.chooseFile;

  String ticketImagePath = "";

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

  SupportTicketViewResponseModel model = SupportTicketViewResponseModel();
  List<SupportMessage> messageList = [];
  String ticket = '';
  String subject = '';
  String status = '-1';
  String ticketName = '';

  Future<void> loadTicketDetailsData({bool shouldLoad = true}) async {
    isLoading = shouldLoad;
    update();
    ResponseModel response = await repo.getSingleTicket(ticketId);

    if (response.statusCode == 200) {
      model = SupportTicketViewResponseModel.fromJson(response.responseJson);
      if (model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
        ticket = model.data?.myTickets?.ticket ?? '';
        subject = model.data?.myTickets?.subject ?? '';
        status = model.data?.myTickets?.status ?? '';
        ticketName = model.data?.myTickets?.name ?? '';
        receivedTicketModel = model.data?.myTickets;
        List<SupportMessage> tempTicketList = model.data?.myMessages ?? [];
        if (tempTicketList.isNotEmpty) {
          messageList.clear();
          messageList.addAll(tempTicketList);
        }
      } else {
        CustomSnackBar.error(
          errorList: model.message ?? [MyStrings.somethingWentWrong],
        );
      }
    } else {
      CustomSnackBar.error(errorList: [response.message]);
    }

    isLoading = false;
    update();
  }

  bool submitLoading = false;
  Future<void> submitReply() async {
    if (replyController.text.toString().trim().isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.messageRequired]);
      return;
    }

    submitLoading = true;
    update();

    try {
      bool b = await repo.replyTicket(
        replyController.text,
        attachmentList,
        receivedTicketModel?.id.toString() ?? "-1",
      );

      if (b) {
        await loadTicketDetailsData(shouldLoad: false);
        CustomSnackBar.success(successList: [MyStrings.repliedSuccessfully]);
        replyController.text = '';
        refreshAttachmentList();
      }
    } catch (e) {
      submitLoading = false;
      update();
    } finally {
      submitLoading = false;
      update();
    }
  }

  void setTicketModel(MyTickets? ticketModel) {
    receivedTicketModel = ticketModel;
    update();
  }

  void clearAllData() {
    refreshAttachmentList();
    replyController.clear();
    messageList.clear();
  }

  void refreshAttachmentList() {
    attachmentList.clear();
    update();
  }

  bool closeLoading = false;
  void closeTicket(String supportTicketID) async {
    closeLoading = true;
    update();
    try {
      ResponseModel responseModel = await repo.closeTicket(supportTicketID);
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );
        if (model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          clearAllData();
          // Get.back(result: "updated");
          await loadSupportTicketData();

          CustomSnackBar.success(
            successList: model.message ?? [MyStrings.requestSuccess],
          );
          Get.back();
          Get.back();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [MyStrings.requestFail],
          );
          await loadSupportTicketData();
          Get.back();
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      closeLoading = false;
      update();
    }
  }

  //download section
  bool isSubmitLoading = false;
  int selectedIndex = -1;

  Future<void> downloadAttachment(
    String url,
    int index,
    String extension,
  ) async {
    selectedIndex = index;
    isSubmitLoading = true;
    update();

    try {
      // Check storage permissions
      if (await MyUtils().checkAndRequestStoragePermission()) {
        Directory downloadsDirectory = await MyUtils.getDefaultDownloadDirectory();
        var fileName = '${moduleName}_${DateTime.now().millisecondsSinceEpoch}.$extension';

        if (downloadsDirectory.existsSync()) {
          final downloadPath = '${downloadsDirectory.path}/$fileName';

          // Try downloading the file
          try {
            ResponseModel responseModel = await ApiService.downloadFile(
              url: url,
              savePath: downloadPath,
            );
            // await MyUtils().openFile(downloadPath, extension);
            CustomSnackBar.success(successList: [responseModel.message]);
          } catch (e) {
            CustomSnackBar.error(errorList: ["Failed to download file: $e"]);
          }
        } else {
          CustomSnackBar.error(
            errorList: ["Download directory does not exist."],
          );
        }
      } else {
        CustomSnackBar.error(
          errorList: ["Storage permission is required to download files."],
        );
      }
    } catch (e) {
      printE(e.toString());
    }
    selectedIndex = -1;
    isSubmitLoading = false;
    update();
  }
}
