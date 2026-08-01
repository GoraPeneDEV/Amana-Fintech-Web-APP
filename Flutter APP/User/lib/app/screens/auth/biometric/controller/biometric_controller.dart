import 'dart:async';
import 'package:local_auth/local_auth.dart';
import 'package:flutter/widgets.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/profile/profile_response_model.dart';
import 'package:ovopay/core/data/repositories/biometric/biometric_repo.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import '../../../../../core/utils/util_exporter.dart';

class BioMetricController extends GetxController {
  final LocalAuthentication _auth = LocalAuthentication();

  BiometricRepo biometricRepo = BiometricRepo();
  bool isLoading = false;
  TextEditingController passwordController = TextEditingController();

  var isDeviceSupportBiometric = false;
  var isBiometricEnabled = false;

  var availableBiometrics = <BiometricType>[];
  var hasFaceID = false;
  var hasFingerprint = false;

  Future<void> loadBiometricPreference() async {
    isBiometricEnabled = SharedPreferenceService.getBioMetricStatus();
    update();
  }

  Future<void> checkAvailableBiometrics() async {
    try {
      isDeviceSupportBiometric = await _auth.isDeviceSupported();
      if (isDeviceSupportBiometric) {
        final biometrics = await _auth.getAvailableBiometrics();
        availableBiometrics = biometrics;
        update();
        // Check if Face ID or Fingerprint is available
        hasFaceID = biometrics.contains(BiometricType.face);
        hasFingerprint = biometrics.contains(BiometricType.fingerprint);
        update();
      }
    } catch (e) {
      Get.snackbar("Error", "Failed to get biometric availability: $e");
    }
  }

  Future<void> toggleBiometric(bool enable) async {
    if (enable) {
      bool isAuthenticated = await _authenticateWithBiometrics();
      if (isAuthenticated) {
        await SharedPreferenceService.setBioMetricStatus(true);
        isBiometricEnabled = true;
        update();
      }
    } else {
      await SharedPreferenceService.setBioMetricStatus(false);
      isBiometricEnabled = false;
      update();
    }
  }

  Future<void> enableBiometric({required Function() onSuccess}) async {
    bool isAuthenticated = await _authenticateWithBiometrics();
    printX(isAuthenticated);
    if (isAuthenticated) {
      await setBioMetric(
        onSuccess: () async {
          await SharedPreferenceService.setBioMetricStatus(true);
          isShowBioMetricAccountPinBox = false;
          isBiometricEnabled = true;
          onSuccess();
        },
        onDisableSuccess: () {},
      );

      update();
    }
  }

  Future<void> disableBiometric({required Function() onSuccess}) async {
    bool isAuthenticated = await _authenticateWithBiometrics();
    printX(isAuthenticated);
    if (isAuthenticated) {
      await setBioMetric(
        onSuccess: () async {},
        onDisableSuccess: () async {
          await SharedPreferenceService.setBioMetricStatus(false);
          isBiometricEnabled = false;
          isShowBioMetricAccountPinBox = false;
          onSuccess();
        },
      );
      update();
    }
  }

  Future<void> checkBiometric({
    required Function() onSuccess,
    bool fromLogin = false,
  }) async {
    bool isAuthenticated = await _authenticateWithBiometrics(
      fromLogin: fromLogin,
    );
    printW(isAuthenticated);
    if (isAuthenticated) {
      onSuccess();
    }
  }

  Future<bool> _authenticateWithBiometrics({bool fromLogin = false}) async {
    try {
      return await _auth.authenticate(
        localizedReason: fromLogin ? 'Please provide your device pin to login' : 'Please authenticate to enable biometrics',
        biometricOnly: true,
        persistAcrossBackgrounding: true,
      );
    } on LocalAuthException catch (e) {
      if (e.code == LocalAuthExceptionCode.noBiometricHardware) {
        CustomSnackBar.error(errorList: ["Biometrics is not available"]);
        return false;
      } else if (e.code == LocalAuthExceptionCode.noBiometricsEnrolled) {
        CustomSnackBar.error(errorList: ["Biometrics is not enrolled"]);
        return false;
      } else {
        return false;
      }
    } catch (e) {
      printE('Authentication error: $e');
      return false;
    }
  }

  //Delete account

  TextEditingController pinCodeController = TextEditingController();
  bool isShowBioMetricAccountPinBox = false;
  bool isPinValidateLoading = false;

  void toggleIsShowAccountPinBox() {
    isShowBioMetricAccountPinBox = !isShowBioMetricAccountPinBox;
    update();
  }

  Future<void> setBioMetric({
    required Function() onSuccess,
    required Function() onDisableSuccess,
  }) async {
    try {
      isPinValidateLoading = true;
      update();

      // Step 1: Validate PIN
      final pinResponse = await biometricRepo.checkPinOfAccount(
        pin: pinCodeController.text,
      );
      if (pinResponse.statusCode != 200) {
        _handleError([pinResponse.message]);
        return;
      }

      final pinCheckResponse = ProfileResponseModel.fromJson(
        pinResponse.responseJson,
      );
      if (pinCheckResponse.status?.toLowerCase() != AppStatus.SUCCESS.toLowerCase()) {
        _handleError(pinCheckResponse.message);
        return;
      }
      pinCodeController.clear();

      // Step 2: Set bio
      onSuccess();
      onDisableSuccess();
    } catch (e) {
      CustomSnackBar.error(errorList: [MyStrings.requestFail]);
    } finally {
      isPinValidateLoading = false;
      update();
    }
  }

  void _handleError(List<String>? errorMessage) {
    CustomSnackBar.error(errorList: errorMessage ?? [MyStrings.requestFail]);
    isPinValidateLoading = false;
    update();
  }
}
