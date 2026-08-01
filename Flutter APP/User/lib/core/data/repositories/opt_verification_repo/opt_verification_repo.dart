import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class OtpVerificationRepo extends ApiService {
  Future<ResponseModel> verify(String code, String actionRemark) async {
    final map = {'otp': code, 'remark': actionRemark};

    String url = '${UrlContainer.baseUrl}${UrlContainer.verifyOtp}';
    ResponseModel responseModel = await ApiService.postRequest(url, map);
    return responseModel;
  }

  Future<ResponseModel> resendVerifyCode(String actionId) async {
    final map = {'action_id': actionId};
    String url = '${UrlContainer.baseUrl}${UrlContainer.otpResend}';
    ResponseModel response = await ApiService.postRequest(url, map);
    return response;
  }
}
