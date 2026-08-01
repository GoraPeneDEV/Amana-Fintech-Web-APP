import 'dart:io';

import 'package:get/get.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/support_ticket/support_ticket_response_model.dart';
import 'package:ovopay/core/data/repositories/support/support_repo.dart';

import '../../../../core/utils/util_exporter.dart';
import '../../../components/snack_bar/show_custom_snackbar.dart';

class SupportController extends GetxController {
  SupportRepo repo;
  SupportController({required this.repo});

  List<FileChooserModel> attachmentList = [
    FileChooserModel(fileName: MyStrings.noFileChosen),
  ];

  String noFileChosen = MyStrings.noFileChosen;
  String chooseFile = MyStrings.chooseFile;

  bool isLoading = false;

  int page = 0;
  String? nextPageUrl;
  List<TicketData> ticketList = [];
  String imagePath = '';
  Future<void> loadData({bool forceLoad = true}) async {
    page = 0;
    isLoading = forceLoad;

    update();
    await getSupportTicket();
    isLoading = false;
    update();
  }

  Future<void> getSupportTicket() async {
    page = page + 1;
    update();

    try {
      ResponseModel responseModel = await repo.getSupportTicketList(
        page.toString(),
      );
      if (responseModel.statusCode == 200) {
        if (page == 1) {
          ticketList.clear();
          update();
        }
        SupportTicketListResponseModel model = SupportTicketListResponseModel.fromJson(responseModel.responseJson);
        if (model.status == AppStatus.SUCCESS) {
          nextPageUrl = model.data?.tickets?.nextPageUrl;
          List<TicketData> tempList = model.data?.tickets?.data ?? [];
          imagePath = model.data?.tickets?.path.toString() ?? '';
          if (tempList.isNotEmpty) {
            ticketList.addAll(tempList);
          }
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isLoading = false;
      update();
    }
  }

  bool hasNext() {
    return nextPageUrl != null && nextPageUrl!.isNotEmpty ? true : false;
  }
}

class FileChooserModel {
  late String fileName;
  late File? choosenFile;
  FileChooserModel({required this.fileName, this.choosenFile});
}
