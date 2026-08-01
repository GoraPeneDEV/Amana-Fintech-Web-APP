import 'dart:io';

import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../models/modules/virtual_cards/virtual_cards_response_model.dart';
import '../../../services/service_exporter.dart';

class VirtualCardsRepo {
  Future<ResponseModel> cardInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardListEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> singleCardInfoData({String cardID = ""}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardSingleEndPoint}/$cardID';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> singleCardConfidentialInfoInfoData({
    String cardID = "",
    required String pin,
  }) async {
    Map<String, String> params = {'pin': pin.toString()};

    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardConfidentialEndPoint}/$cardID';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> addCardBalance({
    String cardID = "",
    required String amount,
  }) async {
    Map<String, String> params = {'amount': amount.toString()};

    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardAddFundEndPoint}/$cardID';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> cancelVirtualCard({String cardID = ""}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardCancelEndPoint}/$cardID';
    final response = await ApiService.postRequest(url, {});
    return response;
  }

  Future<ResponseModel> cardHolderListData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardNewEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> createVirtualCardRequest({
    String usabilityType = "",
    String cardHolderType = "",
    required CardHolder cardHolder,
    File? govFile1,
    File? govFile2,
    String countryID = "",
  }) async {
    Map<String, String> params = {
      'usability_type': usabilityType.toString(),
      'card_holder_type': cardHolderType.toString(),
    };

    if (cardHolderType == "1") {
      params['card_holder'] = (cardHolder.id ?? "").toString();
    } else {
      params.addAll({
        'card_name': (cardHolder.name ?? "").toString(),
        'first_name': (cardHolder.firstName ?? "").toString(),
        'last_name': (cardHolder.lastName ?? "").toString(),
        'email': (cardHolder.email ?? "").toString(),
        'mobile_number': (cardHolder.phoneNumber ?? "").toString(),
        'address': (cardHolder.address ?? "").toString(),
        'state': (cardHolder.state ?? "").toString(),
        'zip_code': (cardHolder.postalCode ?? "").toString(),
        'city': (cardHolder.city ?? "").toString(),
        'birthday': (cardHolder.dob?.day ?? "").toString(),
        'birthday_month': (cardHolder.dob?.month ?? "").toString(),
        'birthday_year': (cardHolder.dob?.year ?? "").toString(),
        'country': countryID.toString(),
      });
    }

    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardStoreEndPoint}';
    // Attachments file list
    Map<String, File> attachmentFiles = {};

    // Add files to the map if they are not null
    if (govFile1 != null) {
      attachmentFiles["document_front"] = govFile1;
    }
    if (govFile2 != null) {
      attachmentFiles["document_back"] = govFile2;
    }
    printW(params);
    final response = await ApiService.postMultiPartRequest(
      url,
      params,
      attachmentFiles,
    );
    return response;
  }

  Future<ResponseModel> virtualCardAllHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.virtualCardHistoryEndPoint}?page=$page';
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
