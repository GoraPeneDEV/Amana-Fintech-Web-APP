import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/packages/auto_height_grid_view/src/auto_height_grid_view.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/promotional_offers_list_controller.dart';
import 'package:ovopay/core/data/models/home/offers_response_model.dart';

import '../../../../../core/utils/util_exporter.dart';

class PromotionalOffersScreen extends StatefulWidget {
  const PromotionalOffersScreen({super.key});

  @override
  State<PromotionalOffersScreen> createState() => _PromotionalOffersScreenState();
}

class _PromotionalOffersScreenState extends State<PromotionalOffersScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<PromotionalOffersListController>().getPromotionalOffersDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<PromotionalOffersListController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    final controller = Get.put(PromotionalOffersListController());

    // Fetch initial data
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        controller.initialHistoryData(); // Receiver if index is 0, Sender otherwise

        // Add scroll listeners
        historyScrollController.addListener(() => scrollListener());
      }
    });
  }

  @override
  void dispose() {
    historyScrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<PromotionalOffersListController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.paymentOffers,
          actionButton: [],
          body: controller.isHistoryLoading
              ? CustomAppCard(
                  padding: EdgeInsets.symmetric(
                    horizontal: Dimensions.space16.w,
                  ),
                  child: TransactionHistoryShimmer(isGrid: true),
                )
              : (controller.promotionalOffersList.isEmpty)
                  ? SingleChildScrollView(
                      child: NoDataWidget(
                        text: MyStrings.noPromotionalOffersToShow.tr,
                      ),
                    )
                  : CustomAppCard(
                      child: RefreshIndicator(
                        color: MyColor.getPrimaryColor(),
                        onRefresh: () async {
                          controller.initialHistoryData();
                        },
                        child: AutoHeightGridView(
                          controller: historyScrollController,
                          itemCount: controller.promotionalOffersList.length,
                          crossAxisCount: 2,
                          mainAxisSpacing: Dimensions.space12.w,
                          crossAxisSpacing: Dimensions.space12.w,
                          physics: const ClampingScrollPhysics(
                            parent: AlwaysScrollableScrollPhysics(),
                          ),
                          padding: EdgeInsets.zero,
                          shrinkWrap: true,
                          builder: (context, index) {
                            if (controller.promotionalOffersList.length == index) {
                              return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                            }
                            var item = controller.promotionalOffersList[index];

                            return CustomAppCard(
                              padding: EdgeInsets.all(Dimensions.space12.w),
                              radius: Dimensions.largeRadius.r,
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.start,
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  MyNetworkImageWidget(
                                    imageUrl: item.getOfferImageUrl() ?? "",
                                    boxFit: BoxFit.contain,
                                    width: double.infinity,
                                    height: Dimensions.space100.h,
                                  ),
                                  spaceDown(Dimensions.space4),
                                  Text(
                                    item.discountType == "1" ? "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0)} ${MyStrings.discount.tr}" : "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0, showPercent: true)} ${MyStrings.off.tr}",
                                    textAlign: TextAlign.start,
                                    overflow: TextOverflow.ellipsis,
                                    style: MyTextStyle.caption1Style.copyWith(
                                      fontWeight: FontWeight.w600,
                                      color: MyColor.error,
                                    ),
                                  ),
                                  spaceDown(Dimensions.space4),
                                  Text(
                                    "${item.merchant?.getFullName()}",
                                    textAlign: TextAlign.start,
                                    overflow: TextOverflow.visible,
                                    style: MyTextStyle.caption1Style.copyWith(
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                  ),
                                ],
                              ),
                              onPressed: () {
                                CustomBottomSheetPlus(
                                  child: SafeArea(
                                    child: buildDetailsSectionSheet(
                                      item: item,
                                      context: context,
                                    ),
                                  ),
                                ).show(context);
                              },
                            );
                          },
                        ),
                      ),
                    ),
        );
      },
    );
  }

  Widget buildDetailsSectionSheet({
    required OfferModel item,
    required BuildContext context,
  }) {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          BottomSheetHeaderRow(header: MyStrings.details),
          spaceDown(Dimensions.space10),
          MyNetworkImageWidget(
            imageUrl: item.getOfferImageUrl() ?? "",
            boxFit: BoxFit.contain,
            width: double.infinity,
            height: Dimensions.space100.h * 1.5,
          ),
          spaceDown(Dimensions.space10),
          Text(
            item.discountType == "1" ? "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0)} ${MyStrings.discount.tr}" : "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0, showPercent: true)} ${MyStrings.off.tr}",
            textAlign: TextAlign.start,
            overflow: TextOverflow.ellipsis,
            style: MyTextStyle.sectionTitle.copyWith(
              fontWeight: FontWeight.w600,
              color: MyColor.error,
            ),
          ),
          spaceDown(Dimensions.space4),
          Text(
            "${item.merchant?.getFullName()}",
            textAlign: TextAlign.start,
            overflow: TextOverflow.visible,
            style: MyTextStyle.sectionTitle.copyWith(
              color: MyColor.getBodyTextColor(),
            ),
          ),
          spaceDown(Dimensions.space4),
          Text(
            item.description ?? "",
            textAlign: TextAlign.start,
            overflow: TextOverflow.visible,
            style: MyTextStyle.sectionTitle.copyWith(
              fontWeight: FontWeight.normal,
              color: MyColor.getBodyTextColor(),
            ),
          ),
          spaceDown(Dimensions.space4),
          if (item.link != null) ...[
            Align(
              alignment: AlignmentDirectional.centerEnd,
              child: CustomElevatedBtn(
                width: Dimensions.space80.w,
                height: Dimensions.space40.h,
                text: MyStrings.more,
                icon: Icon(
                  Icons.arrow_forward_ios_rounded,
                  color: MyColor.getWhiteColor(),
                ),
                iconAlignment: IconAlignment.end,
                onTap: () {
                  MyUtils.launchUrlToBrowser(item.link ?? "");
                },
              ),
            ),
          ],
        ],
      ),
    );
  }
}
