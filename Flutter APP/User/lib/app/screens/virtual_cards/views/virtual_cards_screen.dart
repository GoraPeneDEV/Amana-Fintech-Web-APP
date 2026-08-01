import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/card_screen_shimmer.dart';
import 'package:ovopay/app/screens/virtual_cards/controller/virtual_cards_controller.dart';
import 'package:ovopay/app/screens/virtual_cards/views/widgets/cards_screen_transaction_list_card.dart';
import 'package:ovopay/app/screens/virtual_cards/views/widgets/vc_cards_list_card.dart';
import 'package:ovopay/core/data/repositories/modules/virtual_cards/virtual_cards_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class VirtualCardsScreen extends StatefulWidget {
  const VirtualCardsScreen({super.key});
  @override
  State<VirtualCardsScreen> createState() => _VirtualCardsScreenState();
}

class _VirtualCardsScreenState extends State<VirtualCardsScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<VirtualCardsController>().getVirtualCardHistoryDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<VirtualCardsController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    Get.put(VirtualCardsRepo());
    final controller = Get.put(
      VirtualCardsController(virtualCardsRepo: Get.find()),
    );

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        await Future.wait([
          controller.initController(),
          controller.initialHistoryData(),
        ]);
        // Add scroll listeners
        historyScrollController.addListener(() => scrollListener());
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VirtualCardsController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.cards,
          body: controller.isPageLoading
              ? CardScreenShimmer()
              : (controller.myCardsList.isEmpty)
                  ? SingleChildScrollView(
                      child: NoDataWidget(
                        text: MyStrings.virtualCardsNoMsg,
                        customWidget: Padding(
                          padding: const EdgeInsets.only(top: Dimensions.space15),
                          child: CustomElevatedBtn(
                            elevation: 0,
                            radius: Dimensions.largeRadius.r,
                            text: MyStrings.createMyFirstCard.tr,
                            icon: Padding(
                              padding: EdgeInsets.symmetric(
                                vertical: Dimensions.space10.h,
                              ),
                              child: MyAssetImageWidget(
                                width: Dimensions.space40.w,
                                height: Dimensions.space40.w,
                                boxFit: BoxFit.contain,
                                assetPath: MyIcons.cardAddIcon,
                                color: MyColor.getWhiteColor(),
                                isSvg: true,
                              ),
                            ),
                            onTap: () async {
                              Get.toNamed(RouteHelper.createVccCardScreen);
                            },
                          ),
                        ),
                      ),
                    )
                  : RefreshIndicator(
                      onRefresh: () async {
                        controller.initController();
                        controller.initialHistoryData();
                      },
                      child: NestedScrollView(
                        headerSliverBuilder: (
                          BuildContext context,
                          bool innerBoxIsScrolled,
                        ) {
                          return <Widget>[
                            SliverToBoxAdapter(
                              child: Column(
                                children: [
                                  VcCardsListWidget(),
                                  spaceDown(Dimensions.space16),
                                  CustomElevatedBtn(
                                    elevation: 0,
                                    radius: Dimensions.largeRadius.r,
                                    bgColor: MyColor.getWhiteColor(),
                                    textColor: MyColor.getPrimaryColor(),
                                    text: MyStrings.createNewVirtualCard.tr,
                                    borderColor: MyColor.getPrimaryColor(),
                                    shadowColor: MyColor.getWhiteColor(),
                                    icon: Padding(
                                      padding: EdgeInsets.symmetric(
                                        vertical: Dimensions.space10.h,
                                      ),
                                      child: MyAssetImageWidget(
                                        width: Dimensions.space40.w,
                                        height: Dimensions.space40.w,
                                        boxFit: BoxFit.contain,
                                        assetPath: MyIcons.cardAddIcon,
                                        color: MyColor.getPrimaryColor(),
                                        isSvg: true,
                                      ),
                                    ),
                                    onTap: () async {
                                      Get.toNamed(
                                        RouteHelper.createVccCardScreen,
                                      );
                                    },
                                  ),
                                  spaceDown(Dimensions.space16),
                                ],
                              ),
                            ),
                          ];
                        },
                        body: CardScreenTransactionMenuCard(
                          historyScrollController: historyScrollController,
                        ),
                      ),
                    ),
        );
      },
    );
  }
}
