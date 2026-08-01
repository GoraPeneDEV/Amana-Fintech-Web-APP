import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/data/controller/localization/localization_controller.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:ovopay/environment.dart';
import 'package:toastification/toastification.dart';
import 'core/data/services/service_exporter.dart';
import 'core/di_service/di_services.dart' as di_service;
import 'package:timezone/data/latest.dart' as tz;

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  //Stop Landscape
  MyUtils().stopLandscape();
  // init shared preference
  await SharedPreferenceService.init();
  // inti fcm services
  await PushNotificationService().setupInteractedMessage();
  //Api inits
  ApiService.init();
  //Dependency injection
  await di_service.initDependency();
  HttpOverrides.global = MyHttpOverrides();
  tz.initializeTimeZones();
  runApp(OvoApp(languages: {}));
}

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)..badCertificateCallback = (X509Certificate cert, String host, int port) => true;
  }
}

//APP ENTRY POINT
class OvoApp extends StatefulWidget {
  final Map<String, Map<String, String>> languages;
  const OvoApp({super.key, required this.languages});

  @override
  State<OvoApp> createState() => _OvoAppState();
}

class _OvoAppState extends State<OvoApp> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((t) {
      Future.wait([precacheImage(AssetImage(MyImages.balanceCardBgImage), context)]);
    });
  }

  @override
  Widget build(BuildContext context) {
    return OrientationBuilder(
      builder: (context, orientation) {
        final size = MediaQuery.of(context).size;
        Size designSize;
        if (size.width > 900) {
          // **Tablets or Large Screens**
          designSize = orientation == Orientation.landscape ? const Size(1400, 900) : const Size(900, 1400);
        } else if (size.width > 600) {
          // **Medium-sized tablets**
          designSize = orientation == Orientation.landscape ? const Size(1200, 800) : const Size(800, 1200);
        } else if (size.width > 430) {
          // **Big Phones like Galaxy S24 Ultra, iPhone 15 Pro Max**
          designSize = orientation == Orientation.landscape
              ? const Size(1000, 450) // Adjust for landscape
              : const Size(450, 1000); // Adjust for portrait
        } else {
          // **Default mobile phones**
          designSize = orientation == Orientation.landscape ? const Size(812, 375) : const Size(375, 812);
        }
        // printE("$designSize -- ${size.width}");

        return ScreenUtilInit(
          // todo add your (Xd / Figma) artboard size
          designSize: designSize,
          minTextAdapt: true,
          splitScreenMode: true,
          useInheritedMediaQuery: true,
          rebuildFactor: (old, data) => true,
          builder: (context, w) {
            return ToastificationWrapper(
              child: GetMaterialApp(
                title: Environment.appName,
                debugShowCheckedModeBanner: false,
                defaultTransition: Transition.noTransition,
                transitionDuration: const Duration(milliseconds: 200),
                initialRoute: RouteHelper.splashScreen,
                themeMode: ThemeMode.light,
                navigatorKey: Get.key,
                getPages: RouteHelper.routes,
                locale: LocalizationController().locale,
                translations: LanguageMessages(languages: widget.languages),
                fallbackLocale: Locale(LocalizationController().locale.languageCode, LocalizationController().locale.countryCode),
                useInheritedMediaQuery: true,
                builder: (context, widget) {
                  return Theme(
                    data: ThemeData.light().copyWith(scaffoldBackgroundColor: MyColor.getScreenBgColor()),
                    child: MediaQuery(
                      data: MediaQuery.of(context).copyWith(textScaler: TextScaler.linear(1.0)),
                      child: widget!,
                    ),
                  );
                },
              ),
            );
          },
        );
      },
    );
  }
}
