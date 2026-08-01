import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/screens/investment/controller/investment_controller.dart';
import 'package:ovopay/app/screens/investment/widgets/investment_overview_screen.dart';
import 'package:ovopay/app/screens/investment/widgets/investment_amount_page.dart';
import 'package:ovopay/core/data/repositories/investment/investment_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';
import 'widgets/investment_pin_page.dart';

class InvestmentScreen extends StatefulWidget {
  const InvestmentScreen({super.key});

  @override
  State<InvestmentScreen> createState() => _InvestmentScreenState();
}

class _InvestmentScreenState extends State<InvestmentScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  final ScrollController investmentPlanDataScrollController = ScrollController();
  void fetchData() {
    Get.find<InvestmentController>().getInvesmentDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (investmentPlanDataScrollController.position.pixels == investmentPlanDataScrollController.position.maxScrollExtent) {
      if (Get.find<InvestmentController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    Get.put(InvestmentRepo());
    final controller = Get.put(InvestmentController(investmentRepo: Get.find()));

    // Fetch initial data
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        controller.initialPlanData(); // Receiver if index is 0, Sender otherwise

        // Add scroll listeners
        investmentPlanDataScrollController.addListener(() => scrollListener());
      }
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
    investmentPlanDataScrollController.dispose();
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
        pageTitle: MyStrings.investment,
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
                Get.toNamed(RouteHelper.investmentHistoryScreen);
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
        body: PageView(
          controller: _pageController,
          physics: const NeverScrollableScrollPhysics(),
          children: [
            InvestmentOverviewScreen(
              scrollController: investmentPlanDataScrollController,
              onNextTap: () {
                _nextPage();
              },
            ),
            InvestmentAmountPage(
              onSuccessCallback: () {
                _nextPage(goToPage: 2);
              },
              context: context,
            ),
            InvestmentPaymentPinVerificationPage(context: context),
          ],
        ));
  }
}
