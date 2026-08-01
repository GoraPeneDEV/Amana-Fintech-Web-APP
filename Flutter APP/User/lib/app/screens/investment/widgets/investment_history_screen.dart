import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/investment_history_shimmer.dart';
import 'package:ovopay/app/screens/investment/controller/investment_controller.dart';
import 'package:ovopay/core/data/models/modules/investment/investment_history_response_model.dart';
import 'package:ovopay/core/data/repositories/investment/investment_repo.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';

import '../../../../../core/utils/util_exporter.dart';

class InvestmentHistoryScreen extends StatefulWidget {
  const InvestmentHistoryScreen({super.key});

  @override
  State<InvestmentHistoryScreen> createState() => _InvestmentHistoryScreenState();
}

class _InvestmentHistoryScreenState extends State<InvestmentHistoryScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<InvestmentController>().getInvestmentHistoryDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<InvestmentController>().hasNextHistory()) {
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
    return GetBuilder<InvestmentController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.investmentHistory.tr,
          body: CustomAppCard(
            child: controller.isHistoryLoading && controller.investmentHistoryPlanList.isEmpty
                ? InvestmentHistoryShimmer()
                : (controller.investmentHistoryPlanList.isEmpty)
                    ? SingleChildScrollView(
                        physics: AlwaysScrollableScrollPhysics(),
                        child: NoDataWidget(
                          text: MyStrings.noInvestmentHistory.tr,
                        ),
                      )
                    : RefreshIndicator(
                        color: MyColor.getPrimaryColor(),
                        onRefresh: () async {
                          await controller.initialHistoryData();
                        },
                        child: ListView.separated(
                          physics: ClampingScrollPhysics(
                            parent: AlwaysScrollableScrollPhysics(),
                          ),
                          controller: historyScrollController,
                          // padding: EdgeInsets.symmetric(vertical: Dimensions.space10),
                          separatorBuilder: (context, index) => spaceDown(Dimensions.space12),
                          itemCount: controller.investmentHistoryPlanList.length + 1,
                          itemBuilder: (context, index) {
                            if (controller.investmentHistoryPlanList.length == index) {
                              return controller.hasNextHistory() ? const CustomLoader(isPagination: true) : const SizedBox();
                            }

                            var item = controller.investmentHistoryPlanList[index];
                            return _buildInvestmentHistoryCard(item, context);
                          },
                        ),
                      ),
          ),
        );
      },
    );
  }

  Widget _buildInvestmentHistoryCard(InvestmentData item, BuildContext context) {
    Color statusColor = _getStatusColor(item.status.toString());
    String statusText = _getStatusText(item.status.toString());
    var investmentItem = item;
    return CustomAppCard(
      radius: Dimensions.largeRadius,
      onPressed: () {
        CustomBottomSheetPlus(
          child: SafeArea(
            child: _buildDetailBottomSheet(item: item, context: context),
          ),
        ).show(context);
      },
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              // Investment Icon
              Container(
                padding: EdgeInsets.all(Dimensions.space12),
                decoration: BoxDecoration(
                  color: MyColor.getPrimaryColor().withValues(alpha: .1),
                  borderRadius: BorderRadius.circular(Dimensions.space12),
                ),
                child: Icon(
                  Icons.trending_up_rounded,
                  color: MyColor.getPrimaryColor(),
                  size: Dimensions.space24,
                ),
              ),
              spaceSide(Dimensions.space12),

              // Investment Details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      investmentItem.plan?.name ?? MyStrings.investment.tr,
                      style: MyTextStyle.sectionSubTitle1.copyWith(
                        fontWeight: FontWeight.w600,
                        color: MyColor.getHeaderTextColor(),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    spaceDown(Dimensions.space4),
                    Text(
                      DateConverter.estimatedDateOrTime(
                        investmentItem.createdAt ?? "",
                        customFormat: "dd-MM-yyyy",
                      ),
                      style: MyTextStyle.caption2Style.copyWith(
                        color: MyColor.getBodyTextColor(),
                      ),
                    ),
                  ],
                ),
              ),

              // Amount and Status
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    "${AppConverter.formatNumber(item.investAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                    style: MyTextStyle.sectionSubTitle1.copyWith(
                      fontWeight: FontWeight.w700,
                      color: MyColor.getPrimaryColor(),
                    ),
                  ),
                  spaceDown(Dimensions.space4),
                  Container(
                    padding: EdgeInsets.symmetric(
                      horizontal: Dimensions.space8,
                      vertical: Dimensions.space4,
                    ),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: .15),
                      borderRadius: BorderRadius.circular(Dimensions.space4),
                    ),
                    child: Text(
                      statusText,
                      style: MyTextStyle.caption2Style.copyWith(
                        color: statusColor,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),

          spaceDown(Dimensions.space12),

          // Additional Info Row
          Row(
            children: [
              _buildInfoChip(
                icon: Icons.event_repeat_rounded,
                label: "${item.plan?.repeatTimes ?? "0"}x ${MyStrings.returns.tr}",
              ),
              spaceSide(Dimensions.space8),
              _buildInfoChip(
                icon: Icons.account_balance_wallet_rounded,
                label: "${AppConverter.formatNumber(investmentItem.totalInterestAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()} ${MyStrings.totalReturn.tr}",
              ),
            ],
          ),

          // Next Return Date (only for active investments)
          if (investmentItem.plan?.status == "1" && investmentItem.nextReturnAt != null) ...[
            spaceDown(Dimensions.space8),
            Container(
              padding: EdgeInsets.all(Dimensions.space8),
              decoration: BoxDecoration(
                color: MyColor.lemonadeColor.withValues(alpha: .3),
                borderRadius: BorderRadius.circular(Dimensions.space5),
              ),
              child: Row(
                children: [
                  Icon(
                    Icons.schedule_rounded,
                    size: Dimensions.space16,
                    color: MyColor.getBodyTextColor(),
                  ),
                  spaceSide(Dimensions.space5),
                  Text(
                    "${MyStrings.nextReturn.tr}: ${DateConverter.estimatedDateOrTime(
                      investmentItem.nextReturnAt ?? "",
                      customFormat: "dd-MM-yyyy",
                    )}",
                    style: MyTextStyle.caption2Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                      fontSize: Dimensions.fontExtraSmall,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildInfoChip({required IconData icon, required String label}) {
    return Flexible(
      child: Container(
        padding: EdgeInsets.symmetric(
          horizontal: Dimensions.space8,
          vertical: Dimensions.space4,
        ),
        decoration: BoxDecoration(
          color: MyColor.getScreenBgColor(),
          borderRadius: BorderRadius.circular(Dimensions.space5),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              size: Dimensions.space14,
              color: MyColor.getBodyTextColor(),
            ),
            spaceSide(Dimensions.space4),
            Flexible(
              child: Text(
                label,
                style: MyTextStyle.caption2Style.copyWith(
                  color: MyColor.getBodyTextColor(),
                  fontSize: Dimensions.fontExtraSmall,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case "0": // Active/Running
        return MyColor.greenLightColor;
      case "1":
      default:
        return MyColor.getBodyTextColor();
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case "0":
        return MyStrings.running.tr;
      case "1":
        return MyStrings.completed.tr;
      default:
        return MyStrings.unknown.tr;
    }
  }

  Widget _buildDetailBottomSheet({required InvestmentData item, required BuildContext context}) {
    Color statusColor = _getStatusColor(item.status.toString());
    String statusText = _getStatusText(item.status.toString());

    return SingleChildScrollView(
      child: Column(
        children: [
          BottomSheetHeaderRow(header: MyStrings.investmentDetails.tr),
          spaceDown(Dimensions.space20),

          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Plan Name and Transaction ID
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
                  header: MyStrings.planName.tr,
                  body: item.plan?.name ?? "---",
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.transactionId.tr,
                  body: item.trx ?? "---",
                  space: 5,
                  isCopyable: true,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),

          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Investment Amount and Interest Rate/Amount
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
                  header: MyStrings.capitalBack.tr,
                  body: item.plan?.capitalBack == "1" ? MyStrings.yes.tr : MyStrings.no.tr,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: statusColor,
                  ),
                  header: MyStrings.status.tr,
                  body: statusText,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),
          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Investment Amount and Interest Rate/Amount
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
                  header: MyStrings.investmentAmount.tr,
                  body: "${AppConverter.formatNumber(item.investAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                  isBodyEllipsis: false,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: item.plan?.interestType == "0" ? MyStrings.interestRate.tr : MyStrings.interestAmount.tr,
                  body: item.plan?.interestType == "0" ? "${AppConverter.formatNumber(item.plan?.interestAmount ?? "0")}%" : "${AppConverter.formatNumber(item.plan?.interestAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),

          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Amount Per Interest and Total Interest
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
                  header: MyStrings.perInterestAmount.tr,
                  body: "${AppConverter.formatNumber(item.perInterestAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                  isBodyEllipsis: false,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                    fontWeight: FontWeight.w700,
                  ),
                  header: MyStrings.totalInterest.tr,
                  body: "${AppConverter.formatNumber(item.totalInterestAmount ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                  isBodyEllipsis: false,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),

          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Total Amount and Total Percent Amount
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
                  header: MyStrings.totalInterestAmountGet.tr,
                  body: "${AppConverter.formatNumber(item.totalInterestAmountGet ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                  isBodyEllipsis: false,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.repeatTimes.tr,
                  body: "${item.plan?.repeatTimes ?? "0"}x",
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),

          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Repeat Times and Capital Back
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
                  header: MyStrings.totalInterestTimes.tr,
                  body: "${item.totalRepeat ?? "0"} ${(item.totalRepeat ?? "0") == "0" ? MyStrings.time.tr : MyStrings.times.tr}",
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.totalInterestGet.tr,
                  body: "${item.totalRepeatGet ?? "0"} ${(item.totalRepeatGet ?? "0") == "0" ? MyStrings.time.tr : MyStrings.times.tr}",
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),

          spaceDown(Dimensions.space20),
          _buildDivider(),
          spaceDown(Dimensions.space20),

          // Created Date and Next Return Date
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
                  header: MyStrings.lastReturn.tr,
                  body: item.lastReturnAt != null
                      ? DateConverter.estimatedDateOrTime(
                          item.lastReturnAt ?? "",
                          customFormat: "dd-MM-yyyy",
                        )
                      : "---",
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              _buildVerticalDivider(),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: item.status == "1" ? MyColor.getPrimaryColor() : MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.nextReturn.tr,
                  body: item.nextReturnAt != null
                      ? DateConverter.estimatedDateOrTime(
                          item.nextReturnAt ?? "",
                        )
                      : "---",
                  space: 5,
                  isBodyEllipsis: false,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),

          spaceDown(Dimensions.space20),
        ],
      ),
    );
  }

  Widget _buildDivider() {
    return Container(
      height: 1,
      width: double.infinity,
      color: MyColor.getBorderColor(),
    );
  }

  Widget _buildVerticalDivider() {
    return Container(
      height: Dimensions.space50,
      width: 1,
      color: MyColor.getBorderColor(),
      margin: EdgeInsets.symmetric(horizontal: Dimensions.space10),
    );
  }
}
