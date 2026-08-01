import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class ChangePasswordRepo {
  String token = '', tokenType = '';

  Future<ResponseModel> changePassword(
    String currentPass,
    String password,
  ) async {
    final params = modelToMap(currentPass, password);
    String url = '${UrlContainer.baseUrl}${UrlContainer.changePasswordEndPoint}';

    ResponseModel responseModel = await ApiService.postRequest(url, params);
    return responseModel;
  }

  Map<String, dynamic> modelToMap(String currentPassword, String newPass) {
    Map<String, dynamic> map2 = {
      'current_pin': currentPassword,
      'pin': newPass,
      'pin_confirmation': newPass,
    };
    return map2;
  }
}
