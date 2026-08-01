import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/screens/global/controller/country_controller.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/country_model/country_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/repositories/auth/login_repo.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../components/snack_bar/show_custom_snackbar.dart';

class ForgetPinController extends GetxController {
  LoginRepo loginRepo;

  ForgetPinController({required this.loginRepo});

  CountryData? countryData;
  TextEditingController countryController = TextEditingController();
  TextEditingController mobileController = TextEditingController();
  TextEditingController otpController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  FocusNode pinFocusNode = FocusNode();
  TextEditingController cPinController = TextEditingController();
  FocusNode cPinFocusNode = FocusNode();
  bool submitLoading = false;
  bool isLoading = true;
  bool resendLoading = false;

  void onChangeOtpField(String v) {
    otpController.text = v;
    update();
  }

  @override
  void onInit() {
    initializeData();

    super.onInit();
  }

  CountryController countryDataController = CountryController();
  void initializeData() {
    mobileController.text = SharedPreferenceService.getUserPhoneNumber();
    //Country data
    countryDataController.initialize();
    if (countryDataController.filteredCountries.isNotEmpty) {
      countryData = countryDataController.selectedCountryData ?? countryDataController.filteredCountries.first;
      countryController.text = countryData?.name ?? "";
      SharedPreferenceService.setSelectedOperatingCountry(
        countryData ?? CountryData(),
      );
    }
    update();
  }

  void selectedCountryData(CountryData value) {
    countryData = value;
    countryController.text = value.name ?? "";
    SharedPreferenceService.setSelectedOperatingCountry(value);
    update();
  }

  //Forgot password
  Future verifyYourMobileNo({
    required void Function() onSuccess,
    bool forceLoad = true,
  }) async {
    submitLoading = forceLoad;
    update();
    try {
      ResponseModel responseModel = await loginRepo.forgetPassword(
        (countryData?.id ?? -1).toString(),
        mobileController.text,
      );

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );
        update();

        if (model.status == AppStatus.SUCCESS) {
          CustomSnackBar.success(
            successList: model.message ?? [(MyStrings.requestSuccess)],
          );
          onSuccess();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [(MyStrings.userNotFound)],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      submitLoading = false;
      update();
    }
  }

  //Forgot password verify code
  Future verifyYourMobileNoAndCode({required void Function() onSuccess}) async {
    submitLoading = true;
    update();
    try {
      ResponseModel responseModel = await loginRepo.verifyForgetPassCode(
        otpController.text,
        mobileController.text,
      );

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );
        update();

        if (model.status == AppStatus.SUCCESS) {
          // CustomSnackBar.success(successList: model.message ?? [(MyStrings.requestSuccess)]);
          onSuccess();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [(MyStrings.userNotFound)],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      submitLoading = false;
      update();
    }
  }

  Future<void> sendCodeAgain() async {
    resendLoading = true;
    update();

    await verifyYourMobileNo(onSuccess: () {}, forceLoad: false);
    otpController.text = "";
    resendLoading = false;
    update();
  }
  //Reset password

  Future resetNewPin({required void Function() onSuccess}) async {
    submitLoading = true;
    update();
    try {
      ResponseModel responseModel = await loginRepo.resetPin(
        mobileController.text,
        pinController.text,
        cPinController.text,
        otpController.text,
      );

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );
        update();

        if (model.status == AppStatus.SUCCESS) {
          CustomSnackBar.success(
            successList: model.message ?? [(MyStrings.requestSuccess)],
          );

          onSuccess();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [(MyStrings.userNotFound)],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      submitLoading = false;
      update();
    }
  }
}
