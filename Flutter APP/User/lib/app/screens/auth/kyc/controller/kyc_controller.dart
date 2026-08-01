// ignore_for_file: use_build_context_synchronously

import 'dart:io';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';
import 'package:ovopay/core/data/repositories/kyc/kyc_repo.dart';
import 'package:ovopay/environment.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../../core/utils/util_exporter.dart';

class KycController extends GetxController {
  KycRepo repo;
  KycController({required this.repo});
  File? imageFile;

  bool isLoading = true;
  List<KycFormModel> formList = [];
  String selectOne = MyStrings.selectOne;

  KycResponseModel model = KycResponseModel();
  bool isNoDataFound = false;
  bool isAlreadyVerified = false;
  bool isAlreadyPending = false;
  List<KycPendingData> pendingData = [];
  String path = "";
  Future<void> beforeInitLoadKycData() async {
    setStatusTrue();

    try {
      model = await repo.getKycData();

      if (model.data != null && model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
        List<KycFormModel> tList = model.data?.form?.list ?? [];

        if (tList.isNotEmpty) {
          formList.clear();
          for (var element in tList) {
            if (element.type == 'select') {
              bool? isEmpty = element.options?.isEmpty;
              bool empty = isEmpty ?? true;
              if (element.options != null && empty != true) {
                // element.options?.insert(0, selectOne);
                // element.selectedValue = element.options?.first;
                formList.add(element);
              }
            } else {
              formList.add(element);
            }
          }
        }

        isNoDataFound = false;
      } else {
        if (model.remark?.toLowerCase() == 'already_verified') {
          isAlreadyVerified = true;
          CustomSnackBar.success(
            successList: model.message ?? [MyStrings.kycAlreadyVerifiedMsg],
          );
          // Get.offAllNamed(RouteHelper.kycScreen);
        } else if (model.remark?.toLowerCase() == 'under_review') {
          isAlreadyPending = true;
          pendingData = model.data?.kycPendingData ?? [];
          path = model.data?.path ?? "";
        } else {
          isNoDataFound = true;
        }
      }
    } finally {
      setStatusFalse();
    }
    setStatusFalse();
  }

  void setStatusTrue() {
    isLoading = true;
    update();
  }

  void setStatusFalse() {
    isLoading = false;
    update();
  }

  bool submitLoading = false;
  Future<void> submitKycData() async {
    List<String> list = hasError();

    if (list.isNotEmpty) {
      CustomSnackBar.error(errorList: list);
      return;
    }

    submitLoading = true;
    update();

    AuthorizationResponseModel response = await repo.submitKycData(formList);

    if (response.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
      isAlreadyPending = true;
      Get.find<KycController>().beforeInitLoadKycData();
      CustomSnackBar.success(
        successList: response.message ?? [MyStrings.requestSuccess.tr],
      );
    } else {
      CustomSnackBar.error(
        errorList: response.message ?? [MyStrings.requestFail.tr],
      );
    }

    submitLoading = false;
    update();
  }

  List<String> hasError() {
    List<String> errorList = [];
    errorList.clear();

    for (var element in formList) {
      printW("----${element.name}--${element.type} ${element.cbSelected}");
      if (element.isRequired == 'required') {
        if (element.type == 'checkbox') {
          if (element.cbSelected?.isEmpty ?? false) {
            errorList.add('${element.name} ${MyStrings.isRequired.tr}');
          }
        } else if (element.type == 'file') {
          if (element.imageFile == null) {
            errorList.add('${element.name} ${MyStrings.isRequired.tr}');
          }
        } else {
          if (element.selectedValue == '') {
            errorList.add('${element.name} ${MyStrings.isRequired.tr}');
          }
        }
      }
    }
    return errorList;
  }

  void changeSelectedValue(dynamic value, int index) {
    formList[index].selectedValue = value;
    update();
  }

  void changeSelectedDateTimeValue(int index, BuildContext context) async {
    DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(1900),
      lastDate: DateTime(2101),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: ColorScheme.light(
              primary: MyColor.getPrimaryColor(), // Selection color
            ),
          ),
          child: child!,
        );
      },
    );
    TimeOfDay? pickedTime = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.now(),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: ColorScheme.light(
              primary: MyColor.getPrimaryColor(), // Selection color
            ),
          ),
          child: child!,
        );
      },
    );

    if (pickedDate != null && pickedTime != null) {
      final DateTime selectedDateTime = DateTime(
        pickedDate.year,
        pickedDate.month,
        pickedDate.day,
        pickedTime.hour,
        pickedTime.minute,
      );

      formList[index].selectedValue = DateConverter.localDateTime(
        selectedDateTime,
      );
      // formList[index].selectedValue = selectedDateTime.toIso8601String();
      formList[index].textEditingController?.text = DateConverter.localDateTime(selectedDateTime);
      printX(formList[index].textEditingController?.text);
      printX(formList[index].selectedValue);
      update();
    }

    update();
  }

  void changeSelectedDateOnlyValue(int index, BuildContext context) async {
    DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(1900),
      lastDate: DateTime(2101),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: ColorScheme.light(
              primary: MyColor.getPrimaryColor(), // Selection color
            ),
          ),
          child: child!,
        );
      },
    );
    if (pickedDate != null) {
      final DateTime selectedDateTime = DateTime(
        pickedDate.year,
        pickedDate.month,
        pickedDate.day,
      );

      formList[index].selectedValue = DateConverter.localDate(
        selectedDateTime,
      );
      formList[index].textEditingController?.text = DateConverter.localDate(
        selectedDateTime,
      );
      printX(formList[index].textEditingController?.text);
      printX(formList[index].selectedValue);
      update();
    }
    update();
  }

  void changeSelectedTimeOnlyValue(int index, BuildContext context) async {
    TimeOfDay? pickedTime = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.now(),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: ColorScheme.light(
              primary: MyColor.getPrimaryColor(), // Selection color
            ),
          ),
          child: child!,
        );
      },
    );
    if (pickedTime != null) {
      final DateTime selectedDateTime = DateTime(
        DateTime.now().year,
        DateTime.now().month,
        DateTime.now().day,
        pickedTime.hour,
        pickedTime.minute,
      );

      formList[index].selectedValue = DateConverter.localTime(
        selectedDateTime,
      );
      formList[index].textEditingController?.text = DateConverter.localTime(
        selectedDateTime,
      );
      printX(formList[index].textEditingController?.text);
      printX(formList[index].selectedValue);
      update();
    }

    update();
  }

  //End DATE TIME
  void changeSelectedRadioBtnValue(int listIndex, int selectedIndex) {
    formList[listIndex].selectedValue = formList[listIndex].options?[selectedIndex];
    update();
  }

  void changeSelectedCheckBoxValue(int listIndex, String values) {
    List<String> list = values.split('_');
    int index = int.parse(list[0]);
    bool status = list[1] == 'true' ? true : false;

    List<String> selectedValue = formList[listIndex].cbSelected ?? [];

    String value = formList[listIndex].options?[index] ?? "";
    if (status) {
      if (!selectedValue.contains(value)) {
        selectedValue.add(value);
        formList[listIndex].cbSelected = selectedValue;
        update();
      }
    } else {
      if (selectedValue.contains(value)) {
        selectedValue.removeWhere((element) => element == value);
        formList[listIndex].cbSelected = selectedValue;
        update();
      }
    }
  }

  void pickFile(int index, {List<String>? extention}) async {
    FilePickerResult? result = await FilePicker.platform.pickFiles(
      allowMultiple: false,
      type: FileType.custom,
      allowedExtensions: extention ??
          [
            'jpg',
            'png',
            'jpeg',
            'pdf',
            'doc',
            'docx',
            'csv',
            'txt',
            'docx',
            'xls',
            'xlsx',
          ],
    );

    if (result == null) return;

    formList[index].imageFile = File(result.files.single.path!);
    String fileName = result.files.single.name;
    formList[index].selectedValue = fileName;
    update();
    return;
  }

  bool isImageFile({required List<String> extensions}) {
    // List of non-image file extensions
    const nonImageExtensions = [
      'pdf',
      'doc',
      'docx',
      'csv',
      'txt',
      'xls',
      'xlsx',
    ];

    for (var ext in extensions) {
      if (nonImageExtensions.contains(ext.toLowerCase())) {
        return false; // Return false if any non-image extension is found
      }
    }
    return true; // Return true if no non-image extensions are found
  }

  bool isSubmitLoading = false;
  int selectedDownloadIndex = -1;
  Future<void> downloadAttachment(
    String url,
    int index,
    String extension,
  ) async {
    // Update UI to indicate loading state
    selectedDownloadIndex = index;
    isSubmitLoading = true;
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
            ResponseModel responseModel = await ApiService.downloadFile(
              url: url,
              savePath: downloadPath,
            );
            await MyUtils().openFile(downloadPath, extension);
            CustomSnackBar.success(successList: [responseModel.message]);
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
    selectedDownloadIndex = -1;
    isSubmitLoading = false;
    update();
  }
}
