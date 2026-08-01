import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class GiftCardRepo {
  Future<ResponseModel> allProductInfoData(String pram) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.allGiftCardEndPoint}$pram';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> giftPurchaseRequest({
    String giftId = "-1",
    String amount = "",
    String email = "",
    String name = "",
    String quantity = "",
    String otpType = "",
    String remark = "gift_card",
  }) async {
    Map<String, String> params = {
      'amount': amount,
      'email': email,
      'name': name,
      'quantity': quantity,
      'verification_type': otpType,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.giftPurchaseEndPoint}/$giftId';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> giftCardHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.giftCardHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "gift_card",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> downloadGiftCardPurchaseReceipt(String id) async {
    Map<String, String> params = {};
    String url = '${UrlContainer.baseUrl}${UrlContainer.giftCardPdfDownloadEndPoint}/$id';
    ResponseModel model = await ApiService.postRequest(
      url,
      params,
      asBytes: true,
    );
    return model;
  }
}
