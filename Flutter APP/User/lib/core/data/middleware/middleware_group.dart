import 'package:ovopay/core/data/middleware/app_middleware.dart';

class MiddlewareGroup {
  final List<AppMiddleware> _middlewares;

  MiddlewareGroup(this._middlewares);

  void handleResponse(dynamic response) {
    for (var middleware in _middlewares) {
      middleware.handleResponse(response);
    }
  }
}
