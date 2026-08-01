import 'package:dio/dio.dart' as dio;
import 'package:get/get.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/data/middleware/app_middleware.dart';

class MaintenanceMiddleware implements AppMiddleware {
  @override
  void handleResponse(response) {
    var responseData = response as dio.Response;

    if (responseData.data['remark'] == 'maintenance_mode') {
      if (Get.currentRoute != RouteHelper.maintenanceScreen) {
        Get.offAllNamed(RouteHelper.maintenanceScreen);
      }
    }
  }
}
