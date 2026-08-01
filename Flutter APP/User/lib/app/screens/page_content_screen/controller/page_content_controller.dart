import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/pages_content_response_model/pages_response_model.dart';
import 'package:ovopay/core/data/repositories/privacy_repo/privacy_repo.dart';

class PageContentController extends GetxController {
  PrivacyRepo repo;
  PageContentController({required this.repo});

  int selectedIndex = 1;
  bool isLoading = true;

  List<PagesData> list = [];
  late var selectedHtml = '';

  void loadData() async {
    ResponseModel model = await repo.loadAppPagesData();
    if (model.statusCode == 200) {
      PagesResponseModel responseModel = PagesResponseModel.fromJson(
        model.responseJson,
      );
      if (responseModel.data?.policyPages != null && responseModel.data!.policyPages != null && responseModel.data!.policyPages!.isNotEmpty) {
        list.clear();
        list.addAll(responseModel.data!.policyPages!);
        changeIndex(0);
        updateLoading(false);
      }
    } else {
      CustomSnackBar.error(errorList: [model.message]);
      updateLoading(false);
    }
  }

  void changeIndex(int index) {
    selectedIndex = index;
    selectedHtml = list[index].dataValues?.details ?? '';
    update();
  }

  void updateLoading(bool status) {
    isLoading = status;
    update();
  }
}
