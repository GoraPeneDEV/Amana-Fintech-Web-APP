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
import 'package:ovopay/app/screens/send_money_screen/controller/send_money_controller.dart';
import 'package:ovopay/core/data/models/modules/send_money/send_money_response_model.dart';
import 'package:ovopay/core/data/repositories/modules/send_money/send_money_repo.dart';

import '../../../../../core/utils/util_exporter.dart';

class SendMoneyHistoryScreen extends StatefulWidget {
  const SendMoneyHistoryScreen({super.key});

  @override
  State<SendMoneyHistoryScreen> createState() => _SendMoneyHistoryScreenState();
}

class _SendMoneyHistoryScreenState extends State<SendMoneyHistoryScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<SendMoneyController>().getSendMoneyHistoryDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<SendMoneyController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    Get.put(SendMoneyRepo());
    final controller = Get.put(SendMoneyController(sendMoneyRepo: Get.find()));

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
    return GetBuilder<SendMoneyController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.sendMoneyHistory,
          body: CustomAppCard(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Flexible(
                  fit: FlexFit.loose,
                  child: controller.isHistoryLoading
                      ? TransactionHistoryShimmer()
                      : (controller.sendMoneyHistoryList.isEmpty)
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
                                itemCount: controller.sendMoneyHistoryList.length + 1,
                                itemBuilder: (context, index) {
                                  if (controller.sendMoneyHistoryList.length == index) {
                                    return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                                  }
                                  var item = controller.sendMoneyHistoryList[index];
                                  bool isLastIndex = index == controller.sendMoneyHistoryList.length - 1;
                                  return CustomCompanyListTileCard(
                                    leading: MyNetworkImageWidget(
                                      width: Dimensions.space40.w,
                                      height: Dimensions.space40.w,
                                      isProfile: true,
                                      imageUrl: item.receiverUser?.getUserImageUrl() ?? "",
                                      imageAlt: item.receiverUser?.getFullName(),
                                    ),
                                    imagePath: item.receiverUser?.getUserImageUrl(),
                                    title: item.receiverUser?.getFullName(),
                                    subtitle: MyStrings.sendMOneyMessage.tr.rKv({
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

  Widget buildDetailsSectionSheet({
    required LatestSendMoneyHistory item,
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
              imageAlt: "${item.receiverUser?.getFullName()}",
              isProfile: true,
              imageUrl: item.receiverUser?.getUserImageUrl() ?? "",
            ),
            showBorder: false,
            imagePath: item.receiverUser?.getUserImageUrl() ?? "",
            title: "${item.receiverUser?.getFullName()}",
            subtitle: "+${item.receiverUser?.getUserMobileNo(withCountryCode: true) ?? ""}",
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
                  header: MyStrings.phoneNumber.tr,
                  body: "+${item.receiverUser?.getUserMobileNo(withCountryCode: true) ?? ""}",
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
                  body: MyUtils.getUserAmount(item.totalAmount ?? ""),
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
