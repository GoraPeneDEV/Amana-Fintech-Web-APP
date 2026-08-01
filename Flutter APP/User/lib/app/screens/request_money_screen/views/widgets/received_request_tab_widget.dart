import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/screens/request_money_screen/controller/request_money_controller.dart';
import 'package:ovopay/core/data/models/modules/request_money/request_money_history_model.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';

class ReceivedRequestTabWidget extends StatelessWidget {
  const ReceivedRequestTabWidget({super.key, required this.scrollController});
  final ScrollController scrollController;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<RequestMoneyController>(
      builder: (controller) {
        return Padding(
          padding: EdgeInsets.symmetric(horizontal: Dimensions.space16.w),
          child: controller.isReceiverHistoryLoading
              ? TransactionHistoryShimmer()
              : (controller.receiverHistoryList.isEmpty)
                  ? SingleChildScrollView(
                      child: NoDataWidget(
                        text: MyStrings.noTransactionsToShow.tr,
                      ),
                    )
                  : RefreshIndicator(
                      color: MyColor.getPrimaryColor(),
                      onRefresh: () async {
                        controller.initialHistoryData(true);
                      },
                      child: ListView.builder(
                        physics: ClampingScrollPhysics(
                          parent: AlwaysScrollableScrollPhysics(),
                        ),
                        controller: scrollController,
                        itemCount: controller.receiverHistoryList.length + 1,
                        itemBuilder: (context, index) {
                          if (controller.receiverHistoryList.length == index) {
                            return controller.hasNext(true) ? const CustomLoader(isPagination: true) : const SizedBox();
                          }
                          var item = controller.receiverHistoryList[index];
                          bool isLastIndex = index == controller.receiverHistoryList.length - 1;
                          return CustomCompanyListTileCard(
                            leading: Stack(
                              children: [
                                MyNetworkImageWidget(
                                  width: Dimensions.space40.w,
                                  height: Dimensions.space40.w,
                                  imageAlt: "${item.requestSender?.getFullName()}",
                                  isProfile: true,
                                  imageUrl: item.requestSender?.getUserImageUrl() ?? "",
                                ),
                                PositionedDirectional(
                                  bottom: 0,
                                  end: 0,
                                  child: buildStatusIcon(item.status ?? "-1"),
                                ),
                              ],
                            ),
                            showBorder: !isLastIndex,
                            imagePath: item.requestSender?.getUserImageUrl(),
                            title: item.requestSender?.getFullName() ?? "Unknown",
                            subtitle: "+${item.requestSender?.getUserMobileNo(withCountryCode: true) ?? "N/A"}",
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
          child: SingleChildScrollView(
            child: Column(
              children: [
                BottomSheetHeaderRow(header: MyStrings.details),
                spaceDown(Dimensions.space10),
                CustomCompanyListTileCard(
                  leading: MyNetworkImageWidget(
                    width: Dimensions.space40.w,
                    height: Dimensions.space40.w,
                    imageAlt: "${item.requestSender?.getFullName()}",
                    isProfile: true,
                    imageUrl: item.requestSender?.getUserImageUrl() ?? "",
                  ),
                  showBorder: false,
                  imagePath: item.requestSender?.getUserImageUrl(),
                  title: item.requestSender?.getFullName() ?? "Unknown",
                  subtitle: "+${item.requestSender?.getUserMobileNo(withCountryCode: true) ?? "N/A"}",
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

                if (item.status == "0") ...[
                  spaceDown(Dimensions.space40),
                  Row(
                    children: [
                      Expanded(
                        child: CustomElevatedBtn(
                          // isLoading: controller.isSubmitRejectLoading,
                          radius: Dimensions.largeRadius.r,
                          textColor: MyColor.getDarkColor(),
                          bgColor: MyColor.getWhiteColor(),
                          borderColor: MyColor.getBorderColor(),
                          text: MyStrings.reject,
                          onTap: () {
                            AppDialogs.confirmDialogForAll(
                              subTitle: MyStrings.areYouSureWantToRejectThisTicket,
                              context,
                              isConfirmLoading: controller.isSubmitRejectLoading,
                              onConfirmTap: () {
                                controller.rejectRequestMoneyProcess(
                                  item: item,
                                  onSuccessCallback: (value) {
                                    Get.back();
                                    AppDialogs.globalAppDialogForAll(
                                      context,
                                      title: MyStrings.requestMoney.tr,
                                      subTitle: value.message?.first ?? '',
                                      onTap: () {
                                        Get.back();
                                      },
                                    );
                                  },
                                );
                              },
                            );
                            // controller.changeButtonType("reject");
                          },
                        ),
                      ),
                      spaceSide(Dimensions.space15),
                      Expanded(
                        child: CustomElevatedBtn(
                          radius: Dimensions.largeRadius.r,
                          bgColor: MyColor.getPrimaryColor(),
                          text: MyStrings.approved,
                          onTap: () {
                            controller.selectRequestMoneyUserAndBalance(
                              requestMoneyHistoryDataModel: item,
                            );
                            Get.toNamed(
                              RouteHelper.requestMoneyApproveScreen,
                            )?.then((v) {
                              Get.back();
                            });
                          },
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        );
      },
    );
  }
}
