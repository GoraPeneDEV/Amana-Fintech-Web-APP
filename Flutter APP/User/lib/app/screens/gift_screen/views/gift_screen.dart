import 'dart:async';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/shimmer/gift_screen_shimmer.dart';
import 'package:ovopay/app/packages/auto_height_grid_view/auto_height_grid_view.dart';
import 'package:ovopay/app/screens/gift_screen/views/widgets/gift_bottom_sheet.dart';
import 'package:ovopay/app/screens/gift_screen/views/widgets/gift_purchase_page.dart';
import 'package:ovopay/core/route/route.dart';
import '../../../../../core/utils/util_exporter.dart';
import '../../../../core/data/repositories/modules/gift_card/gift_card_repo.dart';
import '../../../components/no_data.dart';
import '../../../components/text-field/rounded_text_field.dart';
import '../controller/gift_controller.dart';
import 'widgets/gift_pin_page.dart';

class GiftScreen extends StatefulWidget {
  const GiftScreen({super.key});

  @override
  State<GiftScreen> createState() => _GiftScreenState();
}

class _GiftScreenState extends State<GiftScreen> {
  final ScrollController scrollController = ScrollController();

  void scrollListener() {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      if (Get.find<GiftController>().hasNext()) {
        Get.find<GiftController>().loadGiftCardInfo(isPagination: true);
      }
    }
  }

  final PageController _pageController = PageController();
  int _currentPage = 0;

  Timer? _debounceTimer;

  @override
  void initState() {
    Get.put(GiftCardRepo());
    final controller = Get.put(
      GiftController(giftCardRepo: Get.find()),
    );

    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);

    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.loadGiftCardInfo();
      scrollController.addListener(scrollListener);
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
      pageTitle: MyStrings.giftCard,
      padding: EdgeInsets.zero,
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
              Get.toNamed(RouteHelper.giftHistoryScreen);
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
          _buildGiftCardsPage(),
          GiftPurchasePage(
            onSuccessCallback: () {
              //it will define which screen will redirect after success
              _nextPage(goToPage: 2);
            },
            context: context,
          ),
          GiftCardPinVerificationPage(context: context),
        ],
      ),
    );
  }

  //Operator Page
  Widget _buildGiftCardsPage() {
    return GetBuilder<GiftController>(
      builder: (controller) {
        if (controller.isLoading) {
          return GiftScreenShimmer();
        }
        return GestureDetector(
          onTap: () {
            MyUtils.clearAllTypeFocusNodes();
          },
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: Dimensions.space16),
            child: Column(
              children: [
                Visibility(
                    visible: controller.isFilterLoading,
                    child: LinearProgressIndicator(
                      color: MyColor.getPrimaryColor(),
                    )),
                spaceDown(Dimensions.space12.h),
                Row(
                  children: [
                    Expanded(
                      child: CustomAppCard(
                          padding: EdgeInsets.symmetric(horizontal: Dimensions.space12, vertical: Dimensions.space16),
                          onPressed: () {
                            GiftBottomSheet.giftCountryBottomSheet(
                              context,
                              selectedCountry: controller.selectedCountry,
                              onSelectedData: (v) {
                                controller.selectedCountryData(v);
                              },
                            );
                          },
                          child: Row(
                            children: [
                              Expanded(
                                  child: Text(
                                controller.selectedCountry?.id == -1 ? MyStrings.allCountries.tr : controller.selectedCountry?.name ?? "",
                                style: MyTextStyle.sectionSubTitle1,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              )),
                              spaceSide(Dimensions.space4.w),
                              Icon(Icons.arrow_drop_down)
                            ],
                          )),
                    ),
                    spaceSide(Dimensions.space8.w),
                    Expanded(
                      child: CustomAppCard(
                          padding: EdgeInsets.symmetric(horizontal: Dimensions.space12, vertical: Dimensions.space16),
                          onPressed: () {
                            GiftBottomSheet.categoryBottomSheet(
                              context,
                              selectedCategory: controller.selectedCategory,
                              onSelectedData: (v) {
                                controller.selectedCategoryData(v);
                              },
                            );
                          },
                          child: Row(
                            children: [
                              Expanded(
                                  child: Text(
                                controller.selectedCategory.id == -1 ? MyStrings.allCategories : controller.selectedCategory.name ?? "",
                                style: MyTextStyle.sectionSubTitle1,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              )),
                              spaceSide(Dimensions.space4.w),
                              Icon(Icons.arrow_drop_down)
                            ],
                          )),
                    ),
                    spaceSide(Dimensions.space8.w),
                    CustomAppCard(
                        onPressed: () {
                          controller.onSearch();
                        },
                        child: Icon(!controller.isSearch ? CupertinoIcons.search : CupertinoIcons.clear)),
                  ],
                ),
                spaceDown(Dimensions.space14.h),
                Visibility(
                  visible: controller.isSearch,
                  child: RoundedTextField(
                    prefixIcon: Icon(CupertinoIcons.search),
                    contentPadding: EdgeInsets.symmetric(horizontal: Dimensions.space16, vertical: Dimensions.space18),
                    readOnly: false,
                    onChanged: (value) {
                      if (_debounceTimer?.isActive ?? false) {
                        _debounceTimer?.cancel();
                      }
                      _debounceTimer = Timer(const Duration(milliseconds: 500), () {
                        controller.loadGiftCardInfo(shouldLoad: false, filterLoad: true);
                      });
                    },
                    showLabelText: false,
                    labelText: MyStrings.searchGiftCard,
                    hintText: MyStrings.searchGiftCard,
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.text,
                    controller: controller.searchController,
                    suffixIcon: controller.searchController.text.trim().isEmpty
                        ? null
                        : IconButton(
                            icon: Icon(CupertinoIcons.clear),
                            onPressed: () {
                              controller.searchController.clear();
                              controller.loadGiftCardInfo(shouldLoad: false, filterLoad: true);
                            }),
                  ).animate().fadeIn(),
                ),
                controller.allGiftCardList.isEmpty
                    ? RefreshIndicator(
                        color: MyColor.getPrimaryColor(),
                        onRefresh: () async {
                          controller.selectedCategory.id = -1;
                          controller.selectedCountry?.id = -1;
                          controller.searchController.clear();
                          controller.loadGiftCardInfo(filterLoad: true);
                        },
                        child: SingleChildScrollView(physics: const AlwaysScrollableScrollPhysics(parent: ClampingScrollPhysics()), child: NoDataWidget(text: MyStrings.noGiftCardToShow)))
                    : Expanded(
                        child: Animate(
                          target: controller.isSearch ? 1 : 0,
                          effects: [
                            MoveEffect(
                              begin: const Offset(0, 0),
                              end: const Offset(0, 20),
                              duration: 500.ms,
                              curve: Curves.easeIn,
                            ),
                          ],
                          child: RefreshIndicator(
                            color: MyColor.getPrimaryColor(),
                            onRefresh: () async {
                              controller.selectedCategory.id = -1;
                              controller.selectedCountry?.id = -1;
                              controller.searchController.clear();
                              controller.loadGiftCardInfo(filterLoad: true);
                            },
                            child: AutoHeightGridView(
                              controller: scrollController,
                              itemCount: controller.allGiftCardList.length + 2,
                              crossAxisCount: 2,
                              mainAxisSpacing: Dimensions.space15.w,
                              crossAxisSpacing: Dimensions.space15.w,
                              physics: const BouncingScrollPhysics(),
                              padding: EdgeInsets.zero,
                              shrinkWrap: true,
                              builder: (context, index) {
                                final list = controller.allGiftCardList;

                                if (index < list.length) {
                                  var item = controller.allGiftCardList[index];
                                  return CustomAppCard(
                                    onPressed: () {
                                      controller.selectGiftOnTap(item);
                                      _nextPage(goToPage: 1);
                                    },
                                    backgroundColor: item.id == controller.selectedGiftCard.id ? MyColor.getPrimaryColor().withValues(alpha: 0.1) : MyColor.getWhiteColor(),
                                    borderColor: item.id == controller.selectedGiftCard.id ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                                    padding: EdgeInsets.symmetric(
                                      vertical: Dimensions.space20.w,
                                      horizontal: Dimensions.space20.w,
                                    ),
                                    radius: Dimensions.cardExtraRadius.r,
                                    child: Column(
                                      mainAxisAlignment: MainAxisAlignment.start,
                                      crossAxisAlignment: CrossAxisAlignment.center,
                                      children: [
                                        MyNetworkImageWidget(
                                          imageUrl: item.logoUrls?.first ?? "",
                                          boxFit: BoxFit.contain,
                                          width: double.infinity,
                                          height: Dimensions.space50.h,
                                        ),
                                        spaceDown(Dimensions.space5.h),
                                        Text(
                                          item.productName ?? "",
                                          style: MyTextStyle.sectionTitle3,
                                          overflow: TextOverflow.visible,
                                          textAlign: TextAlign.center,
                                        ),
                                        spaceDown(Dimensions.space4.h),
                                        Text(
                                          controller.getGiftAmount(giftCard: item),
                                          style: MyTextStyle.caption1Style,
                                          overflow: TextOverflow.visible,
                                          textAlign: TextAlign.center,
                                        )
                                      ],
                                    ),
                                  );
                                }

                                // Show pagination loaders in last two cells
                                if (controller.hasNext()) {
                                  return BuildShimmerGridWidget();
                                }

                                // Empty placeholder if no pagination
                                return SizedBox.shrink();
                              },
                            ),
                          ),
                        ),
                      ),
              ],
            ),
          ),
        );
      },
    );
  }
}
