import 'dart:io';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/profile/profile_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../core/data/models/profile/profile_post_model.dart';
import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';
import '../../../components/snack_bar/show_custom_snackbar.dart';

class ProfileController extends GetxController {
  ProfileRepo profileRepo;
  ProfileResponseModel model = ProfileResponseModel();

  ProfileController({required this.profileRepo});

  String imageUrl = '';

  bool isLoading = false;
  TextEditingController firstNameController = TextEditingController();
  TextEditingController lastNameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController mobileNoController = TextEditingController();
  TextEditingController addressController = TextEditingController();
  TextEditingController stateController = TextEditingController();
  TextEditingController zipCodeController = TextEditingController();
  TextEditingController cityController = TextEditingController();
  TextEditingController countryController = TextEditingController();

  FocusNode firstNameFocusNode = FocusNode();
  FocusNode lastNameFocusNode = FocusNode();
  FocusNode emailFocusNode = FocusNode();
  FocusNode mobileNoFocusNode = FocusNode();
  FocusNode addressFocusNode = FocusNode();
  FocusNode stateFocusNode = FocusNode();
  FocusNode zipCodeFocusNode = FocusNode();
  FocusNode cityFocusNode = FocusNode();
  FocusNode countryFocusNode = FocusNode();

  File? imageFile;

  Future<void> loadProfileInfo({bool forceLoad = true}) async {
    if (forceLoad) {
      isLoading = true;
      update();
    }

    try {
      ResponseModel responseModel = await profileRepo.loadProfileInfo();
      if (responseModel.statusCode == 200) {
        model = ProfileResponseModel.fromJson(responseModel.responseJson);
        if (model.data != null && model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          loadData(model);
        } else {
          isLoading = false;
          update();
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isLoading = false;
      update();
    }
  }

  bool isSubmitLoading = false;
  Future<void> updateProfile() async {
    isSubmitLoading = true;
    update();

    String firstName = firstNameController.text;
    String lastName = lastNameController.text.toString();
    String address = addressController.text.toString();
    String city = cityController.text.toString();
    String zip = zipCodeController.text.toString();
    String state = stateController.text.toString();

    if (firstName.isNotEmpty && lastName.isNotEmpty) {
      ProfilePostModel model = ProfilePostModel(
        firstname: firstName,
        lastName: lastName,
        address: address,
        state: state,
        zip: zip,
        city: city,
        image: imageFile,
      );

      ResponseModel responseModel = await profileRepo.updateProfile(model);

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel authorizationResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);

        if (authorizationResponseModel.status == "success") {
          CustomSnackBar.success(
            successList: authorizationResponseModel.message ?? [MyStrings.requestSuccess],
          );
        } else {
          CustomSnackBar.error(
            errorList: authorizationResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }

        await loadProfileInfo(forceLoad: false);
      }
    } else {
      if (firstName.isEmpty) {
        CustomSnackBar.error(errorList: [MyStrings.kFirstNameNullError.tr]);
      }
      if (lastName.isEmpty) {
        CustomSnackBar.error(errorList: [MyStrings.kLastNameNullError.tr]);
      }
    }

    isSubmitLoading = false;
    update();
  }

  bool user2faIsOne = false;
  UserModel? userData;
  void loadData(ProfileResponseModel? model) async {
    userData = model?.data?.user;
    firstNameController.text = model?.data?.user?.firstname ?? '';
    lastNameController.text = model?.data?.user?.lastname ?? '';
    emailController.text = model?.data?.user?.email ?? '';
    mobileNoController.text = model?.data?.user?.mobile ?? '';
    addressController.text = model?.data?.user?.address ?? '';
    stateController.text = model?.data?.user?.state ?? '';
    zipCodeController.text = model?.data?.user?.zip ?? '';
    cityController.text = model?.data?.user?.city ?? '';
    countryController.text = model?.data?.user?.countryName ?? '';
    var imageData = model?.data?.user?.image == null ? '' : '${model?.data?.user?.image}';
    user2faIsOne = model?.data?.user?.ts == '1' ? true : false;

    if (imageData.isNotEmpty && imageData != 'null') {
      imageUrl = '${UrlContainer.domainUrl}/assets/images/user/profile/$imageData';
    }
    SharedPreferenceService.setUserName('${model?.data?.user?.username}');
    await SharedPreferenceService.setString(
      SharedPreferenceService.userEmailKey,
      model?.data?.user?.email ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userPhoneNumberKey,
      model?.data?.user?.mobile ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userNameKey,
      model?.data?.user?.username ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userIdKey,
      model?.data?.user?.id.toString() ?? '-1',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userFullNameKey,
      model?.data?.user?.getFullName() ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userImageKey,
      imageUrl,
    );

    isLoading = false;
    update();
  }

  bool isLogOutLoading = false;

  Future<void> logMeOut({required VoidCallback successCallback}) async {
    try {
      isLogOutLoading = true;
      update();

      ResponseModel responseModel = await profileRepo.logout();
      if (responseModel.statusCode == 200) {
        var logoutResponseModel = ProfileResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (logoutResponseModel.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          // CustomSnackBar.success(successList: logoutResponseModel.message ?? [MyStrings.requestSuccess]);
          successCallback();
        } else {
          isLogOutLoading = false;
          update();
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isLoading = false;
      update();
    }
  }

  //Delete account
  TextEditingController pinController = TextEditingController();
  bool isShowDeleteAccountPinBox = false;
  bool isDeleteAccountLoading = false;

  void toggleIsShowDeleteAccountPinBox() {
    isShowDeleteAccountPinBox = !isShowDeleteAccountPinBox;
    update();
  }

  Future<void> deleteAccount() async {
    try {
      isDeleteAccountLoading = true;
      update();

      final deleteResponse = await profileRepo.deleteAccount(
        pin: pinController.text,
      );
      if (deleteResponse.statusCode != 200) {
        _handleError([deleteResponse.message]);
        return;
      }

      final deleteResponseModel = ProfileResponseModel.fromJson(
        deleteResponse.responseJson,
      );
      if (deleteResponseModel.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
        _handleSuccess(deleteResponseModel.message);
      } else {
        _handleError(deleteResponseModel.message);
      }
    } catch (e) {
      printE(e.toString());
      CustomSnackBar.error(errorList: [MyStrings.requestFail]);
    } finally {
      isDeleteAccountLoading = false;
      update();
    }
  }

  // Helper methods
  void _handleSuccess(List<String>? successMessage) {
    CustomSnackBar.success(
      successList: successMessage ?? [MyStrings.requestSuccess],
    );
    SharedPreferenceService.setRememberMe(false);
    SharedPreferenceService.setIsLoggedIn(false);
    Get.offAllNamed(RouteHelper.loginScreen);
  }

  void _handleError(List<String>? errorMessage) {
    CustomSnackBar.error(errorList: errorMessage ?? [MyStrings.requestFail]);
    isDeleteAccountLoading = false;
    update();
    Get.back();
  }
}
