import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/screens/request_money_screen/controller/request_money_controller.dart';
import 'package:ovopay/core/data/models/modules/request_money/request_money_history_model.dart';

import '../../../../../core/utils/util_exporter.dart';

class SentRequestTabWidget extends StatelessWidget {
  const SentRequestTabWidget({super.key, required this.scrollController});
  final ScrollController scrollController;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<RequestMoneyController>(
      builder: (controller) {
        return Padding(
          padding: EdgeInsets.symmetric(horizontal: Dimensions.space16.w),
          child: controller.isSenderHistoryLoading
              ? TransactionHistoryShimmer()
              : (controller.senderHistoryList.isEmpty)
                  ? SingleChildScrollView(
                      child: NoDataWidget(
                        text: MyStrings.noTransactionsToShow.tr,
                      ),
                    )
                  : RefreshIndicator(
                      color: MyColor.getPrimaryColor(),
                      onRefresh: () async {
                        controller.initialHistoryData(false);
                      },
                      child: ListView.builder(
                        physics: ClampingScrollPhysics(
                          parent: AlwaysScrollableScrollPhysics(),
                        ),
                        controller: scrollController,
                        itemCount: controller.senderHistoryList.length + 1,
                        itemBuilder: (context, index) {
                          if (controller.senderHistoryList.length == index) {
                            return controller.hasNext(false) ? const CustomLoader(isPagination: true) : const SizedBox();
                          }
                          var item = controller.senderHistoryList[index];
                          bool isLastIndex = index == controller.senderHistoryList.length - 1;
                          return CustomCompanyListTileCard(
                            leading: Stack(
                              children: [
                                MyNetworkImageWidget(
                                  width: Dimensions.space40.w,
                                  height: Dimensions.space40.w,
                                  imageAlt: "${item.requestReceiver?.getFullName()}",
                                  isProfile: true,
                                  imageUrl: item.requestReceiver?.getUserImageUrl() ?? "",
                                ),
                                PositionedDirectional(
                                  bottom: 0,
                                  end: 0,
                                  child: buildStatusIcon(item.status ?? "-1"),
                                ),
                              ],
                            ),
                            showBorder: !isLastIndex,
                            imagePath: item.requestReceiver?.getUserImageUrl(),
                            title: item.requestReceiver?.getFullName() ?? "Unknown",
                            subtitle: "+${item.requestReceiver?.getUserMobileNo(withCountryCode: true) ?? "N/A"}",
                            trailingTitle: DateConverter.estimatedDateOrTime(
                              item.createdAt ?? "0",
                              formatType: DateFormatType.onlyDate,
                            ),
                            trailingSubtitle: MyUtils.getUserAmount(
                              item.amount ?? "0",
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
      backgroundColor: status == "0"
          ? MyColor.warning
          : status == "1"
              ? MyColor.success
              : MyColor.redLightColor,
      child: FittedBox(
        fit: BoxFit.scaleDown,
        child: Icon(
          status == "0"
              ? Icons.schedule
              : status == "1"
                  ? Icons.check
                  : Icons.close_rounded,
          size: Dimensions.space10.sp,
          color: MyColor.getWhiteColor(),
        ),
      ),
    );
  }

  Widget buildDetailsSectionSheet({
    required RequestMoneyHistoryDataModel item,
    required BuildContext context,
  }) {
    return GetBuilder<RequestMoneyController>(
      builder: (controller) {
        return PopScope(
          onPopInvokedWithResult: (didPop, result) {},
          child: Column(
            children: [
              BottomSheetHeaderRow(header: MyStrings.details),
              spaceDown(Dimensions.space10),
              CustomCompanyListTileCard(
                leading: MyNetworkImageWidget(
                  width: Dimensions.space40.w,
                  height: Dimensions.space40.w,
                  imageAlt: "${item.requestReceiver?.getFullName()}",
                  isProfile: true,
                  imageUrl: item.requestReceiver?.getUserImageUrl() ?? "",
                ),
                showBorder: false,
                imagePath: item.requestReceiver?.getUserImageUrl(),
                title: item.requestReceiver?.getFullName() ?? "Unknown",
                subtitle: "+${item.requestReceiver?.getUserMobileNo(withCountryCode: true) ?? "N/A"}",
              ),
              spaceDown(Dimensions.space10),
              Container(
                height: 1,
                width: double.infinity,
                color: MyColor.getBorderColor(),
                margin: const EdgeInsets.symmetric(horizontal: 10),
              ),
              spaceDown(Dimensions.space10),
              //amount and time
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
                      body: MyUtils.getUserAmount(item.amount ?? "0"),
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
              //note  and status
              Row(
                children: [
                  Expanded(
                    child: CardColumn(
                      headerTextStyle: MyTextStyle.caption1Style.copyWith(
                        color: MyColor.getBodyTextColor(),
                      ),
                      bodyTextStyle: MyTextStyle.sectionSubTitle1.copyWith(
                        fontSize: Dimensions.fontLarge,
                        color: MyColor.getHeaderTextColor(),
                      ),
                      header: MyStrings.note.tr,
                      isBodyEllipsis: false,
                      bodyMaxLine: 100,
                      body: item.note ?? "---",
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
                      body: item.status == "0"
                          ? MyStrings.pending.tr
                          : item.status == "1"
                              ? MyStrings.accepted.tr
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
            ],
          ),
        );
      },
    );
  }
}
