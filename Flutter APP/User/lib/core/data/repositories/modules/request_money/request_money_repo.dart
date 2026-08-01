import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class RequestMoneyRepo {
  Future<ResponseModel> requestMoneyInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.requestMoneyEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> checkUserExist({String usernameOrPhone = ""}) async {
    Map<String, String> params = {'user': usernameOrPhone};
    String url = '${UrlContainer.baseUrl}${UrlContainer.userExistEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> requestMoneyRequest({
    String amount = "",
    String user = "",
    String remark = "request_money",
    String otpType = "",
  }) async {
    Map<String, String> params = {
      'amount': amount,
      'user': user,
      'remark': remark,
      'verification_type': otpType,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.requestMoneyStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> requestMoneyApproveRequest({
    String requestID = "",
    String amount = "",
    String user = "",
    String remark = "request_money_received",
    String otpType = "",
  }) async {
    Map<String, String> params = {
      'amount': amount,
      'user': user,
      'remark': remark,
      'verification_type': otpType,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.requestMoneyReceivedStoreEndPoint}/$requestID';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "request_money",
    String note = "",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark, 'note': note};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> approveMoneyRequest({
    String id = "",
    required String pin,
  }) async {
    Map<String, String> params = {"pin": pin};
    String url = '${UrlContainer.baseUrl}${UrlContainer.requestMoneyApprove}/$id';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> rejectMoneyRequest({String id = ""}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.requestMoneyReject}/$id';
    final response = await ApiService.postRequest(url, {});
    return response;
  }

  Future<ResponseModel> requestMoneyByMeHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.sentRequestMoneyEndPoint}?page=$page'; //&pagination=2
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> requestMoneyByMyFriends(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.receivedRequestMoneyEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }
}
