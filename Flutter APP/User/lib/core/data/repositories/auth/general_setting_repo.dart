import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class GeneralSettingRepo {
  Future<ResponseModel> getGeneralSetting() async {
    String url = UrlContainer.generalSettingEndPoint;
    ResponseModel response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> getLanguage(String languageCode) async {
    String url = '${UrlContainer.languageUrl}$languageCode';
    ResponseModel response = await ApiService.getRequest(url);
    return response;
  }

  Future<ResponseModel> getCountryList() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.countryEndPoint}';
    ResponseModel model = await ApiService.getRequest(url);
    return model;
  }

  Future<ResponseModel> getModuleList() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.moduleSettingEndPoint}';
    ResponseModel model = await ApiService.getRequest(url);
    return model;
  }

  Future<ResponseModel> getNotificationSettingsInfo() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.notificationSettingsEndPoint}';
    ResponseModel model = await ApiService.getRequest(url);
    return model;
  }

  Future<ResponseModel> setNotificationSettings({
    String en = "1",
    String pn = "1",
    String sn = "1",
    String isAllowPromotionalNotify = "1",
  }) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.notificationSettingsEndPoint}';
    Map<String, String> map = {
      'en': en,
      'pn': pn,
      'sn': sn,
      'is_allow_promotional_notify': isAllowPromotionalNotify,
    };

    ResponseModel model = await ApiService.postRequest(url, map);
    return model;
  }
}
