import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/controller/profile_controller.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/controller/two_factor_controller.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/sections/two_factor_disable_section.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/sections/two_factor_enable_section.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import 'package:ovopay/core/data/repositories/auth/two_factor_repo.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/utils/my_strings.dart';
import '../../../../core/utils/util_exporter.dart';

class TwoFactorSetupScreen extends StatefulWidget {
  const TwoFactorSetupScreen({super.key});

  @override
  State<TwoFactorSetupScreen> createState() => _TwoFactorSetupScreenState();
}

class _TwoFactorSetupScreenState extends State<TwoFactorSetupScreen> {
  @override
  void initState() {
    Get.put(TwoFactorRepo());
    final controller = Get.put(TwoFactorController(repo: Get.find()));
    Get.put(ProfileRepo());
    final pcontroller = Get.put(ProfileController(profileRepo: Get.find()));
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      pcontroller.loadProfileInfo();
      controller.get2FaCode();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TwoFactorController>(
      builder: (controller) {
        return GetBuilder<ProfileController>(
          builder: (profileController) {
            return MyCustomScaffold(
              pageTitle: MyStrings.twoFactorAuth,
              body: profileController.user2faIsOne == false
                  ? Skeletonizer(
                      enabled: controller.isLoading || profileController.isLoading,
                      child: const TwoFactorEnableSection(),
                    )
                  : const TwoFactorDisableSection(),
            );
          },
        );
      },
    );
  }
}
