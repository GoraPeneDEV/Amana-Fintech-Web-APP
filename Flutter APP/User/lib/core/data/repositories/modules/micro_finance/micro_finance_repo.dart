import 'dart:io';

import 'package:ovopay/core/data/models/global/formdata/dynamic_file_value_keeper_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class MicroFinanceRepo {
  Future<ResponseModel> microfinanceInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.microfinanceEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  List<Map<String, String>> fieldValueList = [];
  List<DynamicFileValueKeeperModel> filesDataList = [];

  Future<ResponseModel> billPayRequest({
    String ngoId = "",
    String amount = "",
    String pin = "",
    String otpType = "",
    String remark = "microfinance",
    required List<KycFormModel> dynamicFormList,
  }) async {
    fieldValueList.clear();
    await modelToMap(dynamicFormList);
    String url = '${UrlContainer.baseUrl}${UrlContainer.microfinanceStoreEndPoint}';

    //Field value map
    Map<String, String> finalFieldValueMap = {};
    finalFieldValueMap["ngo_id"] = ngoId;
    finalFieldValueMap["pin"] = pin;
    finalFieldValueMap["amount"] = amount;
    finalFieldValueMap["remark"] = remark;
    finalFieldValueMap["verification_type"] = otpType;
    for (var element in fieldValueList) {
      finalFieldValueMap.addAll(element);
    }
    //Attachments file list
    Map<String, File> attachmentFiles = filesDataList.asMap().map(
          (index, value) => MapEntry(value.key, value.value),
        );
    printX(finalFieldValueMap);
    ResponseModel response = await ApiService.postMultiPartRequest(
      url,
      finalFieldValueMap,
      attachmentFiles,
    );

    return response;
  }

  Future<ResponseModel> microfinanceHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.microfinanceHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "microfinance",
  }) async {
    Map<String, String> params = {'pin': pin, 'remark': remark};
    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyPin}';
    final response = await ApiService.postRequest(url, params);
    return response;
  }

  Future<dynamic> modelToMap(List<KycFormModel> list) async {
    for (var e in list) {
      if (e.type == 'checkbox') {
        if (e.cbSelected != null && e.cbSelected!.isNotEmpty) {
          for (int i = 0; i < e.cbSelected!.length; i++) {
            fieldValueList.add({'${e.label}[$i]': e.cbSelected![i]});
          }
        }
      } else if (e.type == 'file') {
        if (e.imageFile != null) {
          filesDataList.add(
            DynamicFileValueKeeperModel(e.label!, e.imageFile!),
          );
        }
      } else if (e.type == 'select') {
        if (e.selectedValue != null && e.selectedValue.toString() != MyStrings.selectOne) {
          fieldValueList.add({e.label ?? '': e.selectedValue});
        }
      } else {
        if (e.selectedValue != null && e.selectedValue.toString().isNotEmpty) {
          fieldValueList.add({e.label ?? '': e.selectedValue});
        }
      }
    }
  }
}
