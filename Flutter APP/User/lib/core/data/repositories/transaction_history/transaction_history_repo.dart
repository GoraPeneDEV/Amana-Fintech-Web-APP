import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import '../../../utils/util_exporter.dart';
import '../../services/service_exporter.dart';

class TransactionHistoryRepo {
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

  Future<ResponseModel> statementsHistory({
    String month = "",
    String year = "",
  }) async {
    String url = '${UrlContainer.baseUrl}${UrlContainer.statementsEndpoint}?month=$month&year=$year';
    final response = await ApiService.getRequest(url);
    return response;
  }
}
