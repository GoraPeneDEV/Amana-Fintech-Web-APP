import 'dart:io';

import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/profile/profile_post_model.dart';
import 'package:ovopay/core/data/models/profile_complete/profile_complete_post_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class ProfileRepo {
  Future<ResponseModel> updateProfile(ProfilePostModel model) async {
    String url = UrlContainer.updateProfileEndPoint;

    Map<String, String> finalMap = {
      'firstname': model.firstname,
      'lastname': model.lastName,
      'address': model.address ?? '',
      'zip': model.zip ?? '',
      'state': model.state ?? "",
      'city': model.city ?? '',
    };
    //Attachments file list
    Map<String, File> attachmentFiles = {};
    if (model.image != null) {
      attachmentFiles = {"image": model.image!};
    }

    ResponseModel responseModel = await ApiService.postMultiPartRequest(
      url,
      finalMap,
      attachmentFiles,
    );
    return responseModel;
  }

  Future<ResponseModel> completeProfile(ProfileCompletePostModel model) async {
    dynamic params = model.toMap();
    String url = '${UrlContainer.baseUrl}${UrlContainer.profileCompleteEndPoint}';
    ResponseModel responseModel = await ApiService.postRequest(url, params);
    return responseModel;
  }

  Future<ResponseModel> loadProfileInfo() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.getProfileEndPoint}';
    ResponseModel responseModel = await ApiService.getRequest(url);
    return responseModel;
  }

  Future<ResponseModel> logout() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.logoutUrl}';
    ResponseModel responseModel = await ApiService.getRequest(url);
    return responseModel;
  }

  Future<ResponseModel> checkPinOfAccount({String pin = ""}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.pinValidate}';
    ResponseModel responseModel = await ApiService.postRequest(url, {
      "pin": pin,
    });
    return responseModel;
  }

  Future<ResponseModel> deleteAccount({String pin = ""}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.deleteAccountEndPoint}';
    ResponseModel responseModel = await ApiService.postRequest(url, {
      "pin": pin,
    });
    return responseModel;
  }

  Future<void> updateDeviceToken() async {
    await PushNotificationService().sendUserToken();
  }

  Future<dynamic> getCountryList() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.countryEndPoint}';
    ResponseModel model = await ApiService.getRequest(url);
    return model;
  }

  Future<ResponseModel> getMyQrCodeData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.myQrCodeEndPoint}';
    ResponseModel model = await ApiService.getRequest(url);
    return model;
  }

  Future<ResponseModel> downloadMyQrCodeData() async {
    Map<String, String> params = {};
    String url = '${UrlContainer.baseUrl}${UrlContainer.myQrCodeDownloadEndPoint}';
    ResponseModel model = await ApiService.postRequest(
      url,
      params,
      asBytes: true,
    );
    return model;
  }

  Future<ResponseModel> scanQrCodeData({String code = ""}) async {
    Map<String, String> params = {'code': code};
    String url = '${UrlContainer.baseUrl}${UrlContainer.qrCodeScanEndPoint}';
    ResponseModel model = await ApiService.postRequest(url, params);
    return model;
  }

  Future<ResponseModel> qrCodeLogin({required String code}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.qrCodeLoginEndPoint}/$code';
    ResponseModel model = await ApiService.postRequest(url, {});
    return model;
  }
}
