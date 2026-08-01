import 'dart:io';

import 'package:ovopay/core/data/models/global/formdata/dynamic_file_value_keeper_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class BankTransferRepo {
  Future<ResponseModel> bankTransferInfoData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.bankTransferEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> bankTransferRequest({
    String userBankId = "",
    String accountName = "",
    String accountNumber = "",
    String amount = "",
    String remark = "bank_transfer",
    String otpType = "",
  }) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.bankTransferStoreEndPoint}';

    //Field value map
    Map<String, String> finalFieldValueMap = {};

    finalFieldValueMap["bank_account_id"] = userBankId;
    finalFieldValueMap["amount"] = amount;
    finalFieldValueMap["remark"] = remark;
    finalFieldValueMap["verification_type"] = otpType;

    ResponseModel response = await ApiService.postRequest(
      url,
      finalFieldValueMap,
    );

    printW(response.responseJson);

    return response;
  }

  List<Map<String, String>> fieldValueList = [];
  List<DynamicFileValueKeeperModel> filesDataList = [];

  Future<ResponseModel> bankTransferOneTimeRequest({
    String bankId = "",
    String accountName = "",
    String accountNumber = "",
    String amount = "",
    String remark = "bank_transfer",
    String otpType = "",
    required List<KycFormModel> dynamicFormList,
  }) async {
    fieldValueList.clear();
    await modelToMap(dynamicFormList);
    String url = '${UrlContainer.baseUrl}${UrlContainer.bankTransferStoreEndPoint}';

    //Field value map
    Map<String, String> finalFieldValueMap = {};
    for (var element in fieldValueList) {
      finalFieldValueMap.addAll(element);
    }
    finalFieldValueMap["bank_id"] = bankId;
    finalFieldValueMap["account_number"] = accountNumber;
    finalFieldValueMap["account_holder"] = accountName;

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

    printW(response.responseJson);

    return response;
  }

  Future<ResponseModel> saveBankAccountRequest({
    String bankId = "",
    String accountName = "",
    String accountNumber = "",
    required List<KycFormModel> dynamicFormList,
  }) async {
    fieldValueList.clear();
    await modelToMap(dynamicFormList);
    String url = '${UrlContainer.baseUrl}${UrlContainer.bankTransferAddBankEndPoint}';

    //Field value map
    Map<String, String> finalFieldValueMap = {};
    for (var element in fieldValueList) {
      finalFieldValueMap.addAll(element);
    }
    finalFieldValueMap["bank_id"] = bankId;
    finalFieldValueMap["account_number"] = accountNumber;
    finalFieldValueMap["account_holder"] = accountName;
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

    printW(response.responseJson);

    return response;
  }

  Future<ResponseModel> deleteBankAccount(String id) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.bankTransferDeleteBankEndPoint}/$id';
    final response = await ApiService.postRequest(url, {});
    return response;
  }

  Future<ResponseModel> bankTransferHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.bankTransferHistoryEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> pinVerificationRequest({
    String pin = "",
    String remark = "bank_transfer",
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
