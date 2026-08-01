import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

import '../../services/service_exporter.dart';

class BiometricRepo {
  Future<ResponseModel> checkPinOfAccount({String pin = ""}) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.pinValidate}';
    ResponseModel responseModel = await ApiService.postRequest(url, {
      "pin": pin,
    });
    return responseModel;
  }
}
