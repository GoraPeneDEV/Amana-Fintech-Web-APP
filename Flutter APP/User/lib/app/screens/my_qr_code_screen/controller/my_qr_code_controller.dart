import 'dart:convert';
import 'dart:io';

import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/qr_code/scan_qr_code_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import 'package:ovopay/environment.dart';

import '../../../../core/data/models/profile/profile_response_model.dart';
import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';

class MyQrCodeController extends GetxController {
  ProfileRepo profileRepo = ProfileRepo();
  bool isLoading = false;
  String qrCodeLink = "";

  Future<void> getMyQrCodeData() async {
    isLoading = true;
    update();

    try {
      ResponseModel responseModel = await profileRepo.getMyQrCodeData();

      if (responseModel.statusCode == 200) {
        ProfileResponseModel model = ProfileResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (model.status.toString() == "success") {
          qrCodeLink = model.data?.qrCode ?? "";
          isLoading = false;
          update();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e);
    }
    isLoading = false;
    update();
  }

  bool isDownloadLoading = false;
  Future<void> downloadAttachment(String url, String extension) async {
    // Update UI to indicate loading state
    isDownloadLoading = true;
    update();

    try {
      // Check storage permissions
      if (await MyUtils().checkAndRequestStoragePermission()) {
        Directory downloadsDirectory = await MyUtils.getDefaultDownloadDirectory();
        var fileName = '${Environment.appName}_${DateTime.now().millisecondsSinceEpoch}.$extension';

        if (downloadsDirectory.existsSync()) {
          final downloadPath = '${downloadsDirectory.path}/$fileName';

          // Try downloading the file
          try {
            ResponseModel responseModel = await profileRepo.downloadMyQrCodeData();

            ResponseModel downloadModel = await ApiService.downloadFile(
              byteData: responseModel.responseJson,
              savePath: downloadPath,
            );
            await MyUtils().openFile(downloadPath, extension);
            CustomSnackBar.success(successList: [downloadModel.message]);
          } catch (e) {
            CustomSnackBar.error(errorList: ["Failed to download file: $e"]);
          }
        } else {
          CustomSnackBar.error(
            errorList: ["Download directory does not exist."],
          );
        }
      } else {
        CustomSnackBar.error(
          errorList: ["Storage permission is required to download files."],
        );
      }
    } catch (e) {
      printE(e.toString());
    }
    // Reset UI loading state
    isDownloadLoading = false;
    update();
  }

  //Scan Qr Code
  //Check if the user exists
  //Check user exist

  String scanUserType = "";
  UserModel? existUserModel;
  bool isScanningQrCodeLoading = false;
  Future<ScanQrCodeResponseModel> scanQrCodeDataFromServer({
    String inputText = "",
  }) async {
    try {
      isScanningQrCodeLoading = true;
      update();

      ResponseModel responseModel = await profileRepo.scanQrCodeData(
        code: inputText,
      );
      if (responseModel.statusCode == 200) {
        final scanQrCodeResponseModel = scanQrCodeResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (scanQrCodeResponseModel.status == "success") {
          scanUserType = scanQrCodeResponseModel.data?.userType ?? "";
          existUserModel = scanQrCodeResponseModel.data?.userData;
          update();
          return scanQrCodeResponseModel;
        } else {
          CustomSnackBar.error(
            errorList: scanQrCodeResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isScanningQrCodeLoading = false;
      update();
    }
    return ScanQrCodeResponseModel();
  }

  //Qr code login
  bool isQrCodeLoginLoading = false;
  Future<bool> qrCodeLogin({String inputText = ""}) async {
    try {
      isQrCodeLoginLoading = true;
      update();

      ResponseModel responseModel = await profileRepo.qrCodeLogin(
        code: inputText,
      );
      if (responseModel.statusCode == 200) {
        final scanQrCodeResponseModel = scanQrCodeResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (scanQrCodeResponseModel.status == "success") {
          CustomSnackBar.success(
            successList: scanQrCodeResponseModel.message ?? [MyStrings.requestSuccess],
          );
          return true;
        } else {
          CustomSnackBar.error(
            errorList: scanQrCodeResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isQrCodeLoginLoading = false;
      update();
    }
    return false;
  }
}
