import 'package:get/get.dart';
import 'package:ovopay/app/screens/splash/controller/splash_controller.dart';
import 'package:ovopay/core/data/controller/contact/contact_controller.dart';
import 'package:ovopay/core/data/controller/localization/localization_controller.dart';

Future<void> initDependency() async {
  Get.lazyPut(() => LocalizationController());
  Get.lazyPut(() => SplashController(repo: Get.find()));
  Get.put(ContactController(), permanent: true);
}
