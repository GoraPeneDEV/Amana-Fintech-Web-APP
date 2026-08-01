import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../../utils/util_exporter.dart';
import '../../../services/service_exporter.dart';

class NotificationRepo {
  Future<ResponseModel> notificationHistory(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.notificationEndPoint}?page=$page';
    final response = await ApiService.getRequest(url);
    return response;
  }
}
