import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class MakePaymentRepo {
  Future<ResponseModel> checkMerchantExist({
    String usernameOrPhone = "",
  }) async {
    Map<String, String> params = {'merchant': usernameOrPhone};
    String url = '${UrlContainer.baseUrl}${UrlContainer.merchantExistEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> makePaymentInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.makePaymentEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> makePaymentRequest({
    String amount = "",
    String user = "",
    String pin = "",
    String otpType = "",
    String reference = "",
    String remark = "make_payment",
  }) async {
    Map<String, String> params = {
      'amount': amount,
      'merchant': user,
      'pin': pin,
      'verification_type': otpType,
      "reference": reference,
      'remark': remark,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.makePaymentStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> makePaymentHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.makePaymentHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "make_payment",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }
}
