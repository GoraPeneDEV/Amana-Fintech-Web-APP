import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/screens/bill_pay_screen/controller/bill_pay_controller.dart';
import 'package:ovopay/app/screens/bill_pay_screen/views/widgets/bill_pay_amount_page.dart';
import 'package:ovopay/app/screens/bill_pay_screen/views/widgets/bill_pay_form_page.dart';
import 'package:ovopay/app/screens/bill_pay_screen/views/widgets/bill_pay_step_1_page.dart';
import 'package:ovopay/core/data/repositories/modules/bill_pay/bill_pay_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';
import 'widgets/bill_pay_pin_page.dart';

class BillPayScreen extends StatefulWidget {
  const BillPayScreen({super.key});

  @override
  State<BillPayScreen> createState() => _BillPayScreenState();
}

class _BillPayScreenState extends State<BillPayScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    Get.put(BillPayRepo());
    final controller = Get.put(BillPayController(billPayRepo: Get.find()));

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
              Get.toNamed(RouteHelper.billPayHistoryScreen);
            },
            width: Dimensions.space40.w,
            height: Dimensions.space40.w,
            padding: EdgeInsetsDirectional.all(Dimensions.space8.w),
            radius: Dimensions.largeRadius.r,
            child: MyAssetImageWidget(
              isSvg: true,
              assetPath: MyIcons.historyInactive,
              width: Dimensions.space24.w,
              height: Dimensions.space24.w,
              color: MyColor.getPrimaryColor(),
            ),
          ),
        spaceSide(Dimensions.space16.w),
      ],
      pageTitle: _currentPage == 4 ? MyStrings.payBill : MyStrings.billPay,
      body: PageView(
        clipBehavior: Clip.none,
        onPageChanged: (value) {
          _currentPage = value;
        },
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: [
          BillPayStep1PageWidget(
            context: context,
            onSuccessCallback: () {
              _nextPage(goToPage: 1);
            },
          ),
          BillPayFormPage(
            context: context,
            onSuccessCallback: () {
              _nextPage(goToPage: 2);
            },
          ),
          BillPayAmountPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 3);
            },
            context: context,
          ),
          BillPayPinVerificationPage(context: context),
        ],
      ),
    );
  }
}
