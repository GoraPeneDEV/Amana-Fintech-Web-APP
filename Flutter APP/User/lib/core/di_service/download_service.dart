import 'dart:io';

import 'package:dio/dio.dart';
import 'package:open_file/open_file.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import '../../app/components/snack_bar/show_custom_snackbar.dart';
import '../utils/my_strings.dart';
import '../utils/util.dart';

class DownloadService {
  static String? extractFileExtension(String value) {
    RegExp regExp = RegExp(r'\.([a-zA-Z0-9]+)$');
    Match? match = regExp.firstMatch(value);
    return match?.group(1);
  }

  static Future<bool> downloadPDF({
    required String url,
    required String fileName,
  }) async {
    if (!await MyUtils().checkAndRequestStoragePermission()) {
      CustomSnackBar.error(
        errorList: ["Storage permission is required to download files."],
      );
      return false;
    }
    printX("Download PDF Service call $url");
    String accessToken = SharedPreferenceService.getAccessToken();
    Dio dio = Dio();

    Directory directory = Directory('/storage/emulated/0/Download');

    String filePath = "${directory.path}/$fileName";

    try {
      await dio.download(
        url,
        filePath,
        options: Options(
          headers: {
            "Authorization": "Bearer $accessToken",
            "Accept": "application/pdf",
          },
        ),
        onReceiveProgress: (received, total) {
          if (total != -1) {
            printX(
              "Download Progress: \${(received / total * 100).toStringAsFixed(2)}%",
            );
          }
        },
      );
      printX('✅ PDF downloaded successfully: $filePath');
      CustomSnackBar.success(successList: [MyStrings.fileDownloadedSuccess]);
      openDownloadedFile(filePath);
      return true;
    } catch (e) {
      printX('Download failed: $e');
      CustomSnackBar.error(errorList: ["Download failed. Please try again."]);
      return false;
    }
  }

  static Future<void> openDownloadedFile(String filePath) async {
    try {
      await OpenFile.open(filePath);
    } catch (e) {
      printX("ERROR: ${e.toString()}");
    }
  }
}
