import 'package:dio/dio.dart' as dio;
import 'package:get/get.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/data/middleware/app_middleware.dart';

class KycMiddleware implements AppMiddleware {
  @override
  void handleResponse(response) {
    var responseData = response as dio.Response;

    if (responseData.data['remark'] == 'kyc_verification' || responseData.data['remark'] == 'under_review') {
      if (Get.currentRoute != RouteHelper.kycScreen) {
        Get.offNamed(RouteHelper.kycScreen);
      }
    }
  }
}
