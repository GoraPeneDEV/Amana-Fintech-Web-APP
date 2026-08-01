class ResponseModel {
  final bool isSuccess;
  final int statusCode;
  final String message;
  final dynamic responseJson;

  ResponseModel({
    required this.isSuccess,
    required this.message,
    required this.statusCode,
    required this.responseJson,
  });
}
