import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class AirtimeRechargeRepo {
  Future<ResponseModel> airtimeRechargeInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.airtimeEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> airtimeRechargeOperatorInfoData({
    String countryID = "",
  }) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.airtimeOperatorDetailsEndPoint}/$countryID';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> airTimeTopUpRequest({
    String operator = "",
    String countryID = "",
    String callingCode = "",
    String amount = "",
    String mobile = "",
    String remark = "mobile_recharge",
    String otpType = "",
  }) async {
    Map<String, String> params = {
      'operator': operator,
      "country": countryID,
      "calling_code": callingCode,
      'amount': amount,
      'mobile_number': mobile,
      'remark': remark,
      'verification_type': otpType,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.airtimeStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> airtimeRechargeHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.airtimeHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "mobile_recharge",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }
}
