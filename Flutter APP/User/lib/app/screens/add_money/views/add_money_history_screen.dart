import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/screens/add_money/controller/add_money_controller.dart';
import 'package:ovopay/core/data/models/deposit/deposit_history_response_model.dart';
import 'package:ovopay/core/data/repositories/modules/donation/donation_repo.dart';

import '../../../../../core/utils/util_exporter.dart';

class AddMoneyHistoryScreen extends StatefulWidget {
  const AddMoneyHistoryScreen({super.key});

  @override
  State<AddMoneyHistoryScreen> createState() => _AddMoneyHistoryScreenState();
}

class _AddMoneyHistoryScreenState extends State<AddMoneyHistoryScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<AddMoneyController>().getDonationHistoryDataList(forceLoad: false);
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<AddMoneyController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    // Initialize RequestMoneyController
    Get.put(DonationRepo());
    final controller = Get.put(AddMoneyController(depositRepo: Get.find()));

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
    return GetBuilder<AddMoneyController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.addMoneyHistory,
          body: CustomAppCard(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Flexible(
                  fit: FlexFit.loose,
                  child: controller.isHistoryLoading
                      ? TransactionHistoryShimmer()
                      : controller.depositHistoryList.isEmpty
                          ? NoDataWidget(text: MyStrings.noDepositToShow.tr)
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
                                itemCount: controller.depositHistoryList.length + 1,
                                itemBuilder: (context, index) {
                                  if (controller.depositHistoryList.length == index) {
                                    return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                                  }
                                  var item = controller.depositHistoryList[index];
                                  bool isLastIndex = index == controller.depositHistoryList.length - 1;
                                  return CustomCompanyListTileCard(
                                    leading: Stack(
                                      children: [
                                        MyNetworkImageWidget(
                                          boxFit: BoxFit.contain,
                                          width: Dimensions.space40.w,
                                          height: Dimensions.space40.w,
                                          imageAlt: "${item.gateway?.name}",
                                          isProfile: true,
                                          imageUrl: item.gateway?.getImageUrl() ?? "",
                                        ),
                                        PositionedDirectional(
                                          bottom: 0,
                                          end: 0,
                                          child: buildStatusIcon(
                                            item.status ?? "-1",
                                          ),
                                        ),
                                      ],
                                    ),
                                    imagePath: item.gateway?.getImageUrl(),
                                    title: item.gateway?.name ?? "",
                                    subtitle: MyStrings.addMoneyMessage.tr.rKv({
                                      "amount": MyUtils.getUserAmount(
                                        item.amount ?? "",
                                      ),
                                      "date": DateConverter.estimatedDateOrTime(
                                        item.createdAt ?? "",
                                        customFormat: "dd-MM-yyyy",
                                      ),
                                    }),
                                    titleStyle: MyTextStyle.sectionSubTitle1.copyWith(fontWeight: FontWeight.w600),
                                    subtitleStyle: MyTextStyle.caption2Style.copyWith(
                                      color: MyColor.getBodyTextColor(),
                                    ),
                                    showBorder: !isLastIndex,
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

  Widget buildDetailsSectionSheet({
    required DepositHistoryListModel item,
    required BuildContext context,
  }) {
    return SingleChildScrollView(
      child: Column(
        children: [
          BottomSheetHeaderRow(header: MyStrings.details),
          spaceDown(Dimensions.space10),
          CustomCompanyListTileCard(
            leading: MyNetworkImageWidget(
              width: Dimensions.space40.w,
              height: Dimensions.space40.w,
              imageAlt: "${item.gateway?.name}",
              isProfile: true,
              imageUrl: item.gateway?.getImageUrl() ?? "",
            ),
            showBorder: false,
            imagePath: item.gateway?.getImageUrl() ?? "",
            title: "${item.gateway?.name}",
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
                  header: MyStrings.status,
                  body: item.status == "1"
                      ? MyStrings.accepted.tr
                      : item.status == "2"
                          ? MyStrings.pending.tr
                          : MyStrings.rejected.tr,
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
                  header: MyStrings.time,
                  body: DateConverter.estimatedDateOrTime(
                    item.createdAt ?? "0",
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
                  header: MyStrings.amount.tr,
                  body: MyUtils.getUserAmount(item.amount ?? ""),
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
                  header: MyStrings.charge,
                  body: MyUtils.getUserAmount(item.charge ?? ""),
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
                  header: MyStrings.total.tr,
                  body: MyUtils.getUserAmount(item.finalAmount ?? ""),
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
                  header: MyStrings.transactionId,
                  body: item.trx ?? "---",
                  space: 5,
                  isCopyable: true,
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
        ],
      ),
    );
  }
}
