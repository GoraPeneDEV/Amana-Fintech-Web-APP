import 'package:dio/dio.dart' as dio;
import 'package:ovopay/core/data/middleware/app_middleware.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/route/route.dart';

class ProfileStatusMiddleware implements AppMiddleware {
  @override
  void handleResponse(response) {
    var responseData = response as dio.Response;
    if (responseData.data['remark'] == 'profile_incomplete' || responseData.data['remark'] == 'mobile_unverified' || responseData.data['remark'] == "unverified") {
      AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
        response.data,
      );
      RouteHelper.checkUserStatusAndGoToNextStep(model.data?.user);
    }
  }
}
