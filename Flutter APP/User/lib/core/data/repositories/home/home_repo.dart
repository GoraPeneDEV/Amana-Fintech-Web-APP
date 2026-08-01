import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class HomeRepo {
  Future<ResponseModel> dashboardInfo() async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.dashBoardEndPoint}';
    ResponseModel responseModel = await ApiService.getRequest(url);
    return responseModel;
  }

  Future<ResponseModel> promotionalOffersList(int page) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.promotionalOffersEndPoint}?page=$page';
    ResponseModel responseModel = await ApiService.getRequest(url);
    return responseModel;
  }

  Future<ResponseModel> transactionHistory(
    int page, {
    String remark = "",
    String orderBy = "",
    String trxType = "",
    String search = "",
  }) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.transactionEndpoint}?page=$page&order_by=$orderBy&type=$trxType&remark=$remark&search=$search';
    final response = await ApiService.getRequest(url);
    return response;
  }
}
