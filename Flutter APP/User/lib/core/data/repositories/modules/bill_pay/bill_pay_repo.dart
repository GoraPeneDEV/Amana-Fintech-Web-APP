import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class BillPayRepo {
  Future<ResponseModel> billPayInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.billPayCategoryEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> billPayRequest({
    String utilityID = "",
    String userSavedUtilityID = "",
    String uniqueID = "",
    String amount = "",
    String otpType = "",
    String reference = "",
    bool saveInformation = false,
    String fixedAmountID = "",
    String remark = "utility_bill",
  }) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.billPayEndPoint}';

    //Field value map
    Map<String, String> finalFieldValueMap = {};

    finalFieldValueMap["company_id"] = utilityID;
    finalFieldValueMap["user_company_id"] = userSavedUtilityID;
    finalFieldValueMap["unique_id"] = uniqueID;
    finalFieldValueMap["amount"] = amount;
    finalFieldValueMap["remark"] = remark;
    finalFieldValueMap["reference"] = reference;
    finalFieldValueMap["verification_type"] = otpType;
    if (saveInformation) {
      finalFieldValueMap["save_information"] = "1";
    }
    if (fixedAmountID != "") {
      finalFieldValueMap["amount_id"] = fixedAmountID;
    }
    ResponseModel response = await ApiService.postRequest(url, finalFieldValueMap);

    return response;
  }

  Future<ResponseModel> billPayHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.billPayHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "utility_bill",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<ResponseModel> deleteSavedAccount(String id) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.billPayCompanyDeleteEndPoint}/$id';
    final response = await ApiService.postRequest(url, {});
    return response;
  }
}
