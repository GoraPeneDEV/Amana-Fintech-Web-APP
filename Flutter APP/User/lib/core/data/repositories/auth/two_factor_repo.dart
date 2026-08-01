import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class TwoFactorRepo {
  Future<ResponseModel> verify(String code) async {
    final map = {'code': code};

    String url = '${UrlContainer.baseUrl}${UrlContainer.verify2FAUrl}';
    ResponseModel responseModel = await ApiService.postRequest(url, map);

    return responseModel;
  }

  Future<ResponseModel> enable2fa(String key, String code) async {
    final map = {'secret': key, 'code': code};

    String url = '${UrlContainer.baseUrl}${UrlContainer.twoFactorEnable}';
    ResponseModel responseModel = await ApiService.postRequest(url, map);

    return responseModel;
  }

  Future<ResponseModel> disable2fa(String code) async {
    final map = {'code': code};

    String url = '${UrlContainer.baseUrl}${UrlContainer.twoFactorDisable}';
    ResponseModel responseModel = await ApiService.postRequest(url, map);

    return responseModel;
  }

  Future<ResponseModel> get2FaData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.twoFactor}';
    ResponseModel responseModel = await ApiService.getRequest(url);

    return responseModel;
  }
}
