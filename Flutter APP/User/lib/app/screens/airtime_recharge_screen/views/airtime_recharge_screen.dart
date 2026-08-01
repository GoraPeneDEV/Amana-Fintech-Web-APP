import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/views/widgets/airtime_recharge_step_1_select_page.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/views/widgets/airtime_recharge_amount_page.dart';
import 'package:ovopay/core/data/repositories/modules/airtime_recharge/airtime_recharge_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';
import '../controller/airtime_recharge_controller.dart';
import 'widgets/airtime_recharge_pin_page.dart';

class AirtimeRechargeScreen extends StatefulWidget {
  const AirtimeRechargeScreen({super.key});

  @override
  State<AirtimeRechargeScreen> createState() => _AirtimeRechargeScreenState();
}

class _AirtimeRechargeScreenState extends State<AirtimeRechargeScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    Get.put(AirtimeRechargeRepo());
    final controller = Get.put(
      AirTimeRechargeController(airtimeRechargeRepo: Get.find()),
    );

    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);

    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.initController();
    });
  }

  void _pageChangeListener() {
    int newPage = _pageController.page?.round() ?? 0;
    if (newPage != _currentPage) {
      setState(() {
        _currentPage = newPage;
      });
    }
  }

  @override
  void dispose() {
    // Remove listener when the widget is disposed to avoid memory leaks
    _pageController.removeListener(_pageChangeListener);
    _pageController.dispose();
    super.dispose();
  }

  void _nextPage({int? goToPage}) {
    setState(() {
      _pageController.animateToPage(
        goToPage ?? ++_currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  void _previousPage({int? goToPage}) {
    setState(() {
      _pageController.animateToPage(
        goToPage ?? --_currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.mobileRecharge,
      onBackButtonTap: () {
        if (_currentPage != 0) {
          _previousPage();
        } else {
          Get.back();
        }
      },
      actionButton: [
        if (_currentPage == 0)
          CustomAppCard(
            onPressed: () {
              Get.toNamed(RouteHelper.airTimeHistoryScreen);
            },
            width: Dimensions.space40.w,
            height: Dimensions.space40.w,
            padding: EdgeInsetsDirectional.all(Dimensions.space8.w),
            radius: Dimensions.largeRadius.r,
            child: MyAssetImageWidget(
              isSvg: true,
              assetPath: MyIcons.historyInactive,
              width: Dimensions.space24.h,
              height: Dimensions.space24.h,
              color: MyColor.getPrimaryColor(),
            ),
          ),
        spaceSide(Dimensions.space16.w),
      ],
      body: PageView(
        clipBehavior: Clip.none,
        onPageChanged: (value) {
          _currentPage = value;
        },
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: [
          AirTimeRechargeStep1SelectPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 1);
            },
          ),
          AirtimeRechargeAmountPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 2);
            },
            context: context,
          ),
          AirTimeRechargePinVerificationPage(context: context),
        ],
      ),
    );
  }
}
