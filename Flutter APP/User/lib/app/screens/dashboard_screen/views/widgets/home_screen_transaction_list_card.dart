import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/app/screens/transaction_history/controller/transaction_history_controller.dart';
import 'package:ovopay/app/screens/transaction_history/views/widgets/custom_main_transactions_list_tile_card.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class HomeScreenTransactionMenuCard extends StatelessWidget {
  const HomeScreenTransactionMenuCard({
    super.key,
    this.onViewAllTransactionTapped,
  });
  final Function(int index)? onViewAllTransactionTapped;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeController>(
      builder: (homeController) {
        return CustomAppCard(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  HeaderText(
                    text: MyStrings.transactions.tr,
                    textStyle: MyTextStyle.sectionTitle2.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                  ),
                  Material(
                    type: MaterialType.transparency,
                    borderRadius: BorderRadius.circular(
                      Dimensions.largeRadius.r,
                    ),
                    child: InkWell(
                      borderRadius: BorderRadius.circular(
                        Dimensions.largeRadius.r,
                      ),
                      onTap: () {
                        if (onViewAllTransactionTapped != null) {
                          onViewAllTransactionTapped!(1);
                        }
                      },
                      child: Padding(
                        padding: EdgeInsets.symmetric(
                          horizontal: Dimensions.space3.w,
                          vertical: Dimensions.space2.w,
                        ),
                        child: Text(
                          MyStrings.showAll.tr,
                          style: MyTextStyle.appBarActionButtonTextStyleTitle.copyWith(decoration: TextDecoration.none),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              spaceDown(Dimensions.space8),
              (homeController.transactionHistoryList.isEmpty)
                  ? SingleChildScrollView(
                      child: NoDataWidget(
                        text: MyStrings.noTransactionsToShow.tr,
                      ),
                    )
                  : ListView.builder(
                      shrinkWrap: true,
                      physics: NeverScrollableScrollPhysics(),
                      itemCount: homeController.transactionHistoryList.length,
                      itemBuilder: (context, index) {
                        var item = homeController.transactionHistoryList[index];
                        bool isLastIndex = index == homeController.transactionHistoryList.length - 1;

                        return CustomMainTransactionListTileCard(
                          item: item,
                          title: Get.find<TransactionHistoryController>().getRemarkText("${item.remark}"),
                          showBorder: !isLastIndex,
                          balance: MyUtils.getUserAmount(item.amount ?? ""),
                          trxType: "${item.trxType}",
                          date: DateConverter.estimatedDateOrTime(
                            item.createdAt ?? "",
                            customFormat: "dd/MM/yyyy hh:mm aa",
                          ),
                          onPressed: () {},
                          remark: "${item.remark}",
                        );
                      },
                    ),
            ],
          ),
        );
      },
    );
  }
}
