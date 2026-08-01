import 'dart:convert';

import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/controller/localization/localization_controller.dart';
import 'package:ovopay/core/data/models/country_model/country_model.dart';
import 'package:ovopay/core/data/models/general_setting/general_setting_response_model.dart';
import 'package:ovopay/core/data/models/global/module/app_module_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/repositories/auth/general_setting_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/app_status.dart';
import 'package:ovopay/core/utils/messages.dart';
import 'package:ovopay/core/utils/my_strings.dart';
import 'package:ovopay/environment.dart';

import '../../../../core/data/services/service_exporter.dart';

class SplashController extends GetxController {
  GeneralSettingRepo repo;

  SplashController({required this.repo});
  LocalizationController localizationController = LocalizationController();
  bool isLoading = true;
  bool isMaintenance = false;
  Future<void> gotoNextPage() async {
    bool isRemember = SharedPreferenceService.getRememberMe();
    bool isLoggedIn = SharedPreferenceService.getIsLoggedIn();
    String accessToken = SharedPreferenceService.getAccessToken();
    bool isOnBoard = SharedPreferenceService.getBool(
      SharedPreferenceService.onboardKey,
      defaultValue: true,
    );
    update();
    if (isLoggedIn) {
      PushNotificationService().sendUserToken();
    }

    await loadLanguage();
    await storeLangDataInLocalStorage();
    await loadCountryDataAndSaveToLocalStorage();
    await loadModuleDataAndSaveToLocalStorage();
    await loadAndSaveGeneralSettingsData(isRemember, isOnBoard, accessToken);
  }

  Future loadAndSaveGeneralSettingsData(
    bool isRemember,
    bool isOnBoard,
    String accessToken,
  ) async {
    ResponseModel response = await repo.getGeneralSetting();

    if (response.statusCode == 200) {
      GeneralSettingResponseModel model = GeneralSettingResponseModel.fromJson(
        response.responseJson,
      );
      if (model.status?.toLowerCase() == AppStatus.SUCCESS) {
        await SharedPreferenceService.setGeneralSettingData(model);
      } else {
        List<String> message = [MyStrings.somethingWentWrong];
        CustomSnackBar.error(errorList: model.message ?? message);
      }
    }

    isLoading = false;
    update();
    if (isMaintenance == false) {
      // Navigate based on onboarding and remember status
      Future.delayed(const Duration(seconds: 1), () {
        if (isOnBoard) {
          Get.offAllNamed(RouteHelper.onboardScreen);
        } else {
          if (Environment.DEV_MODE == true) {
            //DEV LOGIC
            if (SharedPreferenceService.getIsLoggedIn()) {
              Get.offAllNamed(RouteHelper.dashboardScreen);
            } else {
              Get.offAllNamed(RouteHelper.loginScreen);
            }
          } else {
            Get.offAllNamed(RouteHelper.loginScreen);
          }
        }
      });
    }
  }

  Future storeLangDataInLocalStorage() async {
    if (!SharedPreferenceService.containsKey(
      SharedPreferenceService.countryCode,
    )) {
      return SharedPreferenceService.setString(
        SharedPreferenceService.countryCode,
        LocalizationController.myLanguages[0].countryCode,
      );
    }
    if (!SharedPreferenceService.containsKey(
      SharedPreferenceService.languageCode,
    )) {
      return SharedPreferenceService.setString(
        SharedPreferenceService.languageCode,
        LocalizationController.myLanguages[0].languageCode,
      );
    }
    return Future.value(true);
  }

  Future<void> loadLanguage() async {
    localizationController.loadCurrentLanguage();
    String languageCode = localizationController.locale.languageCode;

    ResponseModel response = await repo.getLanguage(languageCode);
    if (response.statusCode == 200) {
      try {
        Map<String, Map<String, String>> language = {};

        var resJson = (response.responseJson);
        if (resJson['remark'] == 'maintenance_mode') {
          isMaintenance = true;
          return;
        }
        saveLanguageList(jsonEncode(resJson));
        Map value = resJson['data']['file'].toString() == '[]' ? {} : resJson['data']['file'];
        Map<String, String> json = {};
        value.forEach((key, value) {
          json[key] = value.toString();
        });
        language['${localizationController.locale.languageCode}_${localizationController.locale.countryCode}'] = json;
        Get.addTranslations(LanguageMessages(languages: language).keys);
      } catch (e) {
        CustomSnackBar.error(errorList: [e.toString()]);
      }
    } else {
      CustomSnackBar.error(errorList: [response.message]);
    }
  }

  void saveLanguageList(String languageJson) async {
    await SharedPreferenceService.setString(
      SharedPreferenceService.languageListKey,
      languageJson,
    );
    return;
  }

  Future loadCountryDataAndSaveToLocalStorage() async {
    try {
      ResponseModel response = await repo.getCountryList();
      if (response.statusCode == 200) {
        CountryModel countryModel = CountryModel.fromJson(
          response.responseJson,
        );

        await SharedPreferenceService.setCountryJsonDataData(countryModel);
      }
    } catch (e) {
      CustomSnackBar.error(errorList: [e.toString()]);
    }
  }

  Future loadModuleDataAndSaveToLocalStorage() async {
    try {
      ResponseModel response = await repo.getModuleList();
      if (response.statusCode == 200) {
        final appModuleResponseModel = appModuleResponseModelFromJson(
          jsonEncode(response.responseJson),
        );

        await SharedPreferenceService.setModuleJsonDataData(
          appModuleResponseModel,
        );
      }
    } catch (e) {
      CustomSnackBar.error(errorList: [e.toString()]);
    }
  }
}
