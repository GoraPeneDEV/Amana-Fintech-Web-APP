import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class DonationRepo {
  Future<ResponseModel> donationInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.donationEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> donationRequest({
    String charityId = "",
    String name = "",
    String hideIdentity = "",
    String reference = "",
    String amount = "",
    String email = "",
    String otpType = "",
    String remark = "donation",
  }) async {
    Map<String, String> params = {
      'charity_id': charityId,
      'name': name,
      'hide_identity': hideIdentity,
      'reference': reference,
      'amount': amount,
      'email': email,
      'verification_type': otpType,
      'remark': remark,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.donationStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> donationHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.donationHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "donation",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }
}
