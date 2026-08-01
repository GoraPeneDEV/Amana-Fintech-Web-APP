import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/core/data/models/modules/gift_card/gift_card_history_response_model.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../../../core/utils/util_exporter.dart';
import '../../../../core/data/repositories/modules/gift_card/gift_card_repo.dart';
import '../../../../core/di_service/download_service.dart';
import '../../../../environment.dart';
import '../controller/gift_controller.dart';

class GiftHistoryScreen extends StatefulWidget {
  const GiftHistoryScreen({super.key});

  @override
  State<GiftHistoryScreen> createState() => _GiftHistoryScreenState();
}

class _GiftHistoryScreenState extends State<GiftHistoryScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<GiftController>().getMobileRechargeHistoryDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<GiftController>().hasNextForHistory()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    // Initialize RequestMoneyController
    Get.put(GiftCardRepo());
    final controller = Get.put(
      GiftController(giftCardRepo: Get.find()),
    );

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
    return GetBuilder<GiftController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.giftCardHistory,
          body: CustomAppCard(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Flexible(
                  fit: FlexFit.loose,
                  child: controller.isHistoryLoading
                      ? TransactionHistoryShimmer()
                      : (controller.giftCardHistoryList.isEmpty)
                          ? SingleChildScrollView(
                              child: NoDataWidget(
                                text: MyStrings.noTransactionsToShow.tr,
                              ),
                            )
                          : RefreshIndicator(
                              color: MyColor.getPrimaryColor(),
                              onRefresh: () async {
                                controller.initialHistoryData();
                              },
                              child: ListView.builder(
                                physics: ClampingScrollPhysics(
                                  parent: AlwaysScrollableScrollPhysics(),
                                ),
                                controller: historyScrollController,
                                itemCount: controller.giftCardHistoryList.length + 1,
                                itemBuilder: (context, index) {
                                  if (controller.giftCardHistoryList.length == index) {
                                    return controller.hasNextForHistory() ? const CustomLoader(isPagination: true) : const SizedBox();
                                  }
                                  var item = controller.giftCardHistoryList[index];
                                  bool isLastIndex = index == controller.giftCardHistoryList.length - 1;
                                  return CustomCompanyListTileCard(
                                    leading: Stack(
                                      children: [
                                        MyNetworkImageWidget(
                                          width: Dimensions.space40.w,
                                          height: Dimensions.space40.w,
                                          imageAlt: item.giftCard?.logoUrls?.first ?? "",
                                          isProfile: true,
                                          imageUrl: item.giftCard?.logoUrls?.first ?? "",
                                        ),
                                      ],
                                    ),
                                    showBorder: !isLastIndex,
                                    imagePath: item.giftCard?.logoUrls?.first ?? "",
                                    title: item.giftCard?.productName ?? "Unknown",
                                    subtitle: item.recipientEmail ?? "",
                                    trailingTitle: DateConverter.estimatedDateOrTime(
                                      item.updatedAt ?? "0",
                                    ),
                                    trailingSubtitle: MyUtils.getUserAmount(
                                      item.total ?? "0",
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
              ],
            ),
          ),
        );
      },
    );
  }

  Widget buildDetailsSectionSheet({
    required GiftCardHistory item,
    required BuildContext context,
  }) {
    return GetBuilder<GiftController>(
      builder: (controller) => SingleChildScrollView(
        child: Column(
          children: [
            BottomSheetHeaderRow(header: MyStrings.details),
            spaceDown(Dimensions.space10),
            CustomCompanyListTileCard(
              leading: MyNetworkImageWidget(
                width: Dimensions.space40.w,
                height: Dimensions.space40.w,
                // imageAlt: "${item.mobileOperator?.name}",
                isProfile: true,
                imageUrl: item.giftCard?.logoUrls?.first ?? "",
              ),
              showBorder: false,
              imagePath: item.giftCard?.logoUrls?.first ?? "",
              title: item.giftCard?.productName ?? "",
              subtitle: item.recipientEmail ?? "",
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            //Number and time
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.unitPrice.tr,
                    body: "${AppConverter.formatNumber(item.unitPrice ?? "")} ${item.giftCard?.senderCurrencyCode ?? ''} (${AppConverter.formatNumber(item.amount ?? "")} ${item.giftCard?.recipientCurrencyCode ?? ''})",
                    isBodyEllipsis: false,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    isDate: true,
                    header: MyStrings.time,
                    body: DateConverter.estimatedDateOrTime(
                      item.updatedAt ?? "0",
                    ),
                    isBodyEllipsis: false,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            //amount and charge
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.subTotal.tr,
                    body: MyUtils.getUserAmount(item.subTotal ?? ""),
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.quantity.tr,
                    body: item.quantity ?? "",
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            //note  and status
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.charge.tr,
                    body: "+${MyUtils.getUserAmount(item.charge ?? "")}",
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.total,
                    body: MyUtils.getUserAmount(item.total ?? ""),
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.discount.tr,
                    body: (double.tryParse(item.discount ?? "0.00") ?? 0.00) > 0 ? "${AppConverter.formatNumber(item.discount ?? "")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}" : "N/A".tr,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.status,
                    body: item.status == AppStatus.PENDING
                        ? MyStrings.pending.tr
                        : item.status == AppStatus.APPROVE
                            ? MyStrings.success.tr
                            : MyStrings.rejected.tr,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),

            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.transactionId,
                    body: item.trx ?? "---",
                    space: 5,
                    isCopyable: true,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),

            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),

            spaceDown(Dimensions.space10),
            CustomElevatedBtn(
              isLoading: controller.isDownloadLoading,
              text: MyStrings.downloadReceipt,
              onTap: () {
                controller.isDownloadLoading = true;
                controller.update();

                DownloadService.downloadPDF(
                  url: "${UrlContainer.baseUrl}${UrlContainer.giftCardPdfDownloadEndPoint}/${item.id}",
                  fileName: "${Environment.appName}_receipt_${item.id}.pdf",
                );
                Future.delayed(const Duration(seconds: 1), () {}).then((_) {
                  controller.isDownloadLoading = false;
                  controller.update();
                });
              },
            )
          ],
        ),
      ),
    );
  }
}

Widget buildStatusIcon(String status) {
  return CustomAppCard(
    borderColor: MyColor.getWhiteColor(),
    borderWidth: Dimensions.space2,
    padding: EdgeInsets.zero,
    width: Dimensions.space15.w,
    height: Dimensions.space15.w,
    showBorder: true,
    backgroundColor: status == "1"
        ? MyColor.success
        : status == "2"
            ? MyColor.warning
            : MyColor.redLightColor,
    child: FittedBox(
      fit: BoxFit.scaleDown,
      child: Icon(
        status == "1"
            ? Icons.check
            : status == "2"
                ? Icons.schedule
                : Icons.close_rounded,
        size: Dimensions.space10.sp,
        color: MyColor.getWhiteColor(),
      ),
    ),
  );
}
