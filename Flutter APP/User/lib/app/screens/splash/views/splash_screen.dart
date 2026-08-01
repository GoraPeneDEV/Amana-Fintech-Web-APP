import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/annotated_region/annotated_region_widget.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/screens/splash/controller/splash_controller.dart';
import 'package:ovopay/core/data/controller/localization/localization_controller.dart';
import 'package:ovopay/core/data/repositories/auth/general_setting_repo.dart';

import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    Get.put(GeneralSettingRepo());
    Get.put(LocalizationController());
    final controller = Get.put(SplashController(repo: Get.find()));

    super.initState();
    MyUtils.splashScreen();
    // Set up the animation controller and animation
    _controller = AnimationController(
      duration: const Duration(seconds: 2),
      vsync: this,
    );

    // Scale animation
    _animation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeInOut));

    // Start the animation
    _controller.forward();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) async {
      await SharedPreferenceService.init();
      controller.gotoNextPage();
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegionWidget(
      statusBarBrightness: Brightness.light,
      statusBarIconBrightness: Brightness.dark,
      statusBarColor: MyColor.getPrimaryColor(),
      child: Scaffold(
        backgroundColor: MyColor.getWhiteColor(),
        body: Center(
          child: AnimatedBuilder(
            animation: _controller,
            builder: (context, child) {
              return Transform.scale(
                scale: _animation.value,
                child: SizedBox(
                  height: context.width * 0.4,
                  width: context.width * 0.4,
                  child: MyAssetImageWidget(
                    assetPath: MyImages.appLogo,
                    boxFit: BoxFit.contain,
                  ), // Place your logo image here
                ),
              );
            },
          ),
        ),
      ),
    );
  }
}
