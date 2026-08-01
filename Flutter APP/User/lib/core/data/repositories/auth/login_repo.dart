import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class LoginRepo {
  Future<ResponseModel> loginUser(
    String countryCode,
    String mobile,
    String pin,
  ) async {
    Map<String, String> map = {
      'country': countryCode,
      'mobile_number': mobile,
      'pin': pin,
    };
    String url = '${UrlContainer.baseUrl}${UrlContainer.authenticationEndPoint}';
    final response = await ApiService.postRequest(url, map);
    return response;
  }

  Future<ResponseModel> registerUser(String countryCode, String mobile) async {
    Map<String, String> map = {'country': countryCode, 'mobile_number': mobile};
    String url = '${UrlContainer.baseUrl}${UrlContainer.authenticationEndPoint}';
    final response = await ApiService.postRequest(url, map);
    return response;
  }

  Future<ResponseModel> forgetPassword(
    String countryCode,
    String mobileNo,
  ) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.forgetPasswordEndPoint}';
    final response = await ApiService.postRequest(url, {
      'country': countryCode,
      "mobile_number": mobileNo,
    });

    return response;
  }

  Future<ResponseModel> verifyForgetPassCode(
    String code,
    String mobileNo,
  ) async {
    Map<String, String> map = {'code': code, 'mobile_number': mobileNo};

    String url = '${UrlContainer.baseUrl}${UrlContainer.passwordVerifyEndPoint}';

    final response = await ApiService.postRequest(url, map);

    return response;
  }

  Future<ResponseModel> resetPin(
    String email,
    String pin,
    String cPin,
    String code,
  ) async {
    Map<String, String> map = {
      'token': code,
      'mobile_number': email,
      'pin': pin,
      'pin_confirmation': cPin,
    };

    String url = '${UrlContainer.baseUrl}${UrlContainer.resetPasswordEndPoint}';

    ResponseModel responseModel = await ApiService.postRequest(url, map);

    return responseModel;
  }

  Future<ResponseModel> sendAuthorizationRequest() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.authorizationCodeEndPoint}';
    ResponseModel responseModel = await ApiService.getRequest(url);
    return responseModel;
  }
}
