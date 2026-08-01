import 'dart:io';

import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/support_ticket/new_ticket_store_model.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

import '../../../../app/components/snack_bar/show_custom_snackbar.dart';
import '../../services/service_exporter.dart';

class SupportRepo {
  Future<ResponseModel> getCommunityGroupListList() async {
    String url = "${UrlContainer.baseUrl}${UrlContainer.communityGroupsEndPoint}";
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> getSupportMethodsList() async {
    String url = "${UrlContainer.baseUrl}${UrlContainer.supportMethodsEndPoint}";
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> getSupportTicketList(String page) async {
    String url = "${UrlContainer.baseUrl}${UrlContainer.supportListEndPoint}?page=$page";
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> storeTicket(TicketStoreModel model) async {
    String url = "${UrlContainer.baseUrl}${UrlContainer.storeSupportEndPoint}";
    Map<String, String> params = {
      'subject': model.subject,
      'message': model.message,
      'priority': model.priority,
    };

    Map<String, File> attachmentFiles = model.list!.asMap().map(
          (index, value) => MapEntry("attachments[$index]", value),
        );

    final response = await ApiService.postMultiPartRequest(
      url,
      params,
      attachmentFiles,
    );

    return response;
  }

  Future<dynamic> getSingleTicket(String ticketId) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.supportViewEndPoint}/$ticketId';
    ResponseModel response = await ApiService.getRequest(url);
    return response;
  }

  Future<dynamic> replyTicket(
    String message,
    List<File> fileList,
    String ticketId,
  ) async {
    try {
      String url = "${UrlContainer.baseUrl}${UrlContainer.supportReplyEndPoint}/$ticketId";
      Map<String, String> map = {'message': message.toString()};

      Map<String, File> attachmentFiles = fileList.asMap().map(
            (index, value) => MapEntry("attachments[$index]", value),
          );
      printE(attachmentFiles.length);

      final response = await ApiService.postMultiPartRequest(
        url,
        map,
        attachmentFiles,
      );

      AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
        response.responseJson,
      );

      if (model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
        return true;
      } else {
        CustomSnackBar.error(errorList: model.message ?? [MyStrings.error]);
        return false;
      }
    } catch (e) {
      return false;
    }
  }

  Future<dynamic> closeTicket(String ticketId) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.supportCloseEndPoint}/$ticketId';
    ResponseModel response = await ApiService.postRequest(url, {});
    return response;
  }
}

class ReplyTicketModel {
  final String? message;
  final List<File>? fileList;

  ReplyTicketModel(this.message, this.fileList);
}
