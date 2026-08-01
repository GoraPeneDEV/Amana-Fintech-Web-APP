import 'dart:io';

import 'package:ovopay/core/data/models/global/formdata/dynamic_file_value_keeper_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class EducationRepo {
  Future<ResponseModel> educationInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.educationFeeEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  List<Map<String, String>> fieldValueList = [];
  List<DynamicFileValueKeeperModel> filesDataList = [];

  Future<ResponseModel> educationRequest({
    String instituteID = "",
    String amount = "",
    String otpType = "",
    String remark = "education_fee",
    required List<KycFormModel> dynamicFormList,
  }) async {
    fieldValueList.clear();
    await modelToMap(dynamicFormList);
    String url = '${UrlContainer.baseUrl}${UrlContainer.educationStoreEndPoint}';

    //Field value map
    Map<String, String> finalFieldValueMap = {};

    for (var element in fieldValueList) {
      finalFieldValueMap.addAll(element);
    }
    finalFieldValueMap["institution_id"] = instituteID;
    finalFieldValueMap["amount"] = amount;
    finalFieldValueMap["remark"] = remark;
    finalFieldValueMap["verification_type"] = otpType;

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

  Future<ResponseModel> educationHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.educationHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "education_fee",
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
