import 'package:get/get.dart';
import 'package:ovopay/core/data/models/faq/faq_model.dart';
import 'package:ovopay/core/data/repositories/faq_repo/faq_repo.dart';

import '../../../../core/data/models/global/response_model/response_model.dart';
import '../../../../core/utils/util_exporter.dart';
import '../../../components/snack_bar/show_custom_snackbar.dart';

class FaqController extends GetxController {
  FaqRepo faqRepo;
  FaqController({required this.faqRepo});

  bool isLoading = true;
  bool isPress = false;
  int selectedIndex = 0;

  List<FaqData> faqList = [];

  void changeSelectedIndex(int index) {
    if (selectedIndex == index) {
      selectedIndex = -1;
      update();
      return;
    }
    selectedIndex = index;
    update();
  }

  void loadData() async {
    ResponseModel model = await faqRepo.loadFaq();
    if (model.statusCode == 200) {
      FaqResponseModel responseModel = FaqResponseModel.fromJson(
        model.responseJson,
      );
      List<FaqData> tempFaqList = responseModel.data?.faqs ?? [];
      if (tempFaqList.isNotEmpty) {
        faqList.addAll(tempFaqList);
      } else {
        CustomSnackBar.error(
          errorList: responseModel.message ?? [MyStrings.somethingWentWrong],
        );
      }
    } else {
      CustomSnackBar.error(errorList: [model.message]);
    }
    isLoading = false;
    update();
  }
}
