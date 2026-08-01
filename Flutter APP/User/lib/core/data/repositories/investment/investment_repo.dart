import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/services/api_service.dart';
import 'package:ovopay/core/utils/url_container.dart';

class InvestmentRepo {
  Future<ResponseModel> investmentPlanRepo(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.investmentPlan}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> investmentHistoryRepo(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.investmentHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> makeInvestmentRequest({
    String investAmount = "",
    String planId = "",
    String pin = "",
    String otpType = "",
    String remark = "investment",
  }) async {
    Map<String, String> params = {
      'invest_amount': investAmount,
      'plan_id': planId,
      'pin': pin,
      'verification_type': otpType,
      'remark': remark,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.makeInvestmentStoreEndPoint}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> makeInvestmentHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.makePaymentHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "investment",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }
}
