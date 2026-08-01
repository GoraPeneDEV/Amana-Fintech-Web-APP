import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/screens/add_money/controller/add_money_controller.dart';
import 'package:ovopay/app/screens/add_money/views/widgets/add_money_amount_page.dart';
import 'package:ovopay/core/data/repositories/add_money/add_money_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class AddMoneyScreen extends StatefulWidget {
  const AddMoneyScreen({super.key});

  @override
  State<AddMoneyScreen> createState() => _AddMoneyScreenState();
}

class _AddMoneyScreenState extends State<AddMoneyScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    super.initState();
    Get.put(AddMoneyRepo());
    final controller = Get.put(AddMoneyController(depositRepo: Get.find()));
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);

    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.getDepositMethod();
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
      pageTitle: MyStrings.addMoney,
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
              Get.toNamed(RouteHelper.addMoneyHistoryScreen);
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
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: [
          _buildPaymentGatewayPage(),
          AddMoneyAmountPage(
            context: context,
            onPaymentGatewayClick: () {
              _previousPage(goToPage: 0);
            },
          ),
        ],
      ),
    );
  }

  //Payment gateway Widget
  Widget _buildPaymentGatewayPage() {
    return GetBuilder<AddMoneyController>(
      builder: (controller) {
        return Skeletonizer(
          enabled: controller.isLoading,
          child: controller.isLoading
              ? ListView.builder(
                  itemCount: 10,
                  itemBuilder: (context, index) {
                    return CustomAppCard(
                      margin: EdgeInsetsDirectional.symmetric(
                        vertical: Dimensions.space4.h,
                      ),
                      radius: Dimensions.largeRadius.r,
                      child: CustomListTileCard(
                        imagePath: "",
                        title: "-----------------",
                        showBorder: false,
                      ),
                      onPressed: () {},
                    );
                  },
                )
              : controller.methodList.isEmpty
                  ? NoDataWidget(text: MyStrings.noDepositMethodToShow.tr)
                  : ListView.builder(
                      itemCount: controller.methodList.length,
                      itemBuilder: (context, index) {
                        var item = controller.methodList[index];
                        return CustomAppCard(
                          margin: EdgeInsetsDirectional.symmetric(
                            vertical: Dimensions.space4.h,
                          ),
                          radius: Dimensions.largeRadius.r,
                          child: CustomListTileCard(
                            imagePath: "${controller.imagePath}/${item.method?.image}",
                            title: "${item.name}",
                            showBorder: false,
                          ),
                          onPressed: () {
                            if (controller.methodList[index].id != -1) {
                              controller.selectedPaymentMethodController.text = controller.methodList[index].name ?? '';
                              controller.setPaymentMethod(
                                controller.methodList[index],
                              );
                              _nextPage(goToPage: 1);
                            }
                          },
                        );
                      },
                    ),
        );
      },
    );
  }
}
