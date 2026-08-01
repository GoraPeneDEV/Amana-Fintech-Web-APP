import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class SendMoneyRepo {
  Future<ResponseModel> checkUserExist({String usernameOrPhone = ""}) async {
    Map<String, String> params = {'user': usernameOrPhone};
    String url = '${UrlContainer.baseUrl}${UrlContainer.userExistEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> sendMoneyInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.sendMoneyEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> sendMoneyRequest({
    String amount = "",
    String user = "",
    String otpType = "",
    String remark = "send_money",
  }) async {
    Map<String, String> params = {
      'amount': amount,
      'user': user,
      'verification_type': otpType,
      'remark': remark,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.sendMoneyStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> sendMoneyHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.sendMoneyHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "send_money",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }
}
