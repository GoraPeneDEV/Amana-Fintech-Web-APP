import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/core/data/controller/localization/localization_controller.dart';

import '../../../../core/data/models/global/response_model/response_model.dart';
import '../../../../core/data/models/language/language_model.dart';
import '../../../../core/data/models/language/main_language_response_model.dart';
import '../../../../core/data/repositories/auth/general_setting_repo.dart';
import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';
import '../../../components/snack_bar/show_custom_snackbar.dart';

class MyLanguageController extends GetxController {
  GeneralSettingRepo repo;
  MyLanguageController({required this.repo});
  LocalizationController localizationController = LocalizationController();
  bool isLoading = true;
  String languageImagePath = "";
  List<MyLanguageModel> langList = [];

  void loadLanguage() {
    langList.clear();
    isLoading = true;

    String languageString = SharedPreferenceService.getString(
      SharedPreferenceService.languageListKey,
    );

    var language = jsonDecode(languageString);
    MainLanguageResponseModel model = MainLanguageResponseModel.fromJson(
      language,
    );
    languageImagePath = "${UrlContainer.domainUrl}/${model.data?.imagePath ?? ''}";
    if (model.data?.languages != null && model.data!.languages!.isNotEmpty) {
      for (var listItem in model.data!.languages!) {
        MyLanguageModel model = MyLanguageModel(
          languageCode: listItem.code ?? '',
          countryCode: listItem.name ?? '',
          languageName: listItem.name ?? '',
          imageUrl: listItem.image ?? '',
        );
        langList.add(model);
      }
    }

    String languageCode = SharedPreferenceService.getString(
      SharedPreferenceService.languageCode,
      defaultValue: 'en',
    );

    if (kDebugMode) {
      printE('current lang code: $languageCode');
    }

    if (langList.isNotEmpty) {
      int index = langList.indexWhere(
        (element) => element.languageCode.toLowerCase() == languageCode.toLowerCase(),
      );

      changeSelectedIndex(index);
    }

    isLoading = false;
    update();
  }

  String selectedLangCode = 'en';

  bool isChangeLangLoading = false;
  int isChangeLangLoadingIndex = -1;
  void changeLanguage(int index) async {
    isChangeLangLoading = true;
    isChangeLangLoadingIndex = index;
    update();

    MyLanguageModel selectedLangModel = langList[index];
    String languageCode = selectedLangModel.languageCode;
    try {
      ResponseModel response = await repo.getLanguage(languageCode);

      if (response.statusCode == 200) {
        await SharedPreferenceService.setString(
          SharedPreferenceService.languageListKey,
          jsonEncode(response.responseJson),
        );

        Locale local = Locale(selectedLangModel.languageCode, 'US');
        localizationController.setLanguage(
          local,
          "$languageImagePath/${langList[index].imageUrl}",
        );
        localizationController.loadCurrentLanguage();
        var resJson = (response.responseJson);
        Map<String, dynamic> value = resJson['data']['file'].toString() == '[]' ? {} : resJson['data']['file'];
        Map<String, String> json = {};
        value.forEach((key, value) {
          json[key] = value.toString();
        });

        Map<String, Map<String, String>> language = {};
        language['${selectedLangModel.languageCode}_${'US'}'] = json;

        Get.clearTranslations();
        Get.addTranslations(LanguageMessages(languages: language).keys);
        update();
        Get.back();
      } else {
        CustomSnackBar.error(errorList: [response.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isChangeLangLoading = false;
      isChangeLangLoadingIndex = -1;
      update();
    }
  }

  int selectedIndex = 0;
  void changeSelectedIndex(int index) {
    selectedIndex = index;
    update();
  }
}
