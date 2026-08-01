import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class PrivacyRepo {
  Future<dynamic> loadAppPagesData() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.privacyPolicyEndPoint}';
    final response = await ApiService.getRequest(url);
    return response;
  }
}
