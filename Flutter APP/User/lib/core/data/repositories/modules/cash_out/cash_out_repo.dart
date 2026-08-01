import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class CashOutRepo {
  Future<ResponseModel> checkAgentExist({String usernameOrPhone = ""}) async {
    Map<String, String> params = {'agent': usernameOrPhone};
    String url = '${UrlContainer.baseUrl}${UrlContainer.agentExistEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> cashOutInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.cashOutEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> cashOutRequest({
    String amount = "",
    String user = "",
    String pin = "",
    String otpType = "",
    String remark = "cash_out",
  }) async {
    Map<String, String> params = {
      'amount': amount,
      'agent': user,
      'pin': pin,
      'verification_type': otpType,
      'remark': remark,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.cashOutStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> cashOutHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.cashOutHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "cash_out",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }
}
