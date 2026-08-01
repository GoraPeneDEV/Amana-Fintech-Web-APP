import 'package:get/get.dart';
import 'package:ovopay/core/data/middleware/app_middleware.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';

class AuthMiddleware implements AppMiddleware {
  @override
  void handleResponse(response) {
    SharedPreferenceService.setBioMetricStatus(false);
    SharedPreferenceService.setIsLoggedIn(false);
    SharedPreferenceService.setAccessToken("");
    if (Get.currentRoute != RouteHelper.loginScreen) {
      Get.offAllNamed(RouteHelper.loginScreen);
    }
  }
}
