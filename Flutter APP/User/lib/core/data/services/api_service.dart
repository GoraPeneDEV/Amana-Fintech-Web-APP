import 'dart:io';
import 'dart:typed_data';

import 'package:dio/dio.dart';
import 'package:ovopay/core/data/middleware/auth_middleware.dart';
import 'package:ovopay/core/data/middleware/kyc_middle_ware.dart';
import 'package:ovopay/core/data/middleware/maintenance_middle_ware.dart';
import 'package:ovopay/core/data/middleware/middleware_group.dart';
import 'package:ovopay/core/data/middleware/no_internet_middleware.dart';
import 'package:ovopay/core/data/middleware/profile_status_middleware.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:path_provider/path_provider.dart';

class ApiService {
  static final Dio _dio = Dio(
    BaseOptions(
      baseUrl: UrlContainer.baseUrl,
      connectTimeout: const Duration(seconds: 30), // 5 seconds
      receiveTimeout: const Duration(seconds: 30), // 3 seconds
      headers: {'Content-Type': 'application/json'},
    ),
  );

  /// Add interceptors to handle request logging and authentication tokens
  static void init() {
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Log the request details
          printX("""
            *** API Request ***
            URL: ${options.uri}
            
            Method: ${options.method}
            
            Headers: ${options.headers}
            
            Request Body: ${options.data}
            
            Query Parameters: ${options.queryParameters}
           """);

          // Add Authorization token if available
          String? token = SharedPreferenceService.getAccessToken();
          if (token != "" && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
            printW("TOKEn: $token");
          }
          return handler.next(options); // Continue the request
        },
        onResponse: (response, handler) {
          // Log the response details

          printX("""
            *** API Response ***
            
           REQ URl: ${response.requestOptions.uri}
            Status Code: ${response.statusCode}
            
            Response Data: ${response.data}
           """);
          if (response.statusCode == 200) {
            try {
              // Initialize middlewares
              final middlewares = [
                KycMiddleware(),
                ProfileStatusMiddleware(),
                MaintenanceMiddleware(),
                // Add other middlewares here if needed
              ];

              MiddlewareGroup(middlewares).handleResponse(response);
            } catch (e) {
              e.toString();
            }
          }

          return handler.next(response); // Continue the response
        },
        onError: (DioException error, handler) {
          // Log the error details
          printE("""
          *** API Error ***
          Error Message: ${error.message}
          Error Exception: ${error.error}
          Error Message: ${error.type}
          Status Code: ${error.response?.statusCode}
          Error Data: ${error.response?.data}
          """);

          // Handle specific error cases, e.g., unauthorized
          if (error.response?.statusCode == 401) {
            printE('Unauthorized, redirecting to login...');
            AuthMiddleware().handleResponse(null);
          }
          // DioExceptionType.connectionError or no internet handler
          if (error.type == DioExceptionType.connectionError) {
            printE('No internet connection. Please check your network.');
            NoInetnetMiddleware().handleResponse(error.response);
          }

          return handler.next(error); // Continue the error handling
        },
      ),
    );
  }

  /// GET request
  static Future<ResponseModel> getRequest(
    String endpoint, {
    Map<String, dynamic>? params,
  }) async {
    try {
      Response response = await _dio.get(endpoint, queryParameters: params);
      return ResponseModel(
        isSuccess: true,
        message: response.data.toString(),
        statusCode: response.statusCode ?? 200,
        responseJson: response.data,
      );
    } on DioException catch (e) {
      printE(
        'GET request error: ${e.type}, ${e.response?.statusCode}, ${e.message}',
      );
      // Handle Errors
      return _handleDioError(e);
    }
  }

  static Future<ResponseModel> postRequest(
    String endpoint,
    Map<String, dynamic>? data, {
    bool asBytes = false, // Add a parameter to control the response type
  }) async {
    try {
      Response response = await _dio.post(
        endpoint,
        data: data,
        options: Options(
          responseType: asBytes ? ResponseType.bytes : ResponseType.json, // Set response type
        ),
      );

      return ResponseModel(
        isSuccess: true,
        message: asBytes ? 'Byte data received' : response.data.toString(),
        statusCode: response.statusCode ?? 200,
        responseJson: asBytes ? response.data : response.data, // Byte data is in response.data
      );
    } on DioException catch (e) {
      printE('POST request error: ${e.response?.statusCode}, ${e.message}');
      // Handle Errors
      return _handleDioError(e);
    }
  }

  /// PUT request
  static Future<ResponseModel> putRequest(
    String endpoint,
    Map<String, dynamic> data,
  ) async {
    try {
      Response response = await _dio.put(endpoint, data: data);
      return ResponseModel(
        isSuccess: true,
        message: response.data.toString(),
        statusCode: response.statusCode ?? 200,
        responseJson: response.data,
      );
    } on DioException catch (e) {
      printE('PUT request error: ${e.response?.statusCode}, ${e.message}');
      // Handle Errors
      return _handleDioError(e);
    }
  }

  /// DELETE request
  static Future<ResponseModel> deleteRequest(
    String endpoint, {
    Map<String, dynamic>? data,
  }) async {
    try {
      Response response = await _dio.delete(endpoint, data: data);
      return ResponseModel(
        isSuccess: true,
        message: response.data.toString(),
        statusCode: response.statusCode ?? 200,
        responseJson: response.data,
      );
    } on DioException catch (e) {
      printE('DELETE request error: ${e.response?.statusCode}, ${e.message}');
      // Handle Errors
      return _handleDioError(e);
    }
  }

  /// Multipart POST request with dynamic keys for files
  static Future<ResponseModel> postMultiPartRequest(
    String endpoint,
    Map<String, dynamic> data,
    Map<String, File> files,
  ) async {
    try {
      // Create a FormData object
      FormData formData = FormData();

      // Add the text fields to the FormData
      data.forEach((key, value) {
        formData.fields.add(MapEntry(key, value.toString()));
      });

      // Add files to the FormData with dynamic keys
      files.forEach((key, file) async {
        formData.files.add(
          MapEntry(
            key, // Dynamic key for each file
            await MultipartFile.fromFile(
              file.path,
              filename: file.path.split('/').last,
            ),
          ),
        );
      });

      // Make the POST request
      Response response = await _dio.post(endpoint, data: formData);
      return ResponseModel(
        isSuccess: true,
        message: response.data.toString(),
        statusCode: response.statusCode ?? 200,
        responseJson: response.data,
      );
    } on DioException catch (e) {
      printE(
        'Multipart POST request error: ${e.response?.statusCode}, ${e.message}',
      );
      // Handle Errors
      return _handleDioError(e);
    }
  }

  /// Download file from an endpoint
  static Future<ResponseModel> downloadFile({
    String? url,
    Uint8List? byteData,
    String? savePath,
  }) async {
    try {
      if (url == null && byteData == null) {
        throw ArgumentError("Either 'url' or 'byteData' must be provided.");
      }

      // Define the save path
      savePath ??= '${(await getTemporaryDirectory()).path}/downloaded_file';

      if (url != null) {
        // Download from URL
        printX("Downloading file from $url");

        // Start the download
        Response response = await _dio.download(
          url,
          savePath,
          onReceiveProgress: (received, total) {
            if (total != -1) {
              printX(
                "Download progress: ${(received / total * 100).toStringAsFixed(0)}%",
              );
            }
          },
        );

        return ResponseModel(
          isSuccess: true,
          message: "File downloaded successfully",
          statusCode: response.statusCode ?? 200,
          responseJson: {"path": savePath},
        );
      } else if (byteData != null) {
        // Save from byte data
        printX("Saving file from provided byte data");
        File file = File(savePath);
        await file.writeAsBytes(byteData);

        return ResponseModel(
          isSuccess: true,
          message: "File saved successfully",
          statusCode: 200,
          responseJson: {"path": savePath},
        );
      }

      return ResponseModel(
        isSuccess: false,
        message: "Invalid operation",
        statusCode: 400,
        responseJson: {},
      );
    } on DioException catch (e) {
      printE('Download error: ${e.message}');
      // Handle Errors
      return _handleDioError(e);
    } catch (e) {
      printE('Error: $e');
      return ResponseModel(
        isSuccess: false,
        message: e.toString(),
        statusCode: 500,
        responseJson: {},
      );
    }
  }

  /// Error handling function
  static ResponseModel _handleDioError(DioException e) {
    printE(
      'GET request error: ${e.type}, ${e.response?.statusCode}, ${e.message}',
    );

    // Handle no internet connection
    if (e.type == DioExceptionType.connectionError) {
      return ResponseModel(
        isSuccess: false,
        message: 'No internet connection. Please check your network.',
        statusCode: 503, // Custom code for no internet
        responseJson: '',
      );
    }

    // Handle specific HTTP status codes
    if (e.response != null) {
      switch (e.response!.statusCode) {
        case 401:
          return ResponseModel(
            isSuccess: false,
            message: 'Unauthorized access.',
            statusCode: 401,
            responseJson: '',
          );
        default:
          return ResponseModel(
            isSuccess: false,
            message: 'Error: ${e.response!.statusCode}',
            statusCode: e.response!.statusCode!,
            responseJson: e.response!.data.toString(),
          );
      }
    }

    // General error response
    return ResponseModel(
      isSuccess: false,
      message: e.message ?? 'An error occurred',
      statusCode: e.response?.statusCode ?? 500,
      responseJson: e.response?.data.toString() ?? '',
    );
  }
}
